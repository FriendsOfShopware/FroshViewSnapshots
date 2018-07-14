<?php

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
                    'requestURI'
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
}
