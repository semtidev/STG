// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('SEMTI.view.graficas.GtiaGridSDPendientes', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.gtiagridsdpendientes',
    requires: ['Ext.toolbar.Paging', 'Ext.grid.*', 'Ext.data.*', 'Ext.util.*', 'Ext.tip.QuickTipManager', 'Ext.ux.grid.FiltersFeature', 'Ext.grid.column.Action', 'Ext.form.field.Checkbox', 'Ext.button.Button'],
    defaults: {
        bodyStyle: 'padding:0'
    },
    store: 'Gtiasdpendientes',
    features: [{ftype: 'filters', encode: true, local: true, filters: [{type: 'boolean', dataIndex: 'visible'}]}],
    columnLines: true,
    columns: [{
            text: 'No.',
            lockable: true,
            locked: true,
            width: 60,
            dataIndex: 'numero',
            align: 'center',
            filter: {
                type: 'int'
            }
        }, {
            text: "Descripci\xF3n",
            lockable: true,
            flex: 1,
            minWidth: 380,
            locked: true,
            dataIndex: 'descripcion',
            align: 'left',
            filter: {
                type: 'string'
            }
        }, {
            text: "Proyecto",
            lockable: true,
            width: 200,
            dataIndex: 'proyecto',
            align: 'left',
            filter: {
                type: 'string'
            }
        }, {
            text: "Zona",
            lockable: true,
            width: 100,
            dataIndex: 'zona',
            align: 'center',
            filter: {
                type: 'string'
            }
        }, {
            text: "Objeto",
            lockable: true,
            width: 230,
            dataIndex: 'objeto',
            align: 'left',
            filter: {
                type: 'string'
            }
        }, {
            text: "Dpto",
            lockable: true,
            width: 200,
            dataIndex: 'dpto',
            align: 'left',
            filter: {
                type: 'string'
            }
        }, {
            text: "Fecha Reporte",
            lockable: true,
            width: 150,
            dataIndex: 'fecha_reporte',
            align: 'center',
            xtype: 'datecolumn',
            format: 'd/m/Y',
            filter: {
                type: 'date',
                dateFormat: 'Y-m-d'
            }
        }, {
            text: "Demora (d\xEDas)",
            lockable: true,
            width: 120,
            dataIndex: 'demora',
            align: 'center',
            filter: {
                type: 'int'
            }
        }, {
            text: "Constructiva",
            lockable: true,
            width: 120,
            align: 'center',
            dataIndex: 'constructiva'
        }, {
            text: "Suministro",
            lockable: true,
            width: 90,
            align: 'center',
            dataIndex: 'suministro',
            menuDisabled: true,
            sortable: false,
            resizable: false,
            renderer: function(val, metaData, record, colIndex, store) {
                if (record != null) {
                    if (val == 'Si') {
                        if (record.get('compra') == 'Importacion') {
                            metaData['tdAttr'] = 'data-qtip="Compra por Importaci\xF3n"';
                            return val;
                        }
                        else if (record.get('compra') == 'Nacional') {
                            metaData['tdAttr'] = 'data-qtip="Compra Nacional"';
                            return val;
                        }
                        else {
                            return val;
                        }
                    }
                    else {
                        return val;
                    }
                }
            }
        }, {
            text: "Afecta Exp. Hotel",
            lockable: true,
            width: 150,
            align: 'center',
            dataIndex: 'afecta_explotacion'
        }, {
            text: "Comentario",
            lockable: true,
            width: 300,
            dataIndex: 'comentario',
            filter: {
                type: 'string'
            }
        }],
    //viewConfig: { stripeRows: true },

    initComponent: function() {

        var comboProyects = Ext.create('SEMTI.view.proyectos.ProyectsComboAll', {
            editable: true,
            id: 'viewSDPendientesProyects',
            width: 200,
            margin: '5 10 0 5',
            allowBlank: true,
            name: 'proyecto',
            emptyText: 'Todos los Proyectos'
        });
                
        var comboTipo = Ext.create('SEMTI.view.garantia.GtiasdComunHabit', {
            allowBlank: true,
            id: 'viewSDPendientesTipo',
            editable: true,
            width: 200,
            name: 'tipo',
            emptyText: 'Todas las SD',
            margin: '5 0 0 0'
        });

        comboProyects.getStore().load();

        this.dockedItems = [{
                xtype: 'toolbar',
                cls: 'toolbar',
                ui: 'footer',
                height: 40,
                items: [comboProyects, comboTipo]
            }, {
                xtype: 'pagingtoolbar',
                cls: 'toolbar',
                dock: 'bottom',
                store: 'Gtiasdpendientes',
                displayInfo: true,
                displayMsg: 'Mostrando Solicitudes de Defectaci\xF3n {0} - {1} de {2}',
                emptyMsg: "Ninguna Solicitud de Defectaci\xF3n encontrada."
            }];
        this.callParent(arguments);
        //this.getStore().load();
    }

});