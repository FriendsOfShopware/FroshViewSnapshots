<?php

use FroshViewSnapshots\Services\Diff;
use Shopware\Components\Api\Exception\ParameterMissingException;

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
     * @throws ParameterMissingException
     * @throws Exception
     */
    public function diffAction()
    {
        $sessionFrom = $this->Request()->getParam('sessionFrom');
        $stepFrom = $this->Request()->getParam('stepFrom');
        $sessionTo = $this->Request()->getParam('sessionTo', $sessionFrom);
        $stepTo = $this->Request()->getParam('stepTo');

        if (empty($sessionFrom)) {
            throw new ParameterMissingException('sessionFrom is missing');
        }

        if (empty($stepFrom)) {
            throw new ParameterMissingException('stepFrom is missing');
        }

        if (empty($sessionTo)) {
            throw new ParameterMissingException('sessionTo is missing');
        }

        if (empty($stepTo)) {
            throw new ParameterMissingException('stepTo is missing');
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
                'step' => $differ->diffPlain($dataFrom['step'], $dataTo['step'])->renderDiffToHTML(),
                'variables' => $differ->diffSerialized($dataFrom['variables'], $dataTo['variables'])->renderDiffToHTML(),
                'params' => $differ->diffJson($dataFrom['params'], $dataTo['params'])->renderDiffToHTML(),
            ],
        ]);
    }

    /**
     * @throws Exception
     */
    protected function getSnapshotStep(string $sessionFrom, int $stepFrom): array
    {
        $qb = $this->getModelManager()->getDBALQueryBuilder();

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
}
