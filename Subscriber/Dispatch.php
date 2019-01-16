<?php

namespace FroshViewSnapshots\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;

/**
 * Class Dispatch
 */
class Dispatch implements SubscriberInterface
{
    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Dispatch constructor.
     *
     * @param \Enlight_Components_Session_Namespace $session
     * @param Connection                            $connection
     */
    public function __construct(
        \Enlight_Components_Session_Namespace $session,
        Connection $connection
    ) {
        $this->session = $session;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchSecureFrontend',
        ];
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchSecureFrontend(\Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $request = $args->getSubject()->Request();
        $params = $request->getParams();
        $sessionID = $this->session->get('sessionId');

        $isSessionRecorded = strtolower($request->getControllerName()) !== 'snapshots' ?
            $this->session->get('isSessionRecorded') :
            false;

        $snapshotSessionID = $view->getAssign('snapshotSessionID');

        $view->assign(
            [
                'snapshotSessionID' => $snapshotSessionID ?: $sessionID,
                'isSessionRecorded' => $isSessionRecorded,
            ]
        );

        if (
            $snapshotSessionID ||
            !$isSessionRecorded ||
            $request->isXmlHttpRequest()
        ) {
            return;
        }

        $template = $view->Template()->template_resource;

        $variables = $view->getAssign();

        array_walk_recursive($variables, function (&$value) {
            if (is_object($value)) {
                try {
                    // workaround for PDOException when trying to serialize PDO instances
                    serialize($value);
                } catch (\Throwable $e) {
                    // as we only need a snapshot for the view, remove the PDO instance
                    $value = null;
                }
            }
        });

        $variables = serialize($variables);

        $params['__controller'] = $request->getControllerName();
        $params['__action'] = $request->getActionName();
        $params = json_encode($params);

        $step = (int) $this->connection->fetchColumn(
            'SELECT MAX(`step`) FROM `view_snapshots` WHERE `sessionID` = :sessionID',
            ['sessionID' => $sessionID]
        );
        $step++;

        $this->connection->insert(
            'view_snapshots',
            [
                'sessionID' => $sessionID,
                'template' => $template,
                'variables' => $variables,
                'params' => $params,
                'step' => $step,
                'requestURI' => $request->getPathInfo(),
            ]
        );
    }
}
