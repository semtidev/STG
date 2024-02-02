Ext.define('SEMTI.view.administracion.ParametrosForm', {
    extend: 'Ext.window.Window',
    alias : 'widget.parametrosform',
 
    requires: ['Ext.form.Panel','Ext.form.field.Text','Ext.form.field.ComboBox'],
 
    layout: 'fit',
    autoShow: true,
    width: 450,
	modal: true,
	
	title: 'Par\xE1metros Generales',
     
    iconCls: 'icon_dptos',
 
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
				xtype: 'textfield',
				allowBlank: true,
				fieldLabel: 'IP del servidor',
				name: 'ipserver',
                emptyText: 'localhost'
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