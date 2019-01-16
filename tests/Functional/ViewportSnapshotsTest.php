<?php

class ViewportSnapshotsTest extends Enlight_Components_Test_Controller_TestCase
{
    public function setUp()
    {
        parent::setUp();

        Shopware()->Template()->disableSecurity();
    }

    public function testSnapshotRecording()
    {
        Shopware()->Session()->offsetSet('isSessionRecorded', true);

        $this->dispatch('/');

        $sql = 'SELECT * FROM view_snapshots WHERE sessionID = ? AND requestURI = ?';
        $snapshot = Shopware()->Db()->fetchRow($sql, [
            Shopware()->Session()->get('sessionId'),
            '/',
        ]);

        $variables = unserialize($snapshot['variables']);

        $this->assertTrue(is_array($variables) && !empty($variables));
    }

    public function testSnapshotReplay()
    {
        $this->dispatch('/snapshots/load/session/' . Shopware()->Session()->get('sessionId'));

        $this->assertTrue(strpos($this->Response()->getBody(), 'is--ctl-index') !== false);
    }
}