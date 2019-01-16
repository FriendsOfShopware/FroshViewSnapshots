Ext.define('Shopware.apps.ViewSnapshots.view.Grid', {
    extend:'Ext.grid.Panel',
    border: false,
    alias:'widget.view-snapshot-window',
    region:'center',
    autoScroll:true,
    initComponent:function () {
        var me = this;
        me.columns = me.getColumns();
        me.pagingbar = me.getPagingBar();
        me.dockedItems = [
                me.pagingbar
            ];
        me.callParent(arguments);
    },
    getColumns:function () {
        var me = this;

        return [
            {
                header: 'Session ID',
                flex: 1,
                dataIndex: 'sessionID'
            },
            {
                header: 'Template',
                flex: 1,
                dataIndex: 'template'
            },
            {
                header: 'URI',
                flex: 1,
                dataIndex: 'requestURI'
            },
            {
                header: 'Step',
                dataIndex: 'step',
                width: 40
            },
            {
                xtype: 'actioncolumn',
                width: 60,
                items: me.getActionColumnItems()
            }
        ];
    },
    getActionColumnItems: function () {
        var me = this;

        return [
                {
                    iconCls: 'x-action-col-icon sprite-globe--arrow',
                    tooltip: 'Open',
                    handler: function (view, rowIndex) {
                        var store = view.getStore(),
                            record = store.getAt(rowIndex);

                        window.open(record.get('url'), '_blank');
                    }
                },
                {
                    iconCls: 'x-action-col-icon sprite-minus-circle-frame',
                    tooltip: 'Delete',
                    handler: function (view, rowIndex, colIndex, item) {
                        me.fireEvent('delete', view, rowIndex, colIndex, item);
                    }
                }
            ];
    },
    getPagingBar: function () {
        var me = this;

        return Ext.create('Ext.toolbar.Paging', {
            store: me.store,
            dock: 'bottom',
            displayInfo: true
        });
    }
});
