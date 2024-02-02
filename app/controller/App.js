Ext.define('SEMTI.controller.App', {
    extend: 'Ext.app.Controller',
    models: ['TreeSistemas', 'Treeseguimiento', 'Mensajes'],
    stores: ['TreeSistemas', 'Treeseguimiento'],
    views: [
        'App',
        'sistema.HeaderApp',
        'sistema.MainAccordion',
        'portada.Portada',
        'portada.TreeSistemas',
        'portada.TreeSeguimiento',
        'administracion.PortadaForm',
        'administracion.PasswordForm',
        'sistema.Mensajes',
        'sistema.MsgSDPendientes',
        'sistema.UserPerfil'
    ],
    refs: [{
            ref: 'mensajesGrid',
            selector: 'mensajes'
        }],
    init: function() {

        this.control({
            'treesistemas': {
                itemclick: this.openSystem
            },
            'treeseguimiento': {
                itemclick: this.openSystem
            },
            'passwordform button[action=guardar]': {
                click: this.cambiarPassword
            },
            'portadaform button[action=guardar]': {
                click: this.cambiarPortada
            },
            'mensajes dataview': {
                itemdblclick: this.abrirMensaje
            },
            'mensajes button': {
                click: this.listarMensajes
            },
            'userperfil': {
                afterrender: this.loadUserperfil
            },
            'userperfil filefield': {
                change: this.changeAvatar
            },
            'userperfil checkboxfield': {
                change: this.changeNotific
            },
        });
    },
    
    loadUserperfil: function(panel) {

        var avatarUser  = Ext.getCmp('avatarUser'),
            nameUser    = Ext.getCmp('nameUser'),
            loginUser   = Ext.getCmp('loginUser'),
            rolUser     = Ext.getCmp('rolUser'),
            emailUser   = Ext.getCmp('emailUser'),
            avatarName  = currentUserData.get('avatar'),
            antiCache   = (new Date()).getTime(),
            newSrc      = avatarName + '?dc=' + antiCache;;
                
        avatarUser.update('<span><img src="resources/images/users/' + newSrc + '" width="100" height="100"/></span>');
        nameUser.update(currentUserData.get('nombre') +' '+ currentUserData.get('apellidos'));
        loginUser.update(currentUserData.get('usuario'));
        rolUser.update(currentUserData.get('cargo'));
        emailUser.update(currentUserData.get('email'));        
    },

    changeAvatar: function(filefield,value){

        var win          = filefield.up('window'),
            form         = win.down('form'),
            avatarPerfil = Ext.getCmp('avatarUser'),
            avatarNavtop = Ext.Element.get('currentuser_avatar');
        
        form.getForm().submit({
            method: 'POST',
            submitEmptyText: false,
            url: './php/sistema/SystemActions.php',
            waitTitle: 'Espere', //Titulo del mensaje de espera
            waitMsg: 'Procesando...', //Mensaje de espera
            params: {
                accion: 'UpdateCurrentUser'
            },
            success: function(form, action) {
                
                var data       = Ext.decode(action.response.responseText),
                    avatarArr  = data.avatar.split('"'),
                    avatarName = avatarArr[1],
                    antiCache  = (new Date()).getTime(),
                    newSrc     = avatarName + '?dc=' + antiCache;

                avatarPerfil.update('<span><img src="resources/images/users/' + newSrc + '" width="100" height="100"/></span>');
                avatarNavtop.setHTML('<img src="resources/images/users/' + newSrc + '"/>');
            },
            failure: function(form, action) {
                
                var data = Ext.decode(action.response.responseText);

                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: data.message,
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        });    
    },

    changeNotific: function(check,newvalue){

        var win  = check.up('window'),
            form = win.down('form');
        
        form.getForm().submit({
            method: 'POST',
            submitEmptyText: false,
            url: './php/sistema/SystemActions.php',
            params: {
                accion: 'UpdateCurrentUser'
            },
            failure: function(form, action) {
                var data = Ext.decode(action.response.responseText);

                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: data.message,
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        });    
    },

    listarMensajes: function(button) {

        var selection = button.getItemId();

        if (!(selection instanceof Object)) {

            //alert(selection);

            /*var CttosmyGroup = Ext.getCmp('ListarContratosGroup').getChecked()[0];
             var listar  = CttosmyGroup.boxLabel;*/

            var store = this.getMensajesStore();
            store.getProxy().setExtraParam("listar", selection);
            store.load();

        }
        //Ext.Msg.alert('alerta',"Label is: " + myGroup.boxLabel + " and Value is: " + myGroup.getGroupValue());
    },

    abrirMensaje: function(grid, record) {

        records = this.getMensajesGrid().getSelectionModel().getSelection()[0];

        if (records.getData().descripcion == 'SD Pendientes por Solución') {

            var mensaje = Ext.create('SEMTI.view.sistema.MsgSDPendientes');
            mensaje.show();
        }
    },
    cambiarPassword: function(button) {

        var win = button.up('window'),
                form = win.down('form');

        if (form.isValid()) {

            form.getForm().submit({
                //target : '_blank', 
                method: 'POST',
                //standardSubmit:true, 
                url: './php/administracion/CambiarPassword.php',
                waitTitle: 'Espere', //Titulo del mensaje de espera
                waitMsg: 'Enviando datos...', //Mensaje de espera
                params: {
                    accion: 'Actualizar'
                },
                success: function() {
                    win.close();
                    Ext.MessageBox.show({
                        title: 'Mensaje del Sistema',
                        msg: 'Ha sido cambiada su contrase\xF1a satisfactoriamente.',
                        icon: Ext.MessageBox.INFO,
                        buttons: Ext.Msg.OK
                    });
                },
                failure: function(form, action) {
                    var data = Ext.decode(action.response.responseText);

                    Ext.MessageBox.show({
                        title: 'Mensaje del Sistema',
                        msg: data.message,
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.Msg.OK
                    });
                }
            });
        }
    },
    cambiarPortada: function(button) {

        var win  = button.up('window'),
            form = win.down('form');
        
        form.getForm().submit({
            method: 'POST',
            url: './php/sistema/SystemActions.php',
            params: {
                accion: 'UpdatePortada'
            },
            success: function() {
                win.setLoading('Actualizando portada...');
                window.location.reload();
            },
            failure: function(form, action) {
                var data = Ext.decode(action.response.responseText);

                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: data.message,
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        });
    },
    openSystem: function(t, record, item, index) {

        var itemid = record.get('id');
        if (!Ext.getCmp('PTpanel').getChildByElement('tabsyst' + itemid)) {

            ////////////////////////////////
            // ENLACES DEL MENU SISTEMAS  //
            ////////////////////////////////

            if (record.get('text') == 'Cambiar Contrase\xF1a') {

                var password = Ext.create('SEMTI.view.administracion.PasswordForm');
                password.show();

                Ext.getCmp('PasswordFormUsuario').setValue(Ext.Element.get('usuario').getHTML());
            }
            if (record.get('text') == 'Roles y Permisos') {

                // Cargar dinamicamente el controlador
                /*var controlador = this.getApplication().getController('Contratacion');
                 controlador.init();*/

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'perfiles',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Usuarios') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'usuarios',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Solicitudes de Defectaci\xF3n') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'gtiasd',
                    id: 'tabsyst' + itemid,
                    iconCls: 'icon_SD',
                    closable: true
                });
            }
            
            if (record.get('text') == 'Departamentos') {

                var dptos = Ext.create('SEMTI.view.sistema.DptosWindows');
                dptos.show();
            }
            
            if (record.get('text') == 'Tipos de Problemas') {

                var problemas = Ext.create('SEMTI.view.garantia.GtiaproblemasWindows');
                problemas.show();
            }
            
            if (record.get('text') == 'Par\xE1metros Generales') {
        
                var parametros = Ext.create('SEMTI.view.administracion.ParametrosForm');
                parametros.show();
        
                var form = parametros.down('form');
        
                form.getForm().load({
                    url: './php/sistema/SystemActions.php',
                    method: 'POST',
                    params: {
                        accion: 'LoadConfig'
                    },
                    failure: function(form, action) {
                        editar.close();
                        Ext.Msg.alert("Carga Fallida", "La carga de los parametros no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
                    }
                });                
            }
            
            if (record.get('text') == 'Tipo de Portada') {
        
                var parametros = Ext.create('SEMTI.view.administracion.PortadaForm');
                parametros.show();
        
                var form = parametros.down('form');
        
                form.getForm().load({
                    url: './php/sistema/SystemActions.php',
                    method: 'POST',
                    params: {
                        accion: 'LoadPortada'
                    },
                    failure: function(form, action) {
                        editar.close();
                        Ext.Msg.alert("Carga Fallida", "La carga de los parametros no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
                    }
                });                
            }
            
            if (record.get('text') == 'SD Por Resolver') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimSDPendientes',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'SD  Const/Sumin/AEH') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimSDConSumAEH',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Demora Prom en SD') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimDemoraProm',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Demora Prom en SD AEH') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimDemoraPromAEH',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Demora Prom SD Const, no AEH y no Sumin') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimDemoraPromConNoAEHNoSum',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Demora Prom SD Const, AEH y no Sumin') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimDemoraPromConAEHNoSum',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Demora Prom SD Const, AEH y Sumin') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimDemoraPromConAEHSum',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Demora Prom SD Const, no AEH y Sumin') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimDemoraPromConNoAEHSum',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Relación Tipo Defecto / Reportes SD') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimTipoDefectoReportesSD',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'SD que No Proceden') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimSDNoProceden',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Comparativa de SD entre Proyectos') {

                Ext.getCmp('PTpanel').add({
                    title: record.get('text'),
                    xtype: 'GtiaSeguimComparativa',
                    id: 'tabsyst' + itemid,
                    closable: true
                });
            }
            if (record.get('text') == 'Habitaciones Fuera de Orden') {

                var hfo = Ext.create('SEMTI.view.informes.HfoWindow');
                hfo.show();
            }
            if (record.get('text') == 'Resumen de Garantía') {

                var codir = Ext.create('SEMTI.view.informes.ResumenWindow');
                codir.show();
            }
            //////////////////////////////////////////////			
        }
        Ext.getCmp('PTpanel').setActiveTab('tabsyst' + itemid);
    }

})