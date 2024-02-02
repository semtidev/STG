Ext.define('SEMTI.view.informes.ResumenGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.ResumenGrid',
    requires: ['Ext.grid.*', 'Ext.data.*', 'Ext.util.*', 'Ext.grid.column.Action', 'Ext.form.field.Checkbox', 'Ext.button.Button'],
    listeners: {
        'selectionchange': function(view, records) {
            this.down('#export').setDisabled(!records.length);    // Se Habilita el Boton Exportar
        },
        'viewready': function() {
             var record = this.getStore().getAt(0);
             this.getSelectionModel().select(record);
             console.log('view-ready');
        }
    },
    getChecked: function(prop) {
        var propdata = prop || null;
        var checked = [];

        this.getView().getStore().getRootNode().cascadeBy(function(node) {
            if (node.data.checked) {
                if (prop && node.data[propdata])
                    checked.push(node.data[propdata]);
                else
                    checked.push(node);
            }
        });

        return checked;
    },
    defaults: {
        bodyStyle: 'padding:0'
    },
    store: 'Resumen',
    cls: 'x-grid3-row',
    //selType: 'checkboxmodel',
    columnLines: true,
    columns: [{
            xtype: 'actioncolumn',
            id: 'gtiaresumenColumnDel',
            cls: 'tasks-icon-column-header tasks-delete-column-header',
            width: 28,
            locked: false,
            lockable: false,
            icon: './resources/images/icons/Delete.png',
            margin: 0,
            iconCls: 'x-hidden',
            align: 'center',
            tooltip: 'Eliminar Informe',
            menuDisabled: true,
            sortable: false,
            resizable: false
        }, {
            xtype: 'actioncolumn',
            id: 'gtiaresumenColumnUpd',
            cls: 'tasks-icon-column-header tasks-edit-column-header',
            width: 28,
            locked: false,
            lockable: false,
            icon: './resources/images/icons/edit2.png',
            margin: 0,
            iconCls: 'x-hidden',
            align: 'center',
            tooltip: 'Modificar Informe',
            menuDisabled: true,
            sortable: false,
            resizable: false
        }, {
            text: "Titulo",
            lockable: true,
            flex: 3,
            minWidth: 150,
            locked: false,
            dataIndex: 'titulo',
            align: 'left',
            renderer: function(val, metaData) {
                metaData['tdAttr'] = 'data-qtip="' + val + '"';
                return val;
            }
        }, {
            text: "Proyecto",
            lockable: true,
            flex: 1,
            dataIndex: 'proyecto',
            align: 'left',
            renderer: function(val, metaData) {
                metaData['tdAttr'] = 'data-qtip="' + val + '"';
                return val;
            }
        }, {
            text: "Zona(s)",
            lockable: true,
            flex: 1,
            dataIndex: 'zona',
            align: 'left',
            renderer: function(val, metaData) {
                metaData['tdAttr'] = 'data-qtip="' + val + '"';
                return val;
            }
        }, {
            text: "Desde",
            lockable: true,
            width: 90,
            dataIndex: 'desde',
            align: 'center',
            xtype: 'datecolumn', // the column type
            format: 'd/m/Y'
        }, {
            text: "Hasta",
            lockable: true,
            width: 90,
            dataIndex: 'hasta',
            align: 'center',
            xtype: 'datecolumn', // the column type
            format: 'd/m/Y'
        }, {
            text: "Fecha Actualizado",
            lockable: true,
            width: 135,
            dataIndex: 'fechamod',
            align: 'center',
            xtype: 'datecolumn', // the column type
            format: 'd/m/Y H:i'
    }],
    viewConfig: {stripeRows: true},
    initComponent: function() {

        this.dockedItems = [{
                xtype: 'toolbar',
                cls: 'toolbar',
                height: 39,
                items: [{
                        iconCls: 'icon-add',
                        cls: 'toolbar_button',
                        text: 'Nuevo',
                        tooltip: 'Nuevo Informe',
                        action: 'agregar'
                    },
                    '-', {
                        xtype:'splitbutton', 
                        itemId: 'export',
                        iconCls: 'icon-download',
                        cls: 'toolbar_button',
                        text: 'Exportar como...',
                        tooltip: 'Elija el formato en que desea Generar el Informe',
                        disabled: true,
                        action: 'imprimir',
                        menu: {
                            lid: 'exportResumen',
                            items: [
                                {
                                    text: 'Documento PDF',
                                    iconCls: 'icon-exppdf',
                                    lid: 'docpdf'
                                },{
                                    text: 'Presentaci\xF3n de PowerPoint',
                                    iconCls: 'icon-expppt',
                                    lid: 'docppt'
                                }
                            ]
                        }
                    }]
            }];

        this.callParent(arguments);
        this.getStore().load();
    }
});