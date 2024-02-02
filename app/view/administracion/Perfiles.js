// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('SEMTI.view.administracion.Perfiles', {
    extend: 'Ext.ux.LiveSearchGridPanel',
    alias: 'widget.perfiles',
    requires: ['Ext.toolbar.Paging', 'Ext.grid.*', 'Ext.data.*', 'Ext.util.*', 'Ext.tip.QuickTipManager', 'Ext.ux.LiveSearchGridPanel', 'Ext.ux.grid.FiltersFeature'],
    //iconCls: 'icon-user',
    listeners: {
        'selectionchange': function(view, records) {
            this.down('#edit').setDisabled(!records.length);//Se Habilita el Boton Editar
            this.down('#delete').setDisabled(!records.length);//Se Habilita el Boton Delete
        }
    },
    iconCls: 'icon-perfil',
    store: 'Perfiles',
    features: [{ftype: 'filters', encode: true, local: true, filters: [{type: 'boolean', dataIndex: 'visible'}]}],
    columnLines: true,
    columns: [{
            header: "Nombre",
            flex: 1,
            dataIndex: 'nombre',
            filter: {
                type: 'string'
            }
        }, {
            header: "Descripci\xF3n",
            flex: 2,
            dataIndex: 'descripcion',
            filter: {
                type: 'string'
            }
        }],
    viewConfig: {stripeRows: true},
    initComponent: function() {

        this.dockedItems = [{
                xtype: 'toolbar',
                cls: 'toolbar',
                items: [{
                        iconCls: 'icon-add',
                        cls: 'toolbar_button',
                        text: 'Nuevo',
                        tooltip: 'Agregar un Nuevo Rol de Usuario',
                        action: 'agregar'
                    }, {
                        itemId: 'edit',
                        iconCls: 'icon-edit',
                        cls: 'toolbar_button',
                        text: 'Editar',
                        disabled: true,
                        tooltip: 'Modificar Rol',
                        action: 'editar'
                    }, {
                        itemId: 'delete',
                        iconCls: 'icon-delete',
                        cls: 'toolbar_button',
                        text: 'Eliminar',
                        disabled: true,
                        tooltip: 'Eliminar Rol',
                        action: 'eliminar'
                    }, '-', {
                        iconCls: 'icon-exppdf',
                        cls: 'toolbar_button',
                        text: 'Imprimir',
                        tooltip: 'Documento PDF',
                        action: 'imprimir'
                    }]
            }, {
                xtype: 'pagingtoolbar',
                cls: 'toolbar',
                dock: 'bottom',
                store: 'Perfiles',
                displayInfo: true,
                displayMsg: 'Mostrando Perfiles {0} - {1} de {2}',
                emptyMsg: "Ning\u00FAn Perfil encontrado."
            }];

        this.callParent(arguments);
        this.getStore().load();
    }
});