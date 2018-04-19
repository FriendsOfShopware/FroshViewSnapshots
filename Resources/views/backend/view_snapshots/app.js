Ext.define('Shopware.apps.ViewSnapshots', {
    extend:'Enlight.app.SubApplication',
    name:'Shopware.apps.ViewSnapshots',
    bulkLoad: true,
    loadPath: '{url action=load}',
    controllers: ['Main'],
    models: [ 'Snapshot' ],
    views: [ 'Window', 'Grid' ],
    stores: [ 'Snapshot' ],

    /** Main Function
     * @private
     * @return [object] mainWindow - the main application window based on Enlight.app.Window
     */
    launch: function() {
        var me = this;
        var mainController = me.getController('Main');

        return mainController.mainWindow;
    }
});