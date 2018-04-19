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
                header: 'Step',
                dataIndex: 'step',
                width: 40
            },
            {
                xtype: 'actioncolumn',
                width: 30,
                items: me.getActionColumnItems()
            }
        ];
    },
    getActionColumnItems: function () {
        return [
                {
                    iconCls: 'x-action-col-icon sprite-globe--arrow',
                    tooltip: 'Open',
                    handler: function (view, rowIndex) {
                        var store = view.getStore(),
                            record = store.getAt(rowIndex);

                        window.open(record.get('url'), '_blank');
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
