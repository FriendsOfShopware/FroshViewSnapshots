Ext.define('Shopware.apps.ViewSnapshots.view.Window', {
    extend: 'Enlight.app.Window',
    title: 'View Snapshots',
    alias: 'widget.view-snapshot-window',
    border: false,
    autoShow: true,
    height: 650,
    width: 925,
    layout: 'fit',
 
    initComponent: function() {
        var me = this;
        me.items = [
            {
                xtype: 'view-snapshot-window',
                store: me.store,
                flex: 1,
                selModel: new Ext.selection.CheckboxModel({
                    checkOnly: true
                })
            }
        ];
    
        me.callParent(arguments);
    }

});
 
