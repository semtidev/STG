Ext.define('SEMTI.view.garantia.GtiasdFiltros', {
    extend: 'Ext.window.Window',
    alias: 'widget.gtiasdfiltros',
    requires: [
        'Ext.form.Panel',
        'Ext.form.field.Text',
        'Ext.form.field.ComboBox',
        'SEMTI.view.proyectos.ProyectsPickerFilter',
        'SEMTI.view.garantia.Gtiaproblemas',
        'SEMTI.view.sistema.SystDptos',
        'SEMTI.view.garantia.GtiasdEstado',
        'SEMTI.view.garantia.GtiasdConSumAEH',
        'SEMTI.view.garantia.GtiasdCriterioDemora',
        'SEMTI.view.garantia.GtiasdCriterioCosto'
    ],
    layout: 'fit',
    autoShow: true,
    width: 700,
    resizable: false,
    modal: true,
    iconCls: 'icon-filter',
    title: 'Filtros Avanzados',
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                padding: '15 20 10 20',
                border: false,
                modal: true,
                style: 'background-color: #fff;',
                fieldDefaults: {
                    anchor: '100%',
                    labelWidth: 150,
                    combineErrors: true,
                    msgTarget: 'side',
                    margin: '0 0 10 0'
                },
                items: [{
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        height: 45,
                        msgTarget: 'none',
                        padding: 0,
                        items: [{
                                xtype: 'textfield',
                                id: 'gtiasd_filtros_desc',
                                flex: 1,
                                allowBlank: true,
                                fieldLabel: 'Descripci\xF3n de la SD (Contiene / No contiene)',
                                labelAlign: 'top',
                                emptyText: 'Ejemplo: TextoQueContiene, -TextoQueNoContiene',
                                maxLength: 70,
                                margin: '0 15 0 0',
                                name: 'descripcion'
                            }, {
                                xtype: 'textfield',
                                width: 150,
                                allowBlank: true,
                                name: 'numero',
                                fieldLabel: 'N\xFAmero (Contiene)',
                                labelAlign: 'top',
                                regex: /[0-9]+$/,
                                margin: '0 0 10 0'
                            }]
                    }, {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        height: 45,
                        msgTarget: 'none',
                        padding: 0,
                        items: [{
                                xtype: 'SystDptos',
                                allowBlank: true,
                                editable: true,
                                flex: 1,
                                margin: '0 15 0 0',
                                fieldLabel: 'Departamento',
                                emptyText: 'Todos los Departamentos',
                                labelAlign: 'top',
                                name: 'dpto'
                            }, {
                                xtype: 'Gtiaproblemascombo',
                                allowBlank: true,
                                name: 'problema',
                                flex: 1,
                                margin: '0 0 0 0',
                                emptyText: 'Todos los Tipos',
                                fieldLabel: 'Tipo de Problema',
                                labelAlign: 'top'
                            }]
                    }, {
                        xtype: 'ProyectsPickerFilter',
                        name: 'objeto_parte',
                        flex: 1,
                        fieldLabel: 'Elemento de la Estructura de Proyectos',
                        labelAlign: 'top'
                    }, {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        height: 50,
                        msgTarget: 'none',
                        padding: 0,
                        items: [{
                                xtype: 'GtiasdConSumAEH',
                                allowBlank: true,
                                width: 90,
                                margin: '0 15 0 0',
                                editable: true,
                                name: 'constructiva',
                                emptyText: 'Todas',
                                fieldLabel: 'Constructiva',
                                labelAlign: 'top'
                            }, {
                                xtype: 'GtiasdConSumAEH',
                                editable: true,
                                width: 90,
                                margin: '0 15 0 0',
                                allowBlank: true,
                                name: 'afecta_explotacion',
                                emptyText: 'Todas',
                                fieldLabel: 'AEH',
                                labelAlign: 'top'
                            }, {
                                xtype: 'GtiasdConSumAEH',
                                editable: true,
                                width: 90,
                                margin: 0,
                                allowBlank: true,
                                name: 'suministro',
                                emptyText: 'Todas',
                                fieldLabel: 'Suministro',
                                labelAlign: 'top',
                                listeners: {
                                    'change': function(combo, newValue) {
                                        if (newValue == 1) {
                                            Ext.getCmp('SdFilterCompra').setVisible(true);
                                        }
                                        else {
                                            Ext.getCmp('SdFilterCompra').setVisible(false);
                                        }
                                    }
                                }
                            }, {
                                xtype: 'checkboxgroup',
                                id: 'SdFilterCompra',
                                hidden: true,
                                fieldLabel: 'Tipo de Compra',
                                labelWidth: 110,
                                margin: '20 0 0 50',
                                padding: '0 0 0 0',
                                // Arrange checkboxes into two columns, distributed vertically
                                columns: 2,
                                vertical: true,
                                items: [
                                    {width: 110, margin: '0 0 0 0', boxLabel: 'Importaci\xF3n', name: 'compra_imp', inputValue: 'Imp', checked: true},
                                    {width: 120, margin: '0 0 0 0', boxLabel: 'Nacional', name: 'compra_nac', inputValue: 'Nac', checked: true}
                                ]
                            }]
                    }, {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        height: 130,
                        msgTarget: 'none',
                        padding: 0,
                        items: [{
                                xtype: 'fieldset',
                                flex: 1,
                                height: 125,
                                title: 'Fecha del Reporte / Costo de Garantía',
                                defaultType: 'datefield',
                                layout: 'anchor',
                                padding: '5 15 5 15',
                                margin: '0 15 0 0',
                                defaults: {
                                    anchor: '100%',
                                    labelWidth: '8',
                                    hideEmptyLabel: true
                                },
                                items: [{
                                        xtype: 'fieldcontainer',
                                        height: 50,
                                        combineErrors: true,
                                        msgTarget: 'none',
                                        layout: 'hbox',
                                        items: [{
                                                xtype: 'datefield',
                                                editable: true,
                                                allowBlank: true,
                                                name: 'reportes_desde',
                                                fieldLabel: 'Desde',
                                                format: 'd/m/Y',
                                                submitFormat: 'Y-m-d',
                                                flex: 1,
                                                labelAlign: 'top',
                                                margin: '0 5 0 0'
                                            }, {
                                                xtype: 'datefield',
                                                editable: true,
                                                allowBlank: true,
                                                name: 'reportes_hasta',
                                                fieldLabel: 'Hasta',
                                                format: 'd/m/Y',
                                                submitFormat: 'Y-m-d',
                                                flex: 1,
                                                margin: '0 0 0 5',
                                                labelAlign: 'top',
                                                emptyText: 'Hoy',
                                            }]
                                    },
                                    {
                                        xtype: 'fieldcontainer',
                                        combineErrors: true,
                                        msgTarget: 'none',
                                        layout: 'hbox',
                                        items: [{
                                                xtype: 'checkboxfield',
                                                allowBlank: false,
                                                name: 'hascosto',
                                                width: 70,
                                                checked: false,
                                                boxLabel: 'Costo',
                                                listeners: {
                                                    'change': function(checkbox, newValue, oldValue, eOpts) {
                                                        if (newValue == true) {
                                                            Ext.getCmp('gtiasearchcriteriocosto').setDisabled(false);
                                                            Ext.getCmp('gtiasearchcosto').setDisabled(false);
                                                        }
                                                        else {
                                                            Ext.getCmp('gtiasearchcriteriocosto').setDisabled(true);
                                                            Ext.getCmp('gtiasearchcosto').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            }, {
                                                xtype: 'GtiasdCriterioCosto',
                                                id: 'gtiasearchcriteriocosto',
                                                width: 50,
                                                allowBlank: true,
                                                editable: false,
                                                value: '=',
                                                name: 'criteriocosto',
                                                margin: '0 10 0 0',
                                                disabled: true
                                            }, {
                                                xtype: 'numberfield',
                                                id: 'gtiasearchcosto',
                                                name: 'costo',
                                                fieldLabel: '$',
                                                labelWidth: 10,
                                                width: 110,
                                                value: 0,
                                                step: 0.01,
                                                editable: true,
                                                minValue: 0,
                                                disabled: true
                                            }]
                                    }
                                ]
                            }, {
                                xtype: 'fieldset',
                                flex: 1,
                                height: 125,
                                title: 'Soluci\xF3n del Reporte',
                                defaultType: 'datefield',
                                layout: 'anchor',
                                padding: '5 15 5 15',
                                margin: 0,
                                defaults: {
                                    anchor: '100%',
                                    labelWidth: '8',
                                    hideEmptyLabel: true
                                },
                                items: [{
                                        xtype: 'fieldcontainer',
                                        height: 50,
                                        combineErrors: true,
                                        msgTarget: 'none',
                                        layout: 'hbox',
                                        items: [{
                                                xtype: 'datefield',
                                                editable: true,
                                                allowBlank: true,
                                                name: 'solucion_desde',
                                                fieldLabel: 'Desde',
                                                format: 'd/m/Y',
                                                submitFormat: 'Y-m-d',
                                                flex: 1,
                                                labelAlign: 'top',
                                                margin: '0 5 0 0'
                                            }, {
                                                xtype: 'datefield',
                                                editable: true,
                                                allowBlank: true,
                                                name: 'solucion_hasta',
                                                fieldLabel: 'Hasta',
                                                format: 'd/m/Y',
                                                submitFormat: 'Y-m-d',
                                                flex: 1,
                                                margin: '0 0 0 5',
                                                labelAlign: 'top',
                                                emptyText: 'Hoy'
                                            }]
                                    }, {
                                        xtype: 'fieldcontainer',
                                        combineErrors: true,
                                        msgTarget: 'none',
                                        layout: 'hbox',
                                        items: [{
                                                xtype: 'checkboxfield',
                                                allowBlank: false,
                                                name: 'demora',
                                                width: 140,
                                                checked: false,
                                                boxLabel: 'Demora Soluci\xF3n',
                                                listeners: {
                                                    'change': function(checkbox, newValue, oldValue, eOpts) {
                                                        if (newValue == true) {
                                                            Ext.getCmp('gtiainformesformcriteriodemora').setDisabled(false);
                                                            Ext.getCmp('gtiainformesformdiasdemora').setDisabled(false);
                                                            Ext.getCmp('gtiainformesformlabeldemora').getEl().setStyle('color', '#000000');
                                                        }
                                                        else {
                                                            Ext.getCmp('gtiainformesformcriteriodemora').setDisabled(true);
                                                            Ext.getCmp('gtiainformesformdiasdemora').setDisabled(true);
                                                            Ext.getCmp('gtiainformesformlabeldemora').getEl().setStyle('color', '#C0C0C0');
                                                        }
                                                    }
                                                }
                                            }, {
                                                xtype: 'GtiasdCriterioDemora',
                                                id: 'gtiainformesformcriteriodemora',
                                                width: 50,
                                                allowBlank: true,
                                                editable: false,
                                                value: '=',
                                                name: 'criteriodemora',
                                                margin: '0 10 0 0',
                                                disabled: true
                                            }, {
                                                xtype: 'numberfield',
                                                id: 'gtiainformesformdiasdemora',
                                                name: 'diasdemora',
                                                width: 50,
                                                value: 0,
                                                editable: false,
                                                minValue: 0,
                                                maxValue: 100,
                                                disabled: true
                                            }, {
                                                xtype: 'label',
                                                id: 'gtiainformesformlabeldemora',
                                                text: 'D\xEDas',
                                                margin: '4 0 0 7',
                                                style: {
                                                    color: '#C0C0C0'
                                                }
                                            }]
                                    }]
                            }]
                    }]
            }];

        this.dockedItems = [{
                xtype: 'toolbar',
                dock: 'bottom',
                id: 'buttons',
                ui: 'footer',
                items: [
                    '->', 
                    {
                        cls: 'app-form-btn',
                        text: '<i class="fas fa-check"></i>&nbsp;Aceptar',
                        action: 'filtrar'
                    },
                    {
                        cls: 'app-form-btn',
                        text: '<i class="fas fa-times"></i>&nbsp;Cancelar',
                        scope: this,
                        handler: this.close
                    }
                ]
            }];

        this.callParent(arguments);
    }
});