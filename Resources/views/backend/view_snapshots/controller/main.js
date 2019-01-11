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

        me.control({
            'view-snapshot-window': {
                'select': me.onGridSelect,
                'delete': me.onSnapshotDelete
            }
        });

    },

    onGridSelect: function(selectionModel, rec) {
        var selection = selectionModel.getSelection();

        if (selection.length > 2) {
            selectionModel.deselectAll();
            selectionModel.select(rec);

            return;
        }

        if (selection.length === 2) {
            var me = this,
                firstRecord = selection[0],
                secondRecord = selection[1],
                loadMask = new Ext.LoadMask(me.mainWindow);

            loadMask.show();

            Ext.Ajax.request({
                url: '{url action="diff"}',
                method: 'GET',
                params: {
                    sessionFrom: firstRecord.get('sessionID'),
                    stepFrom: firstRecord.get('step'),
                    sessionTo: secondRecord.get('sessionID'),
                    stepTo: secondRecord.get('step')
                },
                success: function(response){
                    var responseObj = Ext.JSON.decode(response.responseText);

                    Ext.create('Ext.window.Window', {
                        title: 'Diff',
                        height: 600,
                        width: 800,
                        modal: true,
                        layout: {
                            type: 'vbox',
                            align: 'stretch',
                            pack : 'start'
                        },
                        items: [{
                            xtype: 'panel',
                            title: 'Session ID',
                            html: '{literal}<style>ins{color:green;background:#dfd;text-decoration:none;}del{color:red;background:#fdd;text-decoration:none;}</style>{/literal}' +
                                responseObj.data.sessionID
                        }, {
                            xtype: 'panel',
                            title: 'Step',
                            html: responseObj.data.step
                        }, {
                            xtype: 'panel',
                            title: 'Template',
                            html: responseObj.data.template
                        }, {
                            xtype: 'panel',
                            title: 'URI',
                            html: responseObj.data.requestURI
                        }, {
                            xtype: 'panel',
                            title: 'Params',
                            flex: 1,
                            autoScroll: true,
                            collapsible: true,
                            html: '<pre>' +
                                responseObj.data.params +
                                '</pre>'
                        }, {
                            xtype: 'panel',
                            title: 'Variables',
                            flex: 2,
                            autoScroll: true,
                            html: '<pre>' +
                                responseObj.data.variables +
                                '</pre>'
                        }]
                    }).show();

                    loadMask.hide();
                },
                failure: function(response){
                    console.log(response);

                    loadMask.hide();
                }
            });
        }
    },

    onSnapshotDelete: function (view, rowIndex) {
        var me = this,
            store = me.getStore('Snapshot');

        me.record = store.getAt(rowIndex);

        if (me.record instanceof Ext.data.Model && me.record.get('id') > 0) {
            Ext.MessageBox.confirm('Delete?', 'Are you sure you want to delete the snapshot?', function (response) {
                if (response !== 'yes') {
                    return;
                }
                me.record.destroy({
                    callback: function() {
                        store.load();
                    }
                });
            });
        }
    }
   
});
 
