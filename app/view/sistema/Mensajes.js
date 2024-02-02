Ext.define('SEMTI.view.sistema.Mensajes', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.mensajes',
    id: 'mensajes',
    requires: ['Ext.grid.*', 'Ext.data.*', 'Ext.tip.QuickTipManager'],
    store: 'Mensajes',
    columnLines: true,
    hideHeaders: true,
    columns: [{
            dataIndex: 'id',
            hidden: true
        }, {
            header: "",
            width: 40,
            //flex:1,
            dataIndex: 'tipo',
            filters: false,
            renderer: function(val, metaData, record, colIndex, store) {
                if (val == 'urgente') {
                    metaData['tdAttr'] = 'data-qtip="Este es un Mensaje Urgente"';
                    return '<img src="/semti.garantia/resources/images/icons/Error.png">';
                } else if (val == 'alerta') {
                    metaData['tdAttr'] = 'data-qtip="Este es un Mensaje de Advertencia"';
                    return '<img src="/semti.garantia/resources/images/icons/Warning.png">';
                } else if (val == 'info') {
                    metaData['tdAttr'] = 'data-qtip="Este es un Mensaje Informativo"';
                    return '<img src="/semti.garantia/resources/images/icons/information.png">';
                }
            }
        }, {
            header: "Descripci\xF3n",
            //width: 145,
            flex: 1,
            dataIndex: 'descripcion',
            filters: false
        }],
    
    viewConfig: { stripeRows: true },

    initComponent: function() {
        this.dockedItems = [{
                xtype: 'toolbar',
                cls: 'toolbar',
                height: 39,
                items: [{
                        cls: 'toolbar_button',
                        text: 'Todos',
                        itemId: 'todos',
                        tooltip: 'Mostrar todos los mensajes'
                    }, {
                        iconCls: 'icon-urgente',
                        cls: 'toolbar_button',
                        itemId: 'urgente',
                        text: 'Urgentes',
                        tooltip: 'Mostrar los mensajes urgentes'
                    }, {
                        iconCls: 'icon-warning',
                        cls: 'toolbar_button',
                        itemId: 'alerta',
                        text: 'Advertencias',
                        tooltip: 'Mostrar los mensajes de advertencias'
                    },{
                        iconCls: 'icon-information',
                        cls: 'toolbar_button',
                        itemId: 'info',
                        text: 'Informaciones',
                        tooltip: 'Mostrar los mensajes informativos'
                }]
            }];

        this.callParent(arguments);
    }
});