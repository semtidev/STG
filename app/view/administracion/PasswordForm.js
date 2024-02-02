Ext.define('SEMTI.view.administracion.PasswordForm', {
    extend: 'Ext.window.Window',
    alias : 'widget.passwordform',
 
    requires: ['Ext.form.Panel','Ext.form.field.Text','Ext.form.field.ComboBox'],
 
    layout: 'fit',
    autoShow: true,
    width: 400,
	modal: true,
	
	title: 'Cambiar Contrase\xF1a',
     
    iconCls: 'icon-password',
 
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
				labelWidth: 130,
                combineErrors: true,
                msgTarget: 'side'
            },
            items: [{
				xtype: 'textfield',
				allowBlank: false,
				fieldLabel: 'Usuario',
				name: 'usuario',
                value: localStorage.getItem('usuario'),
				id: 'PasswordFormUsuario'
             },{
				xtype: 'textfield',
				allowBlank: false,
				fieldLabel: 'Contrase\xF1a Anterior',
				inputType: 'password',
				name: 'oldpassword'
             },{
                xtype: 'textfield',
				allowBlank: false,
                name: 'newpassword1',
				inputType: 'password',
                fieldLabel: 'Nueva Contrase\xF1a'
            },{
                xtype: 'textfield',
				allowBlank: false,
                name: 'newpassword2',
				inputType: 'password',
                fieldLabel: 'Repetir Contrase\xF1a'
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