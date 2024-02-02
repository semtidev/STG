Ext.define('SEMTI.view.administracion.PortadaForm', {
    extend: 'Ext.window.Window',
    alias : 'widget.portadaform',    
    requires: ['Ext.form.Panel','Ext.form.field.Text','Ext.form.field.ComboBox'],    
    layout: 'fit',
    autoShow: true,
    width: 500,
	modal: true,
	
	title: 'Tipo de Portada',
     
    iconCls: 'icon_portada',
    
    initComponent: function() {
        this.items = [{
        	xtype: 'form',
            padding: '15 15 15 15',
            border: false,
			modal: true,
            style: 'background-color: #fff;',
                 
            fieldDefaults: {
            	anchor: '100%',
                labelAlign: 'left',
				labelWidth: 110,
                combineErrors: true,
                msgTarget: 'side'
            },
            items: [{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                height: 25,
                msgTarget: 'none',
                padding: 0,
                items: [{
                    xtype: 'checkboxfield',
                    allowBlank: false,
                    name: 'presentacion',
                    id: 'type_show_form',
                    fieldLabel: 'Presentaci\xF3n',
                    flex: 1,
                    margin: '0 0 0 35',
                    labelAlign: 'right',
                    listeners: {
                        'change': function(checkbox, newValue) {
                            if (newValue === true) {
                                Ext.getCmp('type_panelcontrol_form').setValue(false);
                            }
                            else {
                                Ext.getCmp('type_panelcontrol_form').setValue(true);
                            }
                        }
                    }
                },{
                    xtype: 'checkboxfield',
                    allowBlank: false,
                    name: 'panelcontrol',
                    id: 'type_panelcontrol_form',
                    fieldLabel: 'Panel de Control',
                    flex: 1,
                    margin: '0 20 0 75',
                    labelAlign: 'right',
                    listeners: {
                        'change': function(checkbox, newValue) {
                            if (newValue === true) {
                                Ext.getCmp('type_show_form').setValue(false);
                            }
                            else {
                                Ext.getCmp('type_show_form').setValue(true);
                            }
                        }
                    }
                }]
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                height: 180,
                msgTarget: 'none',
                padding: 0,
                items: [{
                    xtype: 'component',
                    width: 200,
                    height: 180,
                    cls: 'component_type_show',
                    margin: '0 10 0 15'
                },{
                    xtype: 'component',
                    width: 200,
                    height: 180,
                    cls: 'component_type_controlpanel',
                    margin: '0 0 0 20'
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
                action: 'guardar'
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