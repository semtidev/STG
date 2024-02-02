Ext.define('SEMTI.view.administracion.PerfilesForm', {
    extend: 'Ext.window.Window',
    alias: 'widget.perfilesform',
    requires: ['Ext.form.Panel', 'Ext.form.field.Text', 'Ext.form.field.ComboBox'],
    layout: 'fit',
    autoShow: true,
    width: 900,
    modal: true,
    iconCls: 'icon-perfil',
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                border: false,
                modal: true,
                style: 'background-color: #fff;',
                fieldDefaults: {
                    anchor: '100%',
                    labelAlign: 'left',
                    labelWidth: 80,
                    combineErrors: true,
                    msgTarget: 'side'
                },
                items: [{
                        xtype: 'textfield',
                        name: 'id',
                        fieldLabel: 'id',
                        hidden: true
                    }, {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        height: 70,
                        msgTarget: 'none',
                        padding: 0,
                        items: [{
                                xtype: 'textfield',
                                name: 'nombre',
                                id: 'perfilesFormNombre',
                                allowBlank: false,
                                flex: 1,
                                maxLength: 70,
                                margin: '15 8 15 20',
                                fieldLabel: 'Nombre',
                                labelAlign: 'top',
                                rows: 2

                            }, {
                                xtype: 'textfield',
                                allowBlank: true,
                                flex: 2,
                                margin: '15 20 10 8',
                                fieldLabel: 'Descripci\xF3n',
                                labelAlign: 'top',
                                name: 'descripcion',
                                rows: 2
                            }]
                    }, {
                        xtype: 'label',
                        text: 'Permisos del Rol:',
                        margin: '0 0 0 20',
                    }, {
                        xtype: 'treesistemasadmin',
                        name: 'paginas',
                        margin: '5 20 20 20',
                        border: true
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
                    }, {
                        cls: 'app-form-btn',
                        text: '<i class="fas fa-times"></i>&nbsp;Cancelar',
                        scope: this,
                        handler: this.close
                    }]
            }];

        this.callParent(arguments);
    }
});