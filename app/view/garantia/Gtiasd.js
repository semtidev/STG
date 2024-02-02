Ext.define('SEMTI.view.garantia.Gtiasd', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.gtiasd',
    requires: ['Ext.toolbar.Paging', 'Ext.grid.*', 'Ext.data.*', 'Ext.util.*', 'Ext.tip.QuickTipManager', 'Ext.ux.grid.FiltersFeature', 'Ext.grid.column.Action', 'Ext.form.field.Checkbox', 'Ext.button.Button'],
    listeners: {
        'selectionchange': function(view, records) {
            this.down('#edit').setDisabled(!records.length);//Se Habilita el Boton Editar
            this.down('#delete').setDisabled(!records.length);//Se Habilita el Boton Delete
        },
        'afterrender': function(view) {
            var me = this;
                                  
            Ext.Ajax.request({
                url: './php/garantia/SdActions.php',
                method: 'POST',
                params: {accion: 'SdExistFilters'},
                success: function(result, request) {
                    var jsonData = Ext.JSON.decode(result.responseText);
                    if (jsonData.existe == 'true') {
                        me.down('#gtia_sd_busqueda_check').setVisible(true);
                        me.down('#gtia_sd_busqueda_check').setValue(true);
                    }
                },
                failure: function() {

                    Ext.MessageBox.show({
                        title: 'Mensaje del Sistema',
                        msg: 'Ha ocurrido un error en el Sistema. Por favor, vuelva a intentar realizar la operacion, de continuar el problema consulte al Administrador del Sistema.',
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            });
        }
    },
    getChecked: function(prop) {
        var prop = prop || null;
        var checked = [];

        this.getView().getStore().getRootNode().cascadeBy(function(node) {
            if (node.data.checked) {
                if (prop && node.data[prop])
                    checked.push(node.data[prop]);
                else
                    checked.push(node);
            }
        });

        return checked;
    },
    defaults: {
        bodyStyle: 'padding:0'
    },
    store: 'Gtiasd',
    cls: 'x-grid3-row',
    selType: 'checkboxmodel',
    features: [{ftype: 'filters', encode: true, local: true, filters: [{type: 'boolean', dataIndex: 'visible'}]}],
    columnLines: true,
    columns: [{
            xtype: 'actioncolumn',
            id: 'gtiasdColumnDel',
            cls: 'tasks-icon-column-header tasks-delete-column-header grid-header-trigger-cursor',
            width: 28,
            locked: true,
            lockable: false,
            icon: './resources/images/icons/Delete.png',
            margin: 0,
            iconCls: 'x-hidden',
            align: 'center',
            tooltip: 'Eliminar SD',
            menuDisabled: true,
            sortable: false,
            resizable: false
        }, {
            xtype: 'actioncolumn',
            id: 'gtiasdColumnUpd',
            cls: 'tasks-icon-column-header tasks-edit-column-header',
            width: 28,
            locked: true,
            lockable: false,
            icon: './resources/images/icons/edit2.png',
            margin: 0,
            iconCls: 'x-hidden',
            align: 'center',
            tooltip: 'Modificar SD',
            menuDisabled: true,
            sortable: false,
            resizable: false
        }, {
            text: "<img src='./resources/images/icons/ic-pdf.gif.png' style='margin-top: 2px;'></img>",
            locked: true,
            lockable: false,
            menuDisabled: true,
            sortable: false,
            resizable: false,
            width: 40,
            dataIndex: 'documento',
            align: 'center',
            renderer: function(val, metaData, record, colIndex, store) {
                if (record !== null) {
                    var result = record.get('documento');
                    if (result !== null && result.length > 0) {
                        metaData['tdAttr'] = 'data-qtip="Abrir Documento Digital Escaneado de la SD"';
                        let ipserver = localStorage.getItem('ipserver');    
                        return '<a href="http://'+ipserver+'/semti.garantia/resources/documents/SD/' + result + '" target="_blank">Ver<a/>';
                    } else {
                        return ' ';
                    }
                }
            }
        }, {
            text: "No.",
            lockable: true,
            width: 60,
            locked: true,
            dataIndex: 'numero',
            align: 'center',
            filter: {
                type: 'string'
            }
        }, {
            text: "Descripci\xF3n",
            lockable: true,
            flex: 1,
            minWidth: 350,
            locked: true,
            dataIndex: 'descripcion',
            align: 'left',
            filter: {
                type: 'string'
            }
        }, {
            text: "Proyecto",
            lockable: true,
            width: 150,
            dataIndex: 'proyecto',
            align: 'center',
            filter: {
                type: 'string'
            }
        }, {
            text: "Zona",
            lockable: true,
            width: 70,
            dataIndex: 'zona',
            align: 'center',
            filter: {
                type: 'string'
            }
        }, {
            text: "Objeto (Local)",
            lockable: true,
            width: 230,
            dataIndex: 'objeto',
            align: 'left',
            filter: {
                type: 'string'
            },
            renderer: function(val, metaData) {
                metaData['tdAttr'] = 'data-qtip="' + val + '"';
                return val;
            }
        }, {
            text: "Departamento(s)",
            lockable: true,
            width: 200,
            dataIndex: 'dpto',
            align: 'left',
            filter: {
                type: 'string'
            }
        }, {
            text: "Problema",
            lockable: true,
            width: 200,
            dataIndex: 'problema',
            align: 'left',
            filter: {
                type: 'string'
            }
        }, {
            text: "Costo USD",
            lockable: true,
            width: 100,
            dataIndex: 'costo',
            align: 'right',
            filter: {
                type: 'string'
            }
        }, {
            text: "Fecha Reporte",
            lockable: true,
            width: 120,
            dataIndex: 'fecha_reporte',
            align: 'center',
            xtype: 'datecolumn', // the column type
            format: 'd/m/Y',
            filter: {
                type: 'date',
                dateFormat: 'Y-m-d'
            }
        }, {
            text: "Fecha Soluci\xF3n",
            lockable: true,
            width: 120,
            dataIndex: 'fecha_solucion',
            align: 'center',
            xtype: 'datecolumn', // the column type
            format: 'd/m/Y',
            filter: {
                type: 'date',
                dateFormat: 'Y-m-d'
            }
        }, {
            text: "Demora (d\xEDas)",
            lockable: true,
            width: 110,
            dataIndex: 'demora',
            align: 'center',
            filter: {
                type: 'int'
            }
        }, {
            text: "Estado",
            width: 100,
            maxHeight: 30,
            align: 'center',
            dataIndex: 'estado',
            menuDisabled: true,
            sortable: false,
            resizable: false,
            /*renderer: function(val, metaData, record, colIndex, store) {
                if (record != null) {
                    if (val == 'F') {
                        metaData['tdAttr'] = 'data-qtip="Firmada"';
                        return '<img src="/semti.garantia/resources/images/icons/smile.png" style="margin:0;">';
                    }
                    if (val == 'PR') {
                        metaData['tdAttr'] = 'data-qtip="Por Resolver"';
                        return '<img src="/semti.garantia/resources/images/icons/sad.png" style="margin:0;">';
                    }
                    if (val == 'NP') {
                        metaData['tdAttr'] = 'data-qtip="No Procede"';
                        return '<img src="/semti.garantia/resources/images/icons/confuced.png" style="margin:0;">';
                    }
                    if (val == 'R') {
                        metaData['tdAttr'] = 'data-qtip="Reclamada"';
                        return '<img src="/semti.garantia/resources/images/icons/angry.png" style="margin:0;">';
                    }
                }
            }*/
        }, {
            text: "Constructiva",
            lockable: true,
            width: 100,
            align: 'center',
            dataIndex: 'constructiva',
            menuDisabled: true,
            sortable: false,
            resizable: false,
            /*renderer: function(val, metaData, record, colIndex, store) {
                if (record != null) {
                    if (val == 1) {
                        metaData['tdAttr'] = 'data-qtip="Si"';
                        return '<img src="/semti.garantia/resources/images/icons/check.png" style="margin:0;">';
                    }
                    else {
                        metaData['tdAttr'] = 'data-qtip="No"';
                        return '<img src="/semti.garantia/resources/images/icons/check2.png" style="margin:0;">';
                    }
                }
            }*/
        }, {
            text: "AEH",
            lockable: true,
            width: 80,
            align: 'center',
            dataIndex: 'afecta_explotacion',
            menuDisabled: true,
            sortable: false,
            resizable: false,
            /*renderer: function(val, metaData, record, colIndex, store) {
                if (record != null) {
                    if (val == 1) {
                        metaData['tdAttr'] = 'data-qtip="Si"';
                        return '<img src="/semti.garantia/resources/images/icons/check.png" style="margin:0;">';
                    }
                    else {
                        metaData['tdAttr'] = 'data-qtip="No"';
                        return '<img src="/semti.garantia/resources/images/icons/check2.png" style="margin:0;">';
                    }
                }
            }*/
        }, {
            text: "Suministro",
            lockable: true,
            width: 90,
            align: 'center',
            dataIndex: 'suministro',
            menuDisabled: true,
            sortable: false,
            resizable: false,
            /*renderer: function(val, metaData, record, colIndex, store) {
                if (record != null) {
                    if (val == 1) {
                        if (record.get('compra') == 'Imp') {
                            metaData['tdAttr'] = 'data-qtip="Compra por Importaci\xF3n"';
                            return '<img src="/semti.garantia/resources/images/icons/check.png" style="margin:0;">';
                        }
                        else if (record.get('compra') == 'Nac') {
                            metaData['tdAttr'] = 'data-qtip="Compra Nacional"'
                            return '<img src="/semti.garantia/resources/images/icons/check.png" style="margin:0;">';
                        }
                        else{
                            metaData['tdAttr'] = 'data-qtip="Si"';
                            return '<img src="/semti.garantia/resources/images/icons/check.png" style="margin:0;">';
                        }
                    }
                    else {
                        metaData['tdAttr'] = 'data-qtip="No"';
                        return '<img src="/semti.garantia/resources/images/icons/check2.png" style="margin:0;">';
                    }
                }
            }*/
        }, {
            text: "Comentario",
            lockable: true,
            width: 400,
            dataIndex: 'comentario',
            filter: {
                type: 'string'
            }
        }],
    viewConfig: {stripeRows: true},
    initComponent: function() {

        var comboProyects = Ext.create('SEMTI.view.proyectos.ProyectsComboAll', {
                editable: true,
                id: 'viewSDcomboProyects',
                width: 170,
                margin: '5 10 0 5',
                allowBlank: true,
                name: 'proyecto',
                emptyText: 'Todos los Proyectos',
            });
        
        var comboEstado = Ext.create('SEMTI.view.garantia.GtiasdEstado', {
                allowBlank: true,
                id: 'viewSDcomboEstado',
                editable: true,
                width: 160,
                name: 'estado',
                emptyText: 'Todos los Estados',
                margin: '5 10 0 0'
            });
        
        var comboTipo = Ext.create('SEMTI.view.garantia.GtiasdComunHabit', {
                allowBlank: true,
                id: 'viewSDcomboTipo',
                editable: true,
                width: 150,
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
                items: [{
                        xtype: 'button',
                        height: 25,
                        cls: 'button-default-background-color button-default-border-color button-default-color',
                        text: 'Archivo SD',
                        menu: {
                            lid: 'mainSD',
                            items: [
                                {text: 'Nueva', lid: 'newSD', tooltip: 'Nueva SD', iconCls: 'icon-new'}, '-',
                                {text: 'Modificar', lid: 'updSD', disabled: true, itemId: 'edit', tooltip: 'Modificar SD', iconCls: 'icon-edit'},
                                {text: 'Eliminar', lid: 'delSD', disabled: true, itemId: 'delete', tooltip: 'Eliminar SD', iconCls: 'icon-delete'}, '-',
                                {text: 'Filtros...', lid: 'filtros', id: 'filtros', tooltip: 'Realizar B\xFAsqueda Avanzada de SD', iconCls: 'icon-filter'},
                                {text: 'Gestor de Informes', lid: 'reportSD', tooltip: 'Generar un Nuevo Informe del listado de SD', iconCls: 'icon_resumen'}, '-',
                                {text: 'Actualizar', lid: 'actualizarArchivoSD', tooltip: 'Actualizar Archivo SD', iconCls: 'icon-update'}
                            ]
                        }
                    }, {
                        xtype: 'component',
                        height: 27,
                        width: 2,
                        margin: '0 3 0 3',
                        cls: 'separator'
                    }, comboProyects, comboEstado, comboTipo, '->', {
                        xtype: 'checkbox',
                        id: 'gtia_sd_busqueda_check',
                        hidden: true
                    }, {
                        text: 'Filtros...',
                        height: 25,
                        cls: 'button-default-background-color button-default-border-color button-default-color',
                        iconCls: 'icon-filter',
                        action: 'buscar',
                        tooltip: 'Realizar B\xFAsqueda Avanzada.'
                    }]
            }, {
                xtype: 'pagingtoolbar',
                cls: 'toolbar',
                id: 'gtia_sd_paging',
                disabled: false,
                dock: 'bottom',
                store: 'Gtiasd',
                displayInfo: true,
                displayMsg: 'Mostrando Solicitudes de Defectaci\xF3n {0} - {1} de {2}',
                emptyMsg: "Ninguna Solicitud de Defectaci\xF3n encontrada."
            }];

        this.callParent(arguments);
        //this.getStore().load();
    }

});