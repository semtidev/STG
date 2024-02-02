Ext.define('SEMTI.view.sistema.MsgSDPendientes', {
    extend: 'Ext.window.Window',
    xtype: 'MsgSDPendientes',
    layout: 'fit',
    autoShow: true,
    width: 950,
    height: 500,
    modal: true,
    title: 'SD Pendientes por Resolver',
    iconCls: 'icon-urgente',

    initComponent: function() {
        this.items = [{
                xtype: 'gridpanel',
                store: 'Gtiasdpendientes',
                columnLines: true,
                columns: [{
                        xtype: 'rownumberer',
                        text: 'Item',
                        width: 50,
                        align: 'center'
                    }, {
                        text: 'No',
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
                        width: 180,
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
                        align: 'left',
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
                        dataIndex: 'constructiva',
                        renderer: function(val, metaData, record, colIndex, store) {
                            if (record != null) {
                                if (val == 1) {
                                    metaData['tdAttr'] = 'data-qtip="Si"';
                                    return '<img src="/ccoGarantia/resources/images/icons/check.png" style="margin:0;">';
                                }
                                else {
                                    metaData['tdAttr'] = 'data-qtip="No"';
                                    return '<img src="/ccoGarantia/resources/images/icons/check2.png" style="margin:0;">';
                                }
                            }
                        }
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
                                if (val == 1) {
                                    if (record.get('compra') == 'Imp') {
                                        metaData['tdAttr'] = 'data-qtip="Compra por Importaci\xF3n"';
                                        return '<img src="/ccoGarantia/resources/images/icons/check.png" style="margin:0;">';
                                    }
                                    else if (record.get('compra') == 'Nac') {
                                        metaData['tdAttr'] = 'data-qtip="Compra Nacional"'
                                        return '<img src="/ccoGarantia/resources/images/icons/check.png" style="margin:0;">';
                                    }
                                    else {
                                        metaData['tdAttr'] = 'data-qtip="Si"';
                                        return '<img src="/ccoGarantia/resources/images/icons/check.png" style="margin:0;">';
                                    }
                                }
                                else {
                                    metaData['tdAttr'] = 'data-qtip="No"';
                                    return '<img src="/ccoGarantia/resources/images/icons/check2.png" style="margin:0;">';
                                }
                            }
                        }
                    }, {
                        text: "Afecta Exp. Hotel",
                        lockable: true,
                        width: 150,
                        align: 'center',
                        dataIndex: 'afecta_explotacion',
                        renderer: function(val, metaData, record, colIndex, store) {
                            if (record != null) {
                                if (val == 1) {
                                    metaData['tdAttr'] = 'data-qtip="Si"';
                                    return '<img src="/ccoGarantia/resources/images/icons/check.png" style="margin:0;">';
                                }
                                else {
                                    metaData['tdAttr'] = 'data-qtip="No"';
                                    return '<img src="/ccoGarantia/resources/images/icons/check2.png" style="margin:0;">';
                                }
                            }
                        }
                    }, {
                        text: "Comentario",
                        lockable: true,
                        width: 300,
                        dataIndex: 'comentario',
                        filter: {
                            type: 'string'
                        }
                    }]
            }];

        this.callParent(arguments);
        this.down('gridpanel').getStore().load();
    }

});