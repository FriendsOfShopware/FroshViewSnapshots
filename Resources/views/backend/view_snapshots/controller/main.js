Ext.define('Shopware.apps.ViewSnapshots.controller.Main', {

    extend: 'Ext.app.Controller',

    mainWindow: null,

    init: function() {

        var me = this;
        me.getStore('Snapshot').load({
            scope: this,
            callback: function() {
                me.mainWindow = me.getView('Window').create({
                    store: me.getStore('Snapshot')
                });
            }
        });

        me.callParent(arguments);

    }
   
});
 
