Ext.define('Shopware.apps.ViewSnapshots.model.Snapshot', {
    extend : 'Ext.data.Model', 
    fields : [ 'sessionID', 'template', 'step', 'url', 'requestURI' ],
    proxy: {
        type : 'ajax',
        reader : {
            type : 'json',
            root : 'data',
            totalProperty: 'totalCount'
        }
    }
});
