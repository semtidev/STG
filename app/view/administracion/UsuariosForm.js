Ext.define('SEMTI.view.administracion.UsuariosForm', {
    extend: 'Ext.window.Window',
    alias: 'widget.usuariosform',
    requires: [
        'Ext.form.Panel',
        'Ext.form.field.Text',
        'Ext.ux.form.ItemSelector',
        'SEMTI.view.administracion.TipoPortadaCombo'
    ],
    layout: 'fit',
    autoShow: true,
    width: 800,
    height: 640,
    modal: true,
    iconCls: 'icon-user-add',
    initComponent: function() {
        this.items = [{
                xtype: 'tabpanel',
                items: [{
                    xtype: 'form',
                    title: 'Datos Generales',
                    id: 'UserForm',
                    border: false,
                    modal: true,
                    style: 'background-color: #fff;',
                    fieldDefaults: {
                        anchor: '100%',
                        labelAlign: 'left',
                        labelWidth: 75,
                        combineErrors: true,
                        msgTarget: 'side'
                    },
                    items: [{
                            xtype: 'fieldset',
                            flex: 1,
                            collapsible: true,
                            collapsed: false,
                            title: 'Informaci\xF3n de Usuario',
                            //defaultType: 'datefield', // each item will be a checkbox
                            layout: 'anchor',
                            padding: '15 20 15 20',
                            margin: '15 20 15 20',
                            defaults: {
                                    anchor: '100%',
                                    labelWidth: 100,
                                    hideEmptyLabel: true
                            },
                            items: [{
                                xtype: 'textfield',
                                name: 'id_usuario',
                                fieldLabel: 'id',
                                hidden: true
                            },{
                                xtype: 'textfield',
                                name: 'avatar',
                                fieldLabel: 'avatar',
                                hidden: true
                            },{
                                xtype: 'fieldcontainer',
                                combineErrors: true,
                                msgTarget: 'none',
                                layout: 'hbox',
                                items: [{
                                        xtype: 'poloscombo',
                                        id: 'userpolocombo',
                                        allowBlank: false,
                                        flex: 1,
                                        margin: '0 20 5 0',
                                        name: 'polo',
                                        emptyText: 'Elija el Polo del usuario',
                                        fieldLabel: 'Polo'
                                     },{
                                        xtype: 'TipoPortadaCombo',
                                        id: 'UsersFormPortada',
                                        allowBlank: false,
                                        flex: 1,
                                        editable: false,
                                        labelWidth: 110,
                                        margin: '0 0 5 0',
                                        name: 'portada',
                                        emptyText: 'Portada del Sistema',
                                        fieldLabel: 'Tipo de Portada',
                                        afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="Required"> *</span>'
                                    }]
                            },{
                                xtype: 'fieldcontainer',
                                combineErrors: true,
                                msgTarget: 'none',
                                layout: 'hbox',
                                items: [{
                                    xtype: 'textfield',
                                    allowBlank: false,
                                    name: 'usuario',
                                    id: 'UsersFormUsuario',
                                    width: 344,
                                    margin: '0 10 5 0',
                                    fieldLabel: 'Usuario',
                                    //labelWidth: 60,
                                    emptyText: 'Cuenta de usuario de Windows',
                                    afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="Required"> *</span>'
                                }, {
                                    xtype: 'datefield',
                                    editable: true,
                                    flex: 1,
                                    margin: '0 10 5 0',
                                    allowBlank: true,
                                    name: 'expira',
                                    //labelAlign: 'top',
                                    fieldLabel: 'Expira',
                                    labelWidth: 50,
                                    labelAlign: 'right',
                                    format: 'd/m/Y',
                                    submitFormat: 'Y-m-d',
                                    emptyText: 'Para usuarios temporales'
                                },{
                                    xtype: 'checkboxfield',
                                    name: 'activo',
                                    fieldLabel: 'Activo',
                                    labelWidth: 50,
                                    width: 70,
                                    margin: '0 0 5 0',
                                    labelAlign: 'right',
                                    checked: true
                                }]
                            },{
                                xtype: 'fieldcontainer',
                                combineErrors: true,
                                msgTarget: 'none',
                                layout: 'hbox',
                                items: [{
                                        xtype: 'textfield',
                                        allowBlank: true,
                                        name: 'password',
                                        id: 'UsersFormPassword',
                                        inputType: 'password',
                                        width: 300,
                                        margin: '0 20 5 0',
                                        labelWidth: 90,
                                        emptyText: 'Contrase\xF1a de acceso',
                                        fieldLabel: 'Contrase\xF1a'
                                    },{
                                        xtype: 'textfield',
                                        allowBlank: true,
                                        id: 'UsersFormPassword2',
                                        inputType: 'password',
                                        flex: 1,
                                        labelWidth: 150,
                                        margin: '0 0 5 0',
                                        name: 'password2',
                                        emptyText: 'Repita la contrase\xF1a',
                                        fieldLabel: 'Confirmar Contrase\xF1a'
                                    }]
                            },{
                                xtype: 'itemselector',
                                style: 'margin-top: 10',
                                name: 'perfiles',
                                id: 'itemselector-field',
                                anchor: '100%',
                                fieldLabel: 'Perfil(es)',
                                labelWidth: 75,
                                //imagePath: './js/extjs4/includes/ux/css/images/',
                                store: Ext.create('SEMTI.store.Perfilescombo').load(),
                                displayField: 'nombre',
                                valueField: 'id',
                                //value: ['3', '4', '6'],
                                //value: ['4'],
                                allowBlank: false,
                                msgTarget: 'side',
                                fromTitle: 'Perfiles Disponibles',
                                toTitle: 'Perfil(es) del Usuario',
                                afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="Required"> *</span>'
                            }] 
                        },{
                            xtype: 'fieldset',
                            flex: 1,
                            collapsible: true,
                            collapsed: false,
                            title: 'Informaci\xF3n de Contacto',
                            //defaultType: 'datefield', // each item will be a checkbox
                            layout: 'anchor',
                            padding: '15 20 15 20',
                            margin: '0 20 20 20',
                            defaults: {
                                    anchor: '100%',
                                    labelWidth: 100,
                                    hideEmptyLabel: true
                            },
                            items: [{
                                    xtype: 'fieldcontainer',
                                    combineErrors: true,
                                    msgTarget: 'none', // qtip  title  under
                                    //fieldLabel: 'Your Name',
                                    //labelStyle: 'font-weight:bold;padding:0;',
                                    layout: 'hbox',
                                    //defaultType: 'textfield',
            
                                    items: [{
                                            xtype: 'textfield',
                                            id: 'usuariosFormNombre',
                                            allowBlank: false,
                                            fieldLabel: 'Nombre',
                                            labelWidth: 60,
                                            flex: 1,
                                            margin: '0 10 5 0',
                                            emptyText: 'Nombre(s)',
                                            name: 'nombre',
                                            afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="Required"> *</span>'
                                        }, {
                                            xtype: 'textfield',
                                            allowBlank: false,
                                            fieldLabel: 'Apellidos',
                                            flex: 1,
                                            margin: '0 0 5 0',
                                            labelAlign: 'right',
                                            emptyText: 'Apellidos',
                                            name: 'apellidos',
                                            afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="Required"> *</span>'
                                        }]
                                },  {
                                    xtype: 'fieldcontainer',
                                    combineErrors: true,
                                    msgTarget: 'none', // qtip  title  under
                                    //fieldLabel: 'Your Name',
                                    //labelStyle: 'font-weight:bold;padding:0;',
                                    layout: 'hbox',
                                    //defaultType: 'textfield',
            
                                    items: [{
                                            xtype: 'textfield',
                                            allowBlank: false,
                                            name: 'cargo',
                                            flex: 1,
                                            margin: '0 10 5 0',
                                            labelWidth: 60,
                                            fieldLabel: 'Cargo',
                                            emptyText: 'Cargo que desempe\xF1a en la empresa',
                                            afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="Required"> *</span>'
                                        },{
                                            xtype: 'textfield',
                                            allowBlank: false,
                                            fieldLabel: 'Correo',
                                            flex: 1,
                                            margin: '0 0 5 0',
                                            labelAlign: 'right',
                                            name: 'email',
                                            vtype:'email',
                                            emptyText: 'Direcci\xF3n de correo electr\xF3nico',
                                            afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="Required"> *</span>'
                                        }]
                                },  {
                                    xtype: 'fieldcontainer',
                                    combineErrors: true,
                                    msgTarget: 'none',
                                    layout: 'hbox',
                                    margin: '0 0 0 0',        
                                    items: [{
                                            xtype: 'filefield',
                                            id: 'user-avatarfile',
                                            width:353,
                                            emptyText: 'Imagen 100x100',
                                            labelWidth: 60,
                                            fieldLabel: '\xC1vatar',
                                            name: 'newavatar',
                                            buttonText: 'Buscar...'
                                        },{
                                            xtype: 'checkboxfield',
                                            name: 'notificaciones',
                                            labelWidth: 265,
                                            fieldLabel: 'Recibir Notificaciones por Correo Electr\xF3nico',
                                            width: 280,
                                            margin: '0 0 0 70',
                                            labelAlign: 'right'
                                        }]
                                }]
                        }],
                        dockedItems: [{
                            xtype: 'toolbar',
                            dock: 'bottom',
                            ui: 'footer',
                            //id: 'buttons',
                            items: ['->', {
                                cls: 'app-form-btn',
                                text: 'Siguiente&nbsp;<i class="fas fa-arrow-right"></i>',
                                id: 'userform_btn_next',
                                disabled: true,
                                formBind: true,  // Activa el boton si el form es válido
                                handler: function(button){
                                            var win = button.up('window'),
                                                tab = win.down('tabpanel');
                                            Ext.getCmp('userformTabProyectos').setDisabled(false);
                                            Ext.getCmp('userform_btn_end').setDisabled(false);
                                            tab.setActiveTab(1);
                                        }
                            }, {
                                cls: 'app-form-btn',
                                text: '<i class="fas fa-times"></i>&nbsp;Cancelar',
                                margin: '0 10 0 0',
                                scope: this,
                                handler: this.close
                            }]
                        }]
                },
                {
                    xtype: 'treeusuarioproyectos',
                    id: 'userformTabProyectos',
                    title: 'Permisos',
                    height: 332,
                    disabled: true,
                    dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'bottom',
                        ui: 'footer',
                        //id: 'buttons',
                        items: ['->', {
                            cls: 'app-form-btn',
                            text: '<i class="fas fa-arrow-left"></i>&nbsp;Anterior',
                            id: 'userform_btn_back',
                            handler: function(button){
                                var win = button.up('window'),
                                    tab = win.down('tabpanel');
                                Ext.getCmp('userform_btn_end').setDisabled(false);
                                tab.setActiveTab(0);
                            }
                        }, {
                            cls: 'app-form-btn',
                            text: '<i class="fas fa-check"></i>&nbsp;Finalizar',
                            id: 'userform_btn_end',
                            action: 'guardar',
                            disabled:true
                        }, {
                            cls: 'app-form-btn',
                            text: '<i class="fas fa-times"></i>&nbsp;Cancelar',
                            margin: '0 10 0 0',
                            scope: this,
                            handler: this.close
                        }]
                    }]              
                }]
        }];

        /*this.dockedItems = [{
                xtype: 'toolbar',
                dock: 'bottom',
                id: 'buttons',
                ui: 'footer',
                items: ['->', ]
            }];*/

        this.callParent(arguments);

        let formproyecto_polo = Ext.getCmp('userpolocombo'),
            store = formproyecto_polo.getStore();
        store.getProxy().setExtraParam('action', 'UserForm');
        store.load();
    }
});