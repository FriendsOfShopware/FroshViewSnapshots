<?php

class Shopware_Controllers_Frontend_Snapshots extends \Enlight_Controller_Action
{
    public function indexAction()
    {
        $this->forward('load');
    }

    /**
     * @throws Exception
     */
    public function loadAction()
    {
        $sessionID = $this->Request()->getParam('session');
        $step = (int) $this->Request()->getParam('step', 1);

        if (empty($sessionID)) {
            throw new \Exception(
                'A valid session ID must be provided'
            );
        }

        $sqlParams = [
            ':sessionID' => $sessionID,
            ':step' => $step,
        ];

        $snapshot = $this->get('dbal_connection')->fetchAssoc(
            'SELECT * FROM `view_snapshots` WHERE `sessionID` = :sessionID AND `step` = :step',
            $sqlParams
        );

        if (empty($snapshot)) {
            throw new \Exception(
                sprintf('No snapshot found by session ID %s and step %d', $sessionID, $step)
            );
        }

        $nextStep = (int) $this->get('dbal_connection')->fetchColumn(
            'SELECT MIN(`step`) as step FROM `view_snapshots` WHERE `sessionID` = :sessionID AND `step` > :step LIMIT 1',
            $sqlParams
        );

        $prevStep = (int) $this->get('dbal_connection')->fetchColumn(
            'SELECT MAX(`step`) as step FROM `view_snapshots` WHERE `sessionID` = :sessionID AND `step` < :step LIMIT 1',
            $sqlParams
        );

        $params = json_decode($snapshot['params'], true);

        $this->Request()->setParams($params);

        $this->Request()->setControllerName($params['__controller']);
        $this->Request()->setActionName($params['__action']);

        $this->View()->loadTemplate($snapshot['template']);
        $this->View()->assign(unserialize($snapshot['variables']));

        $this->View()->assign(
            [
                'snapshotStep' => $step,
                'snapshotNextStep' => $nextStep,
                'snapshotPrevStep' => $prevStep,
                'snapshotSessionID' => $sessionID,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function startRecordingAction()
    {
        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $this->get('session')->offsetSet('isSessionRecorded', true);

        $this->Response()->setBody(json_encode(['success' => true]));
    }

    /**
     * @throws \Exception
     */
    public function stopRecordingAction()
    {
        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $this->get('session')->offsetSet('isSessionRecorded', false);

        $this->Response()->setBody(json_encode(['success' => true]));
    }
}
