Ext.define('SEMTI.controller.Administracion', {
    extend: 'Ext.app.Controller',
    stores: ['Usuarios','Perfiles','Perfilescombo','Treesistemasadmin','ProyectsComboAll','Treeusuarioproyectos'],
    models: ['Usuarios','Perfiles','Perfilescombo','TreeSistemas','TreeSistemasAdmin','ProyectsCombo','Treeusuarioproyectos'],
    views: [
        'sistema.Poloscombo',
        'administracion.Usuarios',
        'administracion.UsuariosForm',
        'administracion.Perfiles',
        'administracion.PerfilesForm',
        'administracion.TreeSistemasAdmin',
        'administracion.ParametrosForm',
        'administracion.TreeUsuarioProyectos'
    ],

    refs: [{
            ref: 'tablaUsuarios',
            selector: 'usuarios'
        }, {
            ref: 'tablaPerfiles',
            selector: 'perfiles'
        }, {
            ref: 'treePerfiles',
            selector: 'treesistemasadmin'
        }, {
            ref: 'treeUsuarioproyectos',
            selector: 'treeusuarioproyectos'
        }, {
            ref: 'poloscombo',
            selector: 'poloscombo'
        }],

    init: function() {
        this.control({
            'parametrosform button[action=guardar]': {
                click: this.actualizarConfiguraciones
            },
            // USUARIOS
            'usuarios dataview': {
                itemdblclick: this.editarUsuario
            },
            'usuarios button[action=agregar]': {
                click: this.nuevoUsuario
            },
            'usuarios button[action=editar]': {
                click: this.editarUsuario
            },
            'usuarios button[action=eliminar]': {
                click: this.eliminarUsuario
            },
            'usuariosform button[action=guardar]': {
                click: this.actualizarUsuario
            },
            'usuarios button[action=imprimir]': {
                click: this.imprimirUsuarios
            },
            'treeusuarioproyectos dataview': {
                itemclick: this.usuarioproyectoSelectItem
            },
            'poloscombo': {
                change: this.usuarioPolo
            },
            // PERFILES
            'perfiles dataview': {
                itemdblclick: this.editarPerfil
            },
            'perfiles button[action=agregar]': {
                click: this.nuevoPerfil
            },
            'perfiles button[action=editar]': {
                click: this.editarPerfil
            },
            'perfiles button[action=eliminar]': {
                click: this.eliminarPerfil
            },
            'perfilesform button[action=guardar]': {
                click: this.actualizarPerfil
            },
            'perfiles button[action=imprimir]': {
                click: this.imprimirPerfiles
            },
            'treesistemasadmin dataview': {
                itemclick: this.perfilSelectItem
            },
            'treesistemasadmin #TreeSistemasAdminCheckModify': {
                checkchange: this.checkModifyTree
            },
            'treesistemasadmin #TreeSistemasAdminCheckReadExp': {
                checkchange: this.checkReadExpTree
            },
            'treesistemasadmin #TreeSistemasAdminCheckRead': {
                checkchange: this.checkReadTree
            },
            'treesistemasadmin #TreeSistemasAdminCheckWrite': {
                checkchange: this.checkWriteTree
            }            
        });
    },
    
    usuarioPolo: function(combo, newValue, oldValue, opts) {
        let usuariopolo = Ext.getCmp('userformTabProyectos'),
            store = usuariopolo.getStore();
        store.getProxy().setExtraParam('polo', newValue);
        store.load();
    },

    actualizarConfiguraciones: function(button) {

        var win    = button.up('window'),
            form   = win.down('form'),
            values = form.getValues();
        
        if (form.isValid()) {

            form.getForm().submit({
                //target : '_blank', 
                method: 'POST',
                //standardSubmit:true, 
                url: './php/sistema/SystemActions.php',
                waitTitle: 'Espere', //Titulo del mensaje de espera
                waitMsg: 'Processando...', //Mensaje de espera
                params: {
                    accion: 'UpdateConfig'
                },
                success: function() {
                    win.close();
                },
                failure: function(form, action) {
                    Ext.MessageBox.show({
                        title: 'Mensaje del Sistema',
                        msg: 'Ha ocurrido un error en la operaci\xF3n. Por favor, compruebe que sean v\xE1lidos todos sus datos, de continuar el problema consulte al Administrador del Sistema.',
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.Msg.OK
                    });
                }
            });
        }
    },
    
    /////////////////////////////////////
    ////          USUARIOS           ////
    /////////////////////////////////////
    nuevoUsuario: function(grid, record) {

        var agregar = Ext.create('SEMTI.view.administracion.UsuariosForm');
        agregar.setTitle('Nuevo Usuario');
        agregar.show();
    },
    
    editarUsuario: function(grid, record) {

        var grid       = this.getTablaUsuarios(),
            record     = grid.getSelectionModel().getSelection()[0],
            id_usuario = record.get('id_usuario');

        var editar = Ext.create('SEMTI.view.administracion.UsuariosForm');
        editar.setTitle('Modificar Usuario');
        editar.show();

        var form = editar.down('form'),
            tree = editar.down('treeusuarioproyectos');

        var treeStore = tree.getStore();
        treeStore.getProxy().setExtraParam("id_usuario", id_usuario);
        treeStore.load();

        form.getForm().load({
            url: './php/administracion/UsuariosActions.php',
            method: 'POST',
            params: {
                accion: 'LoadUsuario', id: id_usuario
            },
            failure: function(form, action) {
                editar.close();
                Ext.Msg.alert("Carga Fallida", "La carga de los parametros del Usuario no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
            }
        });

        /*var usuario = Ext.getCmp('UsersFormUsuario');
        usuario.setDisabled(true);*/
    },

    usuarioproyectoSelectItem: function(tree, record, item, index, eventObj, eOpts) {

        var node = tree.getTreeStore().getNodeById(record.internalId);

        if (eventObj.getTarget('.x-tree-checkbox', 1, true)) {

            var nodoid = record.get('id');

            if (!node.get('checked') == true) {
                var checked = true;
                node.set('lectura', true);
                node.set('checked', true);
            } else {
                var checked = false;
                node.set('checked', false);
                node.set('lectura', false);
                node.set("lectura_exportar", false);
                node.set("eliminar", false);
                node.set("escritura", false);
            }
        }
    },
    
    actualizarUsuario: function(button) {

        var win    = button.up('window'),
            form   = win.down('form'),
            record = form.getRecord(),
            values = form.getValues(),
            store  = this.getUsuariosStore(),
            tree   = this.getTreeUsuarioproyectos();

        if (form.isValid()) {

            // Validar contraseñas
            if (values.password != values.password2) {
                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: 'Ha ocurrido un error en la operaci\xF3n. Por favor, compruebe que las contraseñas sean iguales.',
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
                return;
            }
            
            var proyectos = tree.getChecked();       //getSelectionModel().getSelection();

            if (!Ext.isEmpty(proyectos)) {

                var recordsToSend = [],
                        count = 0,
                        param = '[';

                Ext.each(proyectos, function(record) {
                    if (parseInt(record.get('id'))) {
                        count++;
                        if (count == 1) {
                            param += '{"id":' + record.get('id') + ',"modificar":' + record.get('modificar') + ',"lectura_exportar":' + record.get('lectura_exportar') + ',"lectura":' + record.get('lectura') + ',"escritura":' + record.get('escritura') + '}'
                        }
                        else {
                            param += ',{"id":' + record.get('id') + ',"modificar":' + record.get('modificar') + ',"lectura_exportar":' + record.get('lectura_exportar') + ',"lectura":' + record.get('lectura') + ',"escritura":' + record.get('escritura') + '}'
                        }
                    }
                });

                param += ']';

                if (values.id_usuario > 0) { //Si Hay algun Valor, entra en Modo de Actualizacion


                    ////////////////////////////
                    ////      ACTUALIZAR    ////
                    ////////////////////////////

                    form.getForm().submit({
                        //target : '_blank', 
                        method: 'POST',
                        submitEmptyText: false,
                        //standardSubmit:true, 
                        url: './php/administracion/UsuariosActions.php',
                        waitTitle: 'Espere', //Titulo del mensaje de espera
                        waitMsg: 'Procesando datos...', //Mensaje de espera
                        params: {
                            accion: 'Actualizar', permisos: param
                        },
                        success: function() {
                            win.close();
                            store.load();
                        },
                        failure: function(form, action) {
                            Ext.MessageBox.show({
                                title: 'Mensaje del Sistema',
                                msg: 'Ha ocurrido un error en la operaci\xF3n. Por favor, compruebe que sean v\xE1lidos todos sus datos, de continuar el problema consulte al Administrador del Sistema.',
                                icon: Ext.MessageBox.ERROR,
                                buttons: Ext.Msg.OK
                            });
                        }
                    });
                }
                else { //De Lo contrario, si la accion fue para agregar, se inserta un registro


                    ////////////////////////////
                    ////      INSERTAR      ////
                    ////////////////////////////

                    form.getForm().submit({
                        //target : '_blank', 
                        method: 'POST',
                        submitEmptyText: false,
                        //standardSubmit:true, 
                        url: './php/administracion/UsuariosActions.php',
                        waitTitle: 'Espere', //Titulo del mensaje de espera
                        waitMsg: 'Procesando datos...', //Mensaje de espera
                        params: {
                            accion: 'Insertar', permisos: param
                        },
                        success: function() {
                            win.close();
                            store.load();
                        },
                        failure: function(form, action) {
                            Ext.MessageBox.show({
                                title: 'Mensaje del Sistema',
                                msg: 'Ha ocurrido un error en la operaci\xF3n. Por favor, compruebe que sean v\xE1lidos todos sus datos, de continuar el problema consulte al Administrador del Sistema.',
                                icon: Ext.MessageBox.ERROR,
                                buttons: Ext.Msg.OK
                            });
                        }
                    });
                }
            }
            else {
                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: 'Debe seleccionar al menos el permiso a un proyecto antes de procesar la informaci\xF3n del usuario.',
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        }
    },
        
    eliminarUsuario: function() {

        var grid   = this.getTablaUsuarios();
        var record = grid.getSelectionModel().getSelection()[0];
        var id     = record.get('id_usuario');
        var store  = this.getUsuariosStore();

        Ext.Msg.confirm("Eliminar Usuario del Sistema", "Este usuario  ser\xE1 eliminado definitivamente. Confirma que desea realizar esta operaci\xF3n?", function(btnText) {
            if (btnText === "yes") {

                Ext.Ajax.request({ //dispara la petición

                    url: './php/administracion/UsuariosActions.php',
                    method: 'POST',
                    waitTitle: 'Espere',
                    waitMsg: 'Enviando datos..',
                    params: {accion: 'Eliminar', id_usuario: id},
                    success: function(result, request) {

                        var jsonData = Ext.JSON.decode(result.responseText);

                        if (!jsonData.failure) {

                            store.load();
                        }
                        else {

                            Ext.MessageBox.show({
                                title: 'Mensaje del Sistema',
                                msg: jsonData.message,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    },
                    failure: function() {

                        Ext.MessageBox.show({
                            title: 'Mensaje del Sistema',
                            msg: 'Ha ocurrido un error en el Sistema. Por favor, vuelva a intentar realizar la operacion, de continuar el problema consulte al Administrador del Sistema.',
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                });
            }
        }, this);
    },
    
    imprimirUsuarios: function() {

        let ipserver = localStorage.getItem('ipserver');
        window.open('http://'+ipserver+'/semti.garantia/php/administracion/pdf_Usuarios.php', '_blank');
    },
    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////


    ///////////////////////////////////////////
    ////             PERFILES              ////
    ///////////////////////////////////////////
    nuevoPerfil: function(grid, record) {

        var tree = this.getTreesistemasadminStore();
        tree.getProxy().setExtraParam("id_perfil", 0);
        tree.load();

        var agregar = Ext.create('SEMTI.view.administracion.PerfilesForm'),
                nameField = Ext.getCmp('perfilesFormNombre');

        agregar.setTitle('Nuevo Rol de Usuario');
        agregar.show();
        nameField.focus();
    },
    editarPerfil: function(grid, record) {

        var grid = this.getTablaPerfiles();
        var record = grid.getSelectionModel().getSelection()[0];
        var id_perfil = record.get('id');

        var store = this.getTreesistemasadminStore();
        store.getProxy().setExtraParam("id_perfil", id_perfil);
        store.load();

        var editar = Ext.create('SEMTI.view.administracion.PerfilesForm'),
                nameField = Ext.getCmp('perfilesFormNombre');

        editar.setTitle('Modificar Rol');
        editar.show();
        nameField.focus();

        if (id_perfil != null || id_perfil != '') {

            var EditForm = editar.down('form');
            EditForm.loadRecord(record);
        }
    },
    perfilSelectItem: function(tree, record, item, index, eventObj, eOpts) {

        var node = tree.getTreeStore().getNodeById(record.internalId);

        if (eventObj.getTarget('.x-tree-checkbox', 1, true)) {

            var nodoid = record.get('id');

            if (!node.get('checked') == true) {
                var checked = true;
                node.set('lectura', true);
                node.set('checked', true);
                if (node.parentNode) {
                    var countchilds = -1,
                            countchecks = 0;
                    node.parentNode.cascadeBy(function(child) {
                        countchilds++;
                        if (child.get('checked') == true) {
                            countchecks++
                        }
                    });
                    if (countchilds == countchecks) {
                        node.parentNode.set("checked", true);
                        node.parentNode.set("lectura", true);
                    }
                }
                if (node.hasChildNodes()) {
                    node.cascadeBy(function(child) {
                        child.set("checked", true);
                        child.set("lectura", true);
                    });
                }
            } else {
                var checked = false;
                node.set('checked', false);
                node.set('lectura', false);
                node.set("lectura_exportar", false);
                node.set("modificar", false);
                node.set("escritura", false);
                if (node.parentNode) {
                    node.parentNode.set("checked", false);
                    node.parentNode.set("lectura", false);
                    node.parentNode.set("lectura_exportar", false);
                    node.parentNode.set("modificar", false);
                    node.parentNode.set("escritura", false);
                }
                if (node.hasChildNodes()) {
                    node.cascadeBy(function(child) {
                        child.set("checked", false);
                        child.set("lectura", false);
                        child.set("lectura_exportar", false);
                        child.set("modificar", false);
                        child.set("escritura", false);
                    });
                }
            }
        }
    },
    checkModifyTree: function(checkcolumn, rowIndex, checked, eOpts) {

        var tree = this.getTreePerfiles(),
                view = tree.getView(),
                record = view.getRecord(view.getNode(rowIndex)),
                node = view.getStore().getNodeById(record.internalId),
                countchilds = 0,
                countchecks = 0,
                countmodify = 0,
                countreadexp = 0,
                countread = 0,
                countwrite = 0;

        if (node.hasChildNodes()) {
            node.cascadeBy(function(child) {
                child.set('modificar', checked);
                if (checked == true) {
                    child.set('checked', checked);
                }
                else {
                    countchilds++;
                    if (child.get('modificar') == false) {
                        countmodify++
                    }
                    if (child.get('lectura_exportar') == false) {
                        countreadexp++
                    }
                    if (child.get('lectura') == false) {
                        countread++
                    }
                    if (child.get('escritura') == false) {
                        countwrite++
                    }
                    if (countchilds == countmodify == countreadexp == countread == countwrite) {
                        child.set('checked', false);
                    }
                }
            });

        }
        else {
            if (checked != true) {
                if (node.parentNode) {
                    node.parentNode.set('modificar', checked);
                }
            }
            else {
                if (node.parentNode) {
                    node.parentNode.cascadeBy(function(child) {
                        countchilds++;
                        if (child.get('modificar') == true) {
                            countmodify++
                        }
                    });
                    if (countchilds == countmodify) {
                        node.parentNode.set("modificar", true);
                    }
                }
            }
        }
    },
    checkReadExpTree: function(checkcolumn, rowIndex, checked, eOpts) {

        var tree = this.getTreePerfiles(),
                view = tree.getView(),
                record = view.getRecord(view.getNode(rowIndex)),
                node = view.getStore().getNodeById(record.internalId),
                countchilds = 0,
                countchecks = 0,
                countmodify = 0,
                countreadexp = 0,
                countread = 0,
                countwrite = 0;

        if (node.hasChildNodes()) {
            node.cascadeBy(function(child) {
                child.set('lectura_exportar', checked);
                if (checked == true) {
                    child.set('checked', checked);
                }
                else {
                    countchilds++;
                    if (child.get('modificar') == false) {
                        countmodify++
                    }
                    if (child.get('lectura_exportar') == false) {
                        countreadexp++
                    }
                    if (child.get('lectura') == false) {
                        countread++
                    }
                    if (child.get('escritura') == false) {
                        countwrite++
                    }
                    if (countchilds == countmodify == countreadexp == countread == countwrite) {
                        child.set('checked', false);
                    }
                }
            });

        }
        else {
            if (checked != true) {
                if (node.parentNode) {
                    node.parentNode.set('lectura_exportar', checked);
                }
            }
            else {
                if (node.parentNode) {
                    node.parentNode.cascadeBy(function(child) {
                        countchilds++;
                        if (child.get('lectura_exportar') == true) {
                            countmodify++
                        }
                    });
                    if (countchilds == countmodify) {
                        node.parentNode.set("lectura_exportar", true);
                    }
                }
            }
        }
    },
    checkReadTree: function(checkcolumn, rowIndex, checked, eOpts) {

        var tree = this.getTreePerfiles(),
                view = tree.getView(),
                record = view.getRecord(view.getNode(rowIndex)),
                node = view.getStore().getNodeById(record.internalId),
                countchilds = 0,
                countchecks = 0,
                countmodify = 0,
                countreadexp = 0,
                countread = 0,
                countwrite = 0;

        if (node.hasChildNodes()) {
            node.cascadeBy(function(child) {
                child.set('lectura', checked);
                if (checked == true) {
                    child.set('checked', checked);
                }
                else {
                    countchilds++;
                    if (child.get('modificar') == false) {
                        countmodify++
                    }
                    if (child.get('lectura_exportar') == false) {
                        countreadexp++
                    }
                    if (child.get('lectura') == false) {
                        countread++
                    }
                    if (child.get('escritura') == false) {
                        countwrite++
                    }
                    if (countchilds == countmodify == countreadexp == countread == countwrite) {
                        child.set('checked', false);
                    }
                }
            });

        }
        else {
            if (checked != true) {
                if (node.parentNode) {
                    node.parentNode.set('lectura', checked);
                }
            }
            else {
                if (node.parentNode) {
                    node.parentNode.cascadeBy(function(child) {
                        countchilds++;
                        if (child.get('lectura') == true) {
                            countmodify++
                        }
                    });
                    if (countchilds == countmodify) {
                        node.parentNode.set("lectura", true);
                    }
                }
            }
        }
    },
    checkWriteTree: function(checkcolumn, rowIndex, checked, eOpts) {

        var tree = this.getTreePerfiles(),
                view = tree.getView(),
                record = view.getRecord(view.getNode(rowIndex)),
                node = view.getStore().getNodeById(record.internalId),
                countchilds = 0,
                countchecks = 0,
                countmodify = 0,
                countreadexp = 0,
                countread = 0,
                countwrite = 0;

        if (node.hasChildNodes()) {
            node.cascadeBy(function(child) {
                child.set('escritura', checked);
                if (checked == true) {
                    child.set('checked', checked);
                }
                else {
                    countchilds++;
                    if (child.get('modificar') == false) {
                        countmodify++
                    }
                    if (child.get('lectura_exportar') == false) {
                        countreadexp++
                    }
                    if (child.get('lectura') == false) {
                        countread++
                    }
                    if (child.get('escritura') == false) {
                        countwrite++
                    }
                    if (countchilds == countmodify == countreadexp == countread == countwrite) {
                        child.set('checked', false);
                    }
                }
            });

        }
        else {
            if (checked != true) {
                if (node.parentNode) {
                    node.parentNode.set('escritura', checked);
                }
            }
            else {
                if (node.parentNode) {
                    node.parentNode.cascadeBy(function(child) {
                        countchilds++;
                        if (child.get('escritura') == true) {
                            countmodify++
                        }
                    });
                    if (countchilds == countmodify) {
                        node.parentNode.set("escritura", true);
                    }
                }
            }
        }
    },
    actualizarPerfil: function(button) {

        var win    = button.up('window'),
            form   = win.down('form'),
            values = form.getValues(),
            store  = this.getPerfilesStore(),
            tree   = this.getTreePerfiles();

        if (form.isValid()) {

            var permisos = tree.getChecked();       //getSelectionModel().getSelection();

            if (!Ext.isEmpty(permisos)) {

                var recordsToSend = [],
                        count = 0,
                        param = '[';

                Ext.each(permisos, function(record) {
                    if (parseInt(record.get('id'))) {
                        count++;
                        if (count == 1) {
                            param += '{"id":' + record.get('id') + ',"modificar":' + record.get('modificar') + ',"lectura_exportar":' + record.get('lectura_exportar') + ',"lectura":' + record.get('lectura') + ',"escritura":' + record.get('escritura') + '}'
                        }
                        else {
                            param += ',{"id":' + record.get('id') + ',"modificar":' + record.get('modificar') + ',"lectura_exportar":' + record.get('lectura_exportar') + ',"lectura":' + record.get('lectura') + ',"escritura":' + record.get('escritura') + '}'
                        }
                    }
                });

                param += ']';

                if (values.id > 0) { //Si Hay algun Valor, entra en Modo de Actualizacion


                    ////////////////////////////
                    ////      ACTUALIZAR    ////
                    ////////////////////////////

                    form.getForm().submit({
                        method: 'POST',
                        url: './php/administracion/PerfilesActions.php',
                        waitTitle: 'Espere', //Titulo del mensaje de espera
                        waitMsg: 'Enviando datos...', //Mensaje de espera
                        params: {
                            accion: 'Actualizar', permisos: param
                        },
                        success: function() {
                            win.close();
                            store.load();
                        },
                        failure: function(form, action) {
                            Ext.MessageBox.show({
                                title: 'Mensaje del Sistema',
                                msg: 'Ha ocurrido un error en la operaci\xF3n. Por favor, compruebe que sean v\xE1lidos todos sus datos, de continuar el problema consulte al Administrador del Sistema.',
                                icon: Ext.MessageBox.ERROR,
                                buttons: Ext.Msg.OK
                            });
                        }
                    });


                }
                else { //De Lo contrario, si la accion fue para agregar, se inserta un registro


                    ////////////////////////////
                    ////      INSERTAR      ////
                    ////////////////////////////

                    form.getForm().submit({
                        method: 'POST',
                        url: './php/administracion/PerfilesActions.php',
                        waitTitle: 'Espere', //Titulo del mensaje de espera
                        waitMsg: 'Enviando datos...', //Mensaje de espera
                        params: {
                            accion: 'Insertar', permisos: param
                        },
                        success: function() {
                            win.close();
                            store.load();
                        },
                        failure: function(form, action) {
                            Ext.MessageBox.show({
                                title: 'Mensaje del Sistema',
                                msg: 'Ha ocurrido un error en la operaci\xF3n. Por favor, compruebe que sean v\xE1lidos todos sus datos, de continuar el problema consulte al Administrador del Sistema.',
                                icon: Ext.MessageBox.ERROR,
                                buttons: Ext.Msg.OK
                            });
                        }
                    });
                }
            }
            else {
                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: 'Debe seleccionar al menos un permiso para este perfil antes de guardar la informaci\xF3n.',
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        }
   },
    eliminarPerfil: function() {

        var grid   = this.getTablaPerfiles();
        var record = grid.getSelectionModel().getSelection()[0];
        var id     = record.get('id');
        var store  = this.getPerfilesStore();

        Ext.Msg.confirm("Eliminar Rol de Usuario", "Este rol  ser\xE1 eliminado definitivamente. Confirma que desea realizar esta operaci\xF3n?", function(btnText) {
            if (btnText === "yes") {

                Ext.Ajax.request({ //dispara la petición

                    url: './php/administracion/PerfilesActions.php',
                    method: 'POST',
                    waitTitle: 'Espere',
                    waitMsg: 'Enviando datos..',
                    params: {accion: 'Eliminar', id_perfil: id},
                    success: function(result, request) {

                        var jsonData = Ext.JSON.decode(result.responseText);

                        if (!jsonData.failure) {

                            store.load();
                        }
                        else {

                            Ext.MessageBox.show({
                                title: 'Mensaje del Sistema',
                                msg: jsonData.message,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    },
                    failure: function() {

                        Ext.MessageBox.show({
                            title: 'Mensaje del Sistema',
                            msg: 'Ha ocurrido un error en el Sistema. Por favor, vuelva a intentar realizar la operacion, de continuar el problema consulte al Administrador del Sistema.',
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                });
            }
        }, this);
    },
    imprimirPerfiles: function() {

        let ipserver = localStorage.getItem('ipserver');
        window.open('http://'+ipserver+'/semti.garantia/php/administracion/pdf_Roles.php', '_blank');
    }

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////	
});