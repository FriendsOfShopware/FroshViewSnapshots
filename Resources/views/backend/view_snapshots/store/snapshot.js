Ext.define('Shopware.apps.ViewSnapshots.store.Snapshot', {
    extend: 'Ext.data.Store',
    remoteFilter: true,
    autoLoad : false,
    model : 'Shopware.apps.ViewSnapshots.model.Snapshot',
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url controller="ViewSnapshots" action="list"}',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
