// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('SEMTI.view.administracion.Usuarios', {
    extend: 'Ext.ux.LiveSearchGridPanel',
    alias: 'widget.usuarios',
    id: 'usuarios',
    requires: ['Ext.toolbar.Paging', 'Ext.grid.*', 'Ext.data.*', 'Ext.util.*', 'Ext.tip.QuickTipManager', 'Ext.ux.LiveSearchGridPanel', 'Ext.ux.grid.FiltersFeature'],
    listeners: {
        'selectionchange': function(view, records) {
            this.down('#edit').setDisabled(!records.length);//Se Habilita el Boton Editar
            this.down('#delete').setDisabled(!records.length);//Se Habilita el Boton Delete
        }
    },
    iconCls: 'icon-user',
    store: 'Usuarios',
    features: [{ftype: 'filters', encode: true, local: true, filters: [{type: 'boolean', dataIndex: 'visible'}]}],
    columnLines: true,
    columns: [{
            header: "Usuario",
            width: 110,
            dataIndex: 'usuario',
            filter: {
                type: 'string'
            }
        }, {
            header: "Nombre",
            //width: 145,
            flex: 1,
            dataIndex: 'nombre',
            filter: {
                type: 'string'
            }
        }, {
            header: "Apellidos",
            flex: 1,
            dataIndex: 'apellidos',
            filter: {
                type: 'string'
            }
        }, {
            header: "Cargo",
            flex: 1,
            dataIndex: 'cargo',
            filter: {
                type: 'string'
            }
        }, {
            header: "Correo",
            width: 230,
            dataIndex: 'email'
        }, {
            header: "Activo",
            width: 70,
            align: 'center',
            dataIndex: 'activo'
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
                        tooltip: 'Agregar un Nuevo Usuario',
                        action: 'agregar'
                    }, {
                        itemId: 'edit',
                        iconCls: 'icon-edit',
                        cls: 'toolbar_button',
                        text: 'Editar',
                        disabled: true,
                        tooltip: 'Modificar Usuario',
                        action: 'editar'
                    }, {
                        itemId: 'delete',
                        iconCls: 'icon-delete',
                        cls: 'toolbar_button',
                        text: 'Eliminar',
                        disabled: true,
                        tooltip: 'Eliminar Usuario',
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
                store: 'Usuarios',
                displayInfo: true,
                displayMsg: 'Mostrando Usuarios {0} - {1} de {2}',
                emptyMsg: "Ning\u00FAn Usuario encontrado."
            }];

        this.callParent(arguments);

        let store = this.getStore(),
            polo = localStorage.getItem('polo_id');
        store.getProxy().setExtraParam('polo', polo);
        store.load();
    }
});