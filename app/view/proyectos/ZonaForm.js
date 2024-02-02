Ext.define('SEMTI.view.proyectos.ZonaForm', {
    extend: 'Ext.window.Window',
    alias : 'widget.zonaform',
 
    requires: ['Ext.form.Panel','Ext.form.field.Text'],
 
    layout: 'fit',
    autoShow: true,
    width: 500,
	modal: true,
	     
    iconCls: 'icon_zonas',
 
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
				labelWidth: 90,
                combineErrors: true,
                msgTarget: 'side'
            },
            items: [{
				xtype: 'textfield',
				name: 'id',
				hidden: true
             },{
				xtype: 'textfield',
				name: 'id_parent',
				hidden: true
             },{
				xtype: 'textfield',
				allowBlank: false,
				fieldLabel: 'Nombre',
				emptyText: 'Nombre de la Zona',
				name: 'text'
             },{
                xtype: 'datefield',
				editable: false,
				allowBlank: false,
				name: 'fecha_ini',
				emptyText: 'Fecha en que inicia el periodo de Garant\xEDa',
				fieldLabel: 'Inicio Garant\xEDa',
				format: 'd/m/Y',
				submitFormat: 'Y-m-d'
            },{
                xtype: 'datefield',
				editable: false,
				allowBlank: false,
				name: 'fecha_fin',
				emptyText: 'Fecha en que termina el periodo de Garant\xEDa',
				fieldLabel: 'Final Garant\xEDa',
				format: 'd/m/Y',
				submitFormat: 'Y-m-d'
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