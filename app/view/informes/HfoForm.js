// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('SEMTI.view.informes.HfoForm', {
    extend: 'Ext.window.Window',
    alias : 'widget.hfoform',
 
    requires: [
        'Ext.form.Panel',
        'Ext.form.field.Text',
        'Ext.form.field.ComboBox',
        'SEMTI.view.proyectos.ProyectsCombo',
        'SEMTI.view.proyectos.ZonasCombo',
        'SEMTI.view.proyectos.ObjectsCombo'
    ],
     
    layout: 'fit',
    autoShow: true,
    width: 650,
    resizable: false,
    modal: true,
     
    iconCls: 'doc_unknow_icon',
 
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: '5 20 20 20',
            border: false,
            modal: true,
            style: 'background-color: #fff;',
                 
            fieldDefaults: {
            	anchor: '100%',
                labelAlign: 'left',
                labelWidth: 90,
                margin: '10 0 10 0',
                combineErrors: true,
                msgTarget: 'side'
            },
            items: [{
            	xtype: 'textfield',
                name: 'id',
                fieldLabel: 'id',
                hidden: true
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                height: 45,
                msgTarget: 'none',
                padding: 0,
                items: [{
                    xtype: 'textfield',
                    flex: 1,
                    allowBlank: false,
                    name: 'titulo',
                    emptyText: 'T\xEDtulo del Informe',
                    id: 'hfo_form_titulo',
                    fieldLabel: 'Titulo',
                    labelAlign: 'top',
                    margin: '0 10 0 0'
                },{
                    xtype: 'ProyectsCombo',
                    id: 'hfoformProyect',
                    editable: false,
                    width: 200,
                    allowBlank: false,
                    name: 'proyecto',
                    fieldLabel: 'Proyecto',
                    labelAlign: 'top',
                    emptyText: 'Proyecto que ser\xE1 evaluado',
                    margin: '0 0 10 0',
                    listeners: {
                        render: function(combo){ combo.getStore().load(); }
                    }
                }]
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                msgTarget: 'none',
                margin: '0 0 0 0',
                padding: '0 0 0 0',
                items: [{
                    xtype: 'ZonasCombo',
                    id: 'hfoformZona',
                    allowBlank: true,
                    disabled: true,
                    flex: 1,
                    margin: '0 10 0 0',
                    editable: true,
                    name: 'zona',
                    labelAlign: 'top',
                    fieldLabel: 'Zona(s)',
                    emptyText: 'Todas'
                 },{
                    xtype: 'ObjectsCombo',
                    id: 'hfoformObjeto',
                    allowBlank: true,
                    disabled: true,
                    width: 420,
                    editable: true,
                    labelAlign: 'top',
                    margin: '0 0 10 0',
                    name: 'objeto',
                    fieldLabel: 'Objeto(s)',
                    emptyText: 'Todos'
                 }]
            },{
                xtype: 'fieldset',
                flex: 1,
                collapsible: true,
                collapsed: false,
                title: 'Rango de Fecha del Informe',
                layout: 'anchor',
                padding: '0 20 0 20',
                margin: '5 0 0 0',
                defaults: {
                        anchor: '100%',
                        labelWidth: 100,
                        hideEmptyLabel: true
                },
                items: [{
                    xtype: 'fieldcontainer',
                    layout: 'hbox',
                    //height: 45,
                    msgTarget: 'none',
                    padding: 0,
                    items: [{
                        xtype: 'checkboxfield',
                        margin: '0 10 10 0',
                        allowBlank: false,
                        name: 'rango',
                        checked: true,
                        flex: 1,
                        boxLabel: 'Sin rango de fecha',
                        listeners: {
                            'change': function(checkbox, newValue) {
                                if (newValue === true) {
                                    Ext.getCmp('codirDesde').setDisabled(true);
                                    Ext.getCmp('codirHasta').setDisabled(true);
                                }
                                else {
                                    Ext.getCmp('codirDesde').setDisabled(false);
                                    Ext.getCmp('codirHasta').setDisabled(false);
                                }
                            }
                        }
                    },{
                        xtype: 'datefield',
                        id: 'codirDesde',
                        disabled: true,
                        editable: false,
                        width: 170,
                        allowBlank: false,
                        name: 'desde',
                        emptyText: 'Fecha inicio',
                        fieldLabel: 'Desde',
                        //labelAlign: 'top',
                        labelWidth: 40,
                        format: 'd/m/Y',
                        submitFormat: 'Y-m-d',
                        margin: '0 20 10 0'
                    },{
                        xtype: 'datefield',
                        id: 'codirHasta',
                        disabled: true,
                        editable: false,
                        width: 170,
                        allowBlank: false,
                        name: 'hasta',
                        emptyText: 'Fecha final',
                        fieldLabel: 'Hasta',
                        //labelAlign: 'top',
                        labelWidth: 40,
                        format: 'd/m/Y',
                        submitFormat: 'Y-m-d',
                        margin: '0 0 10 0'
                    }]
                }] 
            }]
        }];
         
    this.dockedItems = [{
        xtype: 'toolbar',
        dock: 'bottom',
        id: 'buttons',
        ui: 'footer',
        items: ['->', {
            cls: 'app-form-btn',
            text: '<i class="fas fa-check"></i>&nbsp;Aceptar',
            action: 'guardar',
            id: 'guardar'
        },{
            cls: 'app-form-btn',
            text: '<i class="fas fa-times"></i>&nbsp;Cancelar',
            scope: this,
            handler: this.close
        }]
    }];
 
    this.callParent(arguments);
    }
});