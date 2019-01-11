<?php

use FroshViewSnapshots\Services\Diff;

/**
 * Class Shopware_Controllers_Backend_ViewSnapshots
 */
class Shopware_Controllers_Backend_ViewSnapshots extends Shopware_Controllers_Backend_ExtJs
{
    public function indexAction()
    {
        $this->View()->loadTemplate('backend/view_snapshots/app.js');
    }

    /**
     * @throws \Exception
     */
    public function listAction()
    {
        $limit = (int) $this->Request()->getParam('limit', 20);
        $offset = (int) $this->Request()->getParam('start', 0);

        $qb = $this->getModelManager()->getDBALQueryBuilder();

        $qb->select(
                [
                    'SQL_CALC_FOUND_ROWS id',
                    'sessionID',
                    'template',
                    'step',
                    'requestURI',
                ]
            )
            ->from('view_snapshots')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('id', 'desc')
            ->addOrderBy('step', 'desc')
        ;

        $data = $qb->execute()->fetchAll();

        foreach ($data as &$row) {
            $row['url'] = $this->get('router')->assemble(
                [
                    'module' => 'frontend',
                    'controller' => 'snapshots',
                    'action' => 'load',
                    'session' => $row['sessionID'],
                    'step' => $row['step'],
                ]
            );
        }

        $total = (int) $this->container->get('dbal_connection')->fetchColumn('SELECT FOUND_ROWS()');

        $this->View()->assign(
            ['success' => true, 'data' => $data, 'total' => $total]
        );
    }

    /**
     * @throws Enlight_Controller_Exception
     * @throws Exception
     */
    public function diffAction()
    {
        $sessionFrom = $this->Request()->getParam('sessionFrom');
        $stepFrom = $this->Request()->getParam('stepFrom');
        $sessionTo = $this->Request()->getParam('sessionTo', $sessionFrom);
        $stepTo = $this->Request()->getParam('stepTo');

        if (empty($sessionFrom)) {
            throw new Enlight_Controller_Exception('sessionFrom is missing');
        }

        if (empty($stepFrom)) {
            throw new Enlight_Controller_Exception('stepFrom is missing');
        }

        if (empty($stepTo)) {
            throw new Enlight_Controller_Exception('stepTo is missing');
        }

        /** @var Diff $differ */
        $differ = $this->get('frosh_view_snapshots.services.diff');
        $dataFrom = $this->getSnapshotStep($sessionFrom, $stepFrom);
        $dataTo = $this->getSnapshotStep($sessionTo, $stepTo);

        $this->View()->assign([
            'success' => true,
            'data' => [
                'sessionID' => $differ->diffPlain($dataFrom['sessionID'], $dataTo['sessionID'])->renderDiffToHTML(),
                'template' => $differ->diffPlain($dataFrom['template'], $dataTo['template'])->renderDiffToHTML(),
                'requestURI' => $differ->diffPlain($dataFrom['requestURI'], $dataTo['requestURI'])->renderDiffToHTML(),
                'step' => $differ->diffPlain($dataFrom['step'], $dataTo['step'])->renderDiffToHTML(),
                'variables' => $differ->diffSerialized($dataFrom['variables'], $dataTo['variables'])->renderDiffToHTML(),
                'params' => $differ->diffJson($dataFrom['params'], $dataTo['params'])->renderDiffToHTML(),
            ],
        ]);
    }

    /**
     * @param string $sessionFrom
     * @param int    $stepFrom
     *
     * @throws Exception
     *
     * @return array
     */
    protected function getSnapshotStep($sessionFrom, $stepFrom)
    {
        $qb = $this->container->get('dbal_connection')->createQueryBuilder();

        $qb->select(['*'])
            ->from('view_snapshots')
            ->where(
                $qb->expr()->eq('sessionID', ':sessionId'),
                $qb->expr()->eq('step', ':step')
            )
            ->setParameter('sessionId', $sessionFrom)
            ->setParameter('step', $stepFrom)
        ;

        return $qb->execute()->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @throws \Exception
     */
    public function deleteAction()
    {
        $id = (int) $this->Request()->get('id');

        $this->container->get('dbal_connection')->delete(
            'view_snapshots',
            [
                'id' => $id,
            ]
        );

        $this->View()->assign(
            ['success' => true]
        );
    }
}
