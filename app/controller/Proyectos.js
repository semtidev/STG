Ext.define('SEMTI.controller.Proyectos', {
    extend: 'Ext.app.Controller',

    models: ['Treeproyectos','Fileproyect'],
    stores: ['Treeproyectos','Fileproyect','Gtiasd'],

    views: [
        'proyectos.TreeProyectos',
        'proyectos.ProyectContextMenu',
        'proyectos.ProyectGrid',
        'proyectos.ProyectGridForm',
        'proyectos.ProyectsPicker',
        'proyectos.ProyectForm',
        'proyectos.ZonaForm',
        'sistema.Poloscombo'
    ],

    refs: [{
            ref: 'treeProyectos',
            selector: 'treeProyectos'
        },{
            ref: 'contextMenu',
            selector: 'proyectContextMenu',
            xtype: 'proyectContextMenu',
            autoCreate: true
        },{
            ref: 'gridProyectos',
            selector: 'proyectGrid'
        },{
            ref: 'formProyectos',
            selector: 'proyectForm'
        },{
            ref: 'formGridProyectos',
            selector: 'ProyectGridForm'
        },{
            ref: 'pickerProyectos',
            selector: 'proyectsPicker'
    }],

    init: function() {
        
        var me = this;

        me.control({
            'treeProyectos button[action=nuevo]': {
            	click: me.handleNewProyect
            },
            'proyectform button[action=guardar]': {
                click: me.handleEditProyect
            },
            '[iconCls=proyects-reload]': {
                click: me.handleReloadClick
            },
            '[iconCls=proyects-rename]': {
                click: me.handleEditClick
            },
            '[iconCls=proyects-delete]': {
                click: me.handleDeleteClick
            },
            '[iconCls=proyects-down-tree]': {
                click: me.handleDownClick
            },
            '[iconCls=proyects-up-tree]': {
                click: me.handleUpClick
            },
            '#proyects-rename-proyect': {
                click: me.handleEditClick
            },
            '#show-sd': {
                click: me.setfiltroSDfromTree
            },
            '#proyects-delete-proyect': {
                click: me.handleDeleteClick
            },
            '#proyects-new-zone': {
                click: me.handleNewZone
            },
            'zonaform button[action=guardar]': {
                click: me.handleEditZone
            },
            '#proyects-rename-zone': {
                click: me.handleEditClick
            },
            '#proyects-delete-zone': {
                click: me.handleDeleteClick
            },
            '#proyects-new-object': {
                click: me.handleNewObjectClick
            },
            '#proyects-rename-object': {
                click: me.handleEditClick
            },
            '#proyects-new-parte': {
                click: me.handleNewPartClick
            },
            '#proyects-delete-part': {
                click: me.handleDeleteClick
            },
            '#proyects-rename-part': {
                click: me.handleEditClick
            },
            'treeProyectos': {
                /*edit: me.updateProyect,
                completeedit: me.handleCompleteEdit,*/
                recordedit: me.updateTreeElement,
                canceledit: me.handleCancelEdit,
                deleteclick: me.handleDeleteIconClick,
                itemclick: me.openProyect,
                itemmouseenter: me.showActions,
                itemmouseleave: me.hideActions,
                itemcontextmenu: me.showContextMenu
            },
            'ProyectGridForm #FileFormElementName': {
                specialkey: me.handleSpecialKey
            },
            'proyectGrid': {
                recordedit: me.updateGridElement,
                deleteclick: me.handleGridDeleteIconClick,
                editclick: me.handleEditIconClick,
                itemmouseenter: me.showActionsGrid,
                itemmouseleave: me.hideActionsGrid
            }
        });
    },

    handleNewProyect: function(){
		
        var add  = Ext.create('SEMTI.view.proyectos.ProyectForm'),
            form = add.down('form');

        add.setTitle('Nuevo Proyecto');
        add.show();
        //form.getForm().findField('text').focus();
    },
	
    handleNewZone: function(){
		
        var add    = Ext.create('SEMTI.view.proyectos.ZonaForm'),
                form   = add.down('form'),
                tree   = this.getTreeProyectos(),
                record = tree.getSelectionModel().getSelection()[0];

        add.setTitle('Nueva Zona');
        add.show();
        form.getForm().findField('id_parent').setValue(record.get('id'));
        form.getForm().findField('text').focus();
    },
		
    handleEditProyect: function(button) {

        var me         = this,
            tree       = me.getTreeProyectos(),
            record     = tree.getSelectionModel().getSelection()[0],
            win        = button.up('window'),
            form       = win.down('form'),
            record     = form.getRecord(),
            values     = form.getValues(),
            nombre     = values.text;

        if(form.isValid()){

        if (values.id.length > 0){ //Si Hay algun Valor, entra en Modo de Actualizacion


            ////////////////////////////
            ////      ACTUALIZAR    ////
            ////////////////////////////

            form.getForm().submit({   
                //target : '_blank', 
                method : 'POST',
                submitEmptyText : false,  
                //standardSubmit:true, 
                url: './php/proyectos/treeProyectActions.php',
                waitTitle: 'Espere',    //Titulo del mensaje de espera
                waitMsg: 'Enviando datos...',    //Mensaje de espera
                params: {
                    accion: 'EditarProyecto'
                },
                success: function(){
                    win.close();
                    if(Ext.getCmp('FileGridTitleProyect')){ Ext.getCmp('FileGridTitleProyect').setText('Proyecto ' + nombre); }
                    tree.getStore().load({
                            callback: function(records, operation, success) {
                                    var id = record.get('id'); // This is the ID of the node that somehow you know in advance
                                    var node = tree.getStore().getNodeById(id);
                                    tree.expandPath(node.getPath());
                            }                               
                    });
                    if(me.getPickerProyectos()){ me.getPickerProyectos().getStore().load(); }
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
        else{ //De Lo contrario, si la accion fue para agregar, se inserta un registro


            ////////////////////////////
            ////      INSERTAR      ////
            ////////////////////////////

            form.getForm().submit({   
                //target : '_blank', 
                method : 'POST', 
                submitEmptyText : false, 
                //standardSubmit:true, 
                url: './php/proyectos/treeProyectActions.php',
                waitTitle: 'Espere',    //Titulo del mensaje de espera
                waitMsg: 'Enviando datos...',    //Mensaje de espera
                params: {
                        accion: 'NuevoProyecto'
                },
                success: function(){
                        win.close();
                        me.getTreeProyectos().getStore().load();
                        if(me.getPickerProyectos()){ me.getPickerProyectos().getStore().load(); }
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
    },
	
    handleEditZone: function(button) {

        var me         = this,
            tree       = me.getTreeProyectos(),
            record     = tree.getSelectionModel().getSelection()[0],
            ruta       = record.get('ruta'),
            ruta_array = ruta.split(',')
            win        = button.up('window'),
            form       = win.down('form'),
            values     = form.getValues(),
            nombre     = values.text;

        if(form.isValid()){

            if (values.id.length > 0){ //Si Hay algun Valor, entra en Modo de Actualizacion

                ////////////////////////////
                ////      ACTUALIZAR    ////
                ////////////////////////////

                form.getForm().submit({   
                    //target : '_blank', 
                    method : 'POST',
                    submitEmptyText : false,  
                    //standardSubmit:true, 
                    url: './php/proyectos/treeProyectActions.php',
                    waitTitle: 'Espere',    //Titulo del mensaje de espera
                    waitMsg: 'Enviando datos...',    //Mensaje de espera
                    params: {
                        accion: 'EditarZona'
                    },
                    success: function(){
                        win.close();
                        Ext.getCmp('FileGridTitleProyect').setText(ruta_array[0] + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + nombre);
                        tree.getStore().load({
                            callback: function(records, operation, success) {
                                    var id = record.get('id'); // This is the ID of the node that somehow you know in advance
                                    var node = tree.getStore().getNodeById(id);
                                    tree.expandPath(node.getPath());
                            }                               
                        });
                        if(me.getPickerProyectos()){ me.getPickerProyectos().getStore().load(); }
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
            else{ //De Lo contrario, si la accion fue para agregar, se inserta un registro

                ////////////////////////////
                ////      INSERTAR      ////
                ////////////////////////////

                form.getForm().submit({   
                    //target : '_blank', 
                    method : 'POST', 
                    submitEmptyText : false, 
                    //standardSubmit:true, 
                    url: './php/proyectos/treeProyectActions.php',
                    waitTitle: 'Espere',    //Titulo del mensaje de espera
                    waitMsg: 'Enviando datos...',    //Mensaje de espera
                    params: {
                        accion: 'NuevaZona'
                    },
                    success: function(){
                        win.close();
                        tree.getStore().load({
                            callback: function(records, operation, success) {
                                    var id = record.get('id'); // This is the ID of the node that somehow you know in advance
                                    var node = tree.getStore().getNodeById(id);
                                    tree.expandPath(node.getPath());
                            }                               
                        });
                        if(me.getPickerProyectos()){ me.getPickerProyectos().getStore().load(); }
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
    },
	
    openProyect: function(){  //t,record,item,index

        if (localStorage.getItem('perfiles') == '10' || localStorage.getItem('perfiles') == '17') {
            return;
        }
        
        var record = this.getTreeProyectos().getSelectionModel().getSelection()[0];

        if(record != null){

            var itemid       = record.get('id'),
                arrId        = record.get('id').split('.'),
                categoria    = arrId[0],
                mainTabpanel = Ext.getCmp('PTpanel'),
                title;

            if(record.parentNode && categoria != 4){

                if (record.get('tipo') == 'polo') {
                    return;
                }

                if(!mainTabpanel.getChildByElement('tabProyect')){

                    mainTabpanel.add({
                            title: 'Estructuras de Proyectos',
                            xtype: 'proyectGrid',
                            id:'tabProyect',
                            closable:true
                    });
                }

                ///////////////////////////////////////////////
                ////////       CLIC EN PROYECTOS       ////////
                ///////////////////////////////////////////////
                if(categoria == 1){

                    title = 'Proyecto ' + record.get('text');
                    Ext.getCmp('FileGridTitleName').setText('Zonas');
                }
                ///////////////////////////////////////////////


                ///////////////////////////////////////////////
                ////////        CLIC EN ZONAS         /////////
                ///////////////////////////////////////////////
                else if(categoria == 2){

                    title = 'Proyecto ' + record.parentNode.get('text') + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + record.get('text');
                    Ext.getCmp('FileGridTitleName').setText('Objetos');
                }
                ///////////////////////////////////////////////


                ///////////////////////////////////////////////
                ////////       CLIC EN OBJETOS        /////////
                ///////////////////////////////////////////////
                else if(categoria == 3){

                    var proyecto = record.parentNode.parentNode.get('text'),
                        objeto   = record.get('text'),
                        arrRuta  = record.get('ruta').split(','),
                        zonaName = arrRuta[1];

                    var title = 'Proyecto ' + proyecto + '\xA0\xA0\xA0\xBB\xA0\xA0' + zonaName + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + objeto;
                    Ext.getCmp('FileGridTitleProyect').setText(title);
                    Ext.getCmp('FileGridTitleName').setText('Locales');
                }
                ////////////////////////////////////////////


                var store = mainTabpanel.getChildByElement('tabProyect').getStore();

                store.getProxy().setExtraParam("proyectId", itemid);
                store.load();

                Ext.getCmp('FileGridTitleProyect').setText(title);
                Ext.getCmp('FileFormElementId').setValue(itemid);

                mainTabpanel.setActiveTab('tabProyect');
            }
        }
    },
	
    handleDownClick: function(){

        var tree = this.getTreeProyectos();
        tree.getRootNode().expand(true);
    },

    handleUpClick: function(){

        var tree = this.getTreeProyectos();
        tree.getRootNode().collapseChildren(true);
    },

    handleNewObjectClick: function(){

        var me         = this
            tree       = me.getTreeProyectos(),
            record     = tree.getSelectionModel().getSelection()[0],
            myMsgBox   = new Ext.window.MessageBox(),
            idNode     = record.get('id')
            arr_id     = idNode.split('.'),
            id_zone    = arr_id[2];

        myMsgBox.textField.inputType = 'text';
        myMsgBox.prompt('Nuevo Objeto','Nombre:',function(btn,result) {
            if(btn=='ok') {
                if(result!=null) { 

                    var nombre = result;
                    tree.el.mask('Procesando...', 'x-mask-loading');

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/proyectos/treeProyectActions.php',
                        method:'POST', 
                        params:{accion: 'NuevoObjeto', idZona: id_zone, nombreObjeto: nombre},
                        success: function(result, request) { 
                            var jsonData = Ext.JSON.decode(result.responseText);
                            if(jsonData.failure){

                                Ext.MessageBox.show({
                                   title: 'Mensaje del Sistema',
                                   msg: jsonData.message,
                                   buttons: Ext.MessageBox.OK,
                                   icon: Ext.MessageBox.ERROR
                                });	
                            }
                            else{
                                tree.el.unmask();
                                tree.getStore().load({
                                        callback: function(records, operation, success) {
                                                var id = record.get('id'); // This is the ID of the node that somehow you know in advance
                                                var node = tree.getStore().getNodeById(id);
                                                tree.expandPath(node.getPath());
                                        }                               
                                });
                                if(me.getGridProyectos()){ me.getGridProyectos().getStore().load(); }
                                if(me.getPickerProyectos()){ me.getPickerProyectos().getStore().load(); }
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
            }
        });
    },

    handleNewPartClick: function(){

        var me         = this
            tree       = me.getTreeProyectos(),
            record     = tree.getSelectionModel().getSelection()[0],
            myMsgBox   = new Ext.window.MessageBox(),
            idNode     = record.get('id')
            arr_id     = idNode.split('.'),
            id_object  = arr_id[3];

        myMsgBox.textField.inputType = 'text';
        myMsgBox.prompt('Nueva Parte','Nombre:',function(btn,result) {
            if(btn=='ok') {
                if(result!=null) { 

                    var nombre = result;
                    tree.el.mask('Procesando...', 'x-mask-loading');

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/proyectos/treeProyectActions.php',
                        method:'POST', 
                        params:{accion: 'NuevaParte', idObject: id_object, nombreParte: nombre},
                        success: function(result, request) { 
                            var jsonData = Ext.JSON.decode(result.responseText);
                            if(jsonData.failure){

                                Ext.MessageBox.show({
                                   title: 'Mensaje del Sistema',
                                   msg: jsonData.message,
                                   buttons: Ext.MessageBox.OK,
                                   icon: Ext.MessageBox.ERROR
                                });	
                            }
                            else{
                                tree.el.unmask();
                                tree.getStore().load({
                                        callback: function(records, operation, success) {
                                                var id = record.get('id'); // This is the ID of the node that somehow you know in advance
                                                var node = tree.getStore().getNodeById(id);
                                                tree.expandPath(node.getPath());
                                        }                               
                                });
                                if(me.getGridProyectos()){ me.getGridProyectos().getStore().load(); }
                                if(me.getPickerProyectos()){ me.getPickerProyectos().getStore().load(); }
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
            }
        });
    },

    handleNewClick: function(component, e) {
        this.addProyect();
    },
	
    handleReloadClick: function(){

        var tree   = this.getTreeProyectos(),
            record = tree.getSelectionModel().getSelection()[0];
    
        if(record != null){
            tree.getStore().load({
                callback: function(records, operation, success) {
                    var id = record.get('id'); // This is the ID of the node that somehow you know in advance
                    var node = tree.getStore().getNodeById(id);
                    tree.getSelectionModel().deselect(records, true);
                    tree.expandPath(node.getPath());
                    tree.selectPath(node.getPath());
                }                               
            });
        }
        else{
            tree.getStore().load({
                callback: function(records, operation, success) {
                    tree.getSelectionModel().deselect(records, true);
                }
            });
        }
    },

    updateTreeElement: function(element) {
        
        var me          = this,
            tree        = me.getTreeProyectos(),
            idElement   = element.get('id'),
            arrId       = idElement.split('.'),
            categoria   = arrId[0],
            nameElement = element.get('text'),
            title;

        Ext.Ajax.request({ //dispara la petición
												
            url: './php/proyectos/treeProyectActions.php',
            method:'POST', 
            params:{accion: 'updateTreeElement', idElement: idElement, nameElement: nameElement},
            success: function(result, request) { 
                var jsonData = Ext.JSON.decode(result.responseText);
                if(jsonData.failure){

                    Ext.MessageBox.show({
                       title: 'Mensaje del Sistema',
                       msg: jsonData.message,
                       buttons: Ext.MessageBox.OK,
                       icon: Ext.MessageBox.ERROR
                    });	
                }
                else{
                   
                    // Si es un Proyecto
                    if(categoria == 1){
                        title = 'Proyecto ' + nameElement;
                    }
                    // Si es una Zona
                    else if(categoria == 2){
                        title = 'Proyecto ' + element.parentNode.get('text') + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + nameElement;
                    }
                    // Si es un Objeto
                    else if(categoria == 3){
                        var proyecto = element.parentNode.parentNode.get('text'),
                            objeto   = nameElement,
                            arrRuta  = element.get('ruta').split(','),
                            zonaName = arrRuta[1];

                        var title = 'Proyecto ' + proyecto + '\xA0\xA0\xA0\xBB\xA0\xA0' + zonaName + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + objeto;
                    }

                    Ext.getCmp('FileGridTitleProyect').setText(title);
                    me.handleReloadClick();
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
    },

    setfiltroSDfromTree : function(){
        var tree     = this.getTreeProyectos(),
            record   = tree.getSelectionModel().getSelection()[0],
            id       = record.get('id'),
            id_array = id.split('.');
        
        if(id_array[0] == 1){
            var proyecto = record.get('text');
            var zona = '';
            var objeto = '';
        }
        if(id_array[0] == 2){
            var proyecto = record.parentNode.get('text')+', ';
            var zona = record.get('text');
            var objeto = '';
        }
        if(id_array[0] == 3){
            var proyecto = record.parentNode.parentNode.get('text')+', ';
            var zona = record.parentNode.get('text')+', ';
            var objeto = record.get('text');
        }

        // Sustituirlo por darle click a la opcion del tree de administracion.
        if (!Ext.getCmp('tabsyst26')) {
            Ext.getCmp('PTpanel').add({
                title: 'Solicitudes de Defectaci\xF3n',
                xtype: 'gtiasd',
                id: 'tabsyst26',
                iconCls: 'icon_SD',
                closable: true
            });
            Ext.getCmp('PTpanel').setActiveTab('tabsyst26');
        }        

        var store = this.getGtiasdStore(),
            fieldsToSend = '****'+proyecto+zona+objeto+'**********-**';
        
        store.getProxy().setExtraParam("filtrar", fieldsToSend);
        store.load();
        Ext.getCmp('viewSDcomboEstado').setValue('Por Resolver');
        //store.getProxy().setExtraParam("listar", 'Todos.Por Resolver.Todos');
        Ext.getCmp('gtia_sd_busqueda_check').setVisible(true);
        Ext.getCmp('gtia_sd_busqueda_check').setValue(true); 
    },
    
    handleEditClick: function() {

        var tree     = this.getTreeProyectos(),
            record   = tree.getSelectionModel().getSelection()[0],
            id       = record.get('id'),
            id_array = id.split('.');

        if(id_array[0] == 1){

            var editar = Ext.create('SEMTI.view.proyectos.ProyectForm');
            editar.setTitle('Modificar Proyecto');
            editar.show();

            var EditForm = editar.down('form');	
            EditForm.loadRecord(record);
        }
        else if(id_array[0] == 2){

            var editar = Ext.create('SEMTI.view.proyectos.ZonaForm');
            editar.setTitle('Modificar Zona');
            editar.show();

             var EditForm = editar.down('form');	
             EditForm.loadRecord(record);	
        }
        else{
            tree.getPlugin('elementNameEditing').startEdit(record,0);
        }
    },

    handleCompleteEdit: function(editor, e){
        delete this.addedNode;
    },

    handleCancelEdit: function(editor, e) {
        var list = e.record,
            parent = list.parentNode,
            added = this.addedNode;

        delete this.addedNode;
        if (added === list) {
            // Only remove it if it's been newly added
            parent.removeChild(list);
            this.getListTree().getStore().sync();
            this.getListTree().getSelectionModel().select([parent]);
        }
    },

    handleNewFolderClick: function(component, e) {
        this.addProyect();
    },

    updateProyect: function(editor, e) {
        
        var me           = this,
            tree         = me.getTreeProyectos(),
            mainTabpanel = Ext.getCmp('PTpanel'),
            record       = tree.getSelectionModel().getSelection()[0];

        tree.getStore().sync({
            failure: function(batch, options) {
                var error = batch.exceptions[0].getError(),
                    msg   = Ext.isObject(error) ? error.status + ' ' + error.statusText : error;

                Ext.MessageBox.show({
                    title: 'Fall\xF3 la operaci\xF3.',
                    msg: msg,
                    icon: Ext.Msg.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        });

        // Actualizar el Tab Panel
        if(mainTabpanel.getChildByElement('tabProyect')){

            var grid   = me.getGridProyectos(),
                form   = grid.down('ProyectGridForm'),
                values = form.getValues();

            if(values.idElement == record.get('id')){
                    grid.getStore().load();	
            }
        }
        tree.getStore().load({
            callback: function(records, operation, success) {
                var id = record.get('id'); // This is the ID of the node that somehow you know in advance
                var node = tree.getStore().getNodeById(id);
                tree.expandPath(node.getPath());
            }                               
        });
    },
    
    handleDeleteIconClick: function(view, rowIndex, colIndex, column, e) {
        this.deleteElement(view.getRecord(view.findTargetByEvent(e)));
    },

    handleDeleteClick: function(component, e) {
        this.deleteElement(this.getTreeProyectos().getSelectionModel().getSelection()[0]);
    },

    deleteElement: function(record) {
        
        var me            = this,
            elementTree   = me.getTreeProyectos(),
            elementName   = record.get('text'),
            elementId     = record.get('id'),
            elementStore  = me.getTreeproyectosStore(),
            selModel      = elementTree.getSelectionModel(),
            mainTabpanel  = Ext.getCmp('PTpanel'),
            elementParent = record.parentNode;
            arrId         = record.get('id').split('.'),
            categoria     = arrId[0];
			
        //if((arrId.length == 2 && categoria == 1) || (arrId.length == 3 && categoria != 4)){

            Ext.Msg.show({
                title: 'Confirmaci\xF3n',
                msg: 'Esta seguro que desea eliminar permanentemente el elemento "' + elementName + '" de la estructura de Proyectos?',
                buttons: Ext.Msg.YESNO,
                fn: function(response) {
                    if(response === 'yes') {

                        // Actualizar el Tab Panel
                        if(mainTabpanel.getChildByElement('tabProyect')){

                            var grid   = me.getGridProyectos(),
                                form   = grid.down('ProyectGridForm'),
                                values = form.getValues();

                            if(values.idElement == record.get('id')){
                                mainTabpanel.remove('tabProyect');	
                            }
                        }


                        record.removeAll();
                        record.parentNode.removeChild(record);

                        Ext.Ajax.request({
                            url: './php/proyectos/treeProyectActions.php',
                            method:'POST', 
                            params:{accion: 'destroyTreeElement', params: elementId},
                            success: function(result, request) { 
                                if(me.getGridProyectos()){ me.getGridProyectos().getStore().load(); }
                                /*var jsonData = Ext.JSON.decode(result.responseText);
                                objeto.inputEl.dom.value = jsonData.objeto;
                                objeto.inputEl.dom.style = 'color: #000000; width: 100%';*/
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
                }
            });
        //}
    },

    showActions: function(view, record, node, rowIndex, e) {
        
        if (localStorage.getItem('perfiles') == '10' || localStorage.getItem('perfiles') == '17') {
            return;
        }

        var icons    = Ext.DomQuery.select('.x-action-col-icon', node),
            array_id = view.getRecord(node).get('id').split('.');
		
        // Mostrar solo en proyectos y nodos terminales
        //if(array_id.length == 3 || array_id[0] == 1) {
            Ext.each(icons, function(icon){
                Ext.get(icon).removeCls('x-hidden');
            });
        //}
    },

    hideActions: function(view, list, node, rowIndex, e) {
        if (localStorage.getItem('perfiles') == '10' || localStorage.getItem('perfiles') == '17') {
            return;
        }
        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon){
            Ext.get(icon).addCls('x-hidden');
        });
    },

    showContextMenu: function(view, record, node, rowIndex, e) {
        
        if (record.get('tipo') == 'polo' || localStorage.getItem('perfiles') == '10' || localStorage.getItem('perfiles') == '17') {
            return;
        }

        var contextMenu   = this.getContextMenu(),
            newZone       = Ext.getCmp('proyects-new-zone'),
            newObject     = Ext.getCmp('proyects-new-object'),
            newParte      = Ext.getCmp('proyects-new-parte'),
            renameProyect = Ext.getCmp('proyects-rename-proyect'),
            renameZone    = Ext.getCmp('proyects-rename-zone'),
            renameObject  = Ext.getCmp('proyects-rename-object'),
            deleteProyect = Ext.getCmp('proyects-delete-proyect'),
            deleteZone    = Ext.getCmp('proyects-delete-zone'),
            deleteObject  = Ext.getCmp('proyects-delete-object'),
            showsd        = Ext.getCmp('show-sd'),
            //tieneSd      = view.getRecord(node).get('itemId'),
            array_id      = view.getRecord(node).get('id').split('.');
        
        if(array_id.length == 2) {
            
            newZone.show();
            newObject.hide();
            newParte.hide()
            renameProyect.show();
            renameZone.hide();
            renameObject.hide();
            deleteProyect.show();
            deleteZone.hide();
            deleteObject.hide();			
        }
        else if(array_id.length == 3) {

            newZone.hide();
            newObject.show();
            newParte.hide()
            renameProyect.hide();
            renameZone.show();
            renameObject.hide();
            deleteProyect.hide();
            deleteZone.show();
            deleteObject.hide();
        }
        else if(array_id.length == 4) {
            
            newZone.hide();
            newObject.hide();
            newParte.show()
            renameProyect.hide();
            renameZone.hide();
            renameObject.show();
            deleteProyect.hide();
            deleteZone.hide();
            deleteObject.show();

        }
        else{
            return false;
        }
		
        contextMenu.setList(record);
        contextMenu.showAt(e.getX(), e.getY());
        e.preventDefault(); 
    },
	
    handleSpecialKey: function(field, e) {
        if(e.getKey() === e.ENTER) {
            this.newGridElement();
        }
    },

    newGridElement: function() {
        
        var me          = this,
            tree        = me.getTreeProyectos(),
            grid        = me.getGridProyectos(),
            treerecord  = this.getTreeProyectos().getSelectionModel().getSelection()[0],
            arrayrecord = treerecord.get('id').split('.'),
            form        = me.getFormGridProyectos(),
            basicForm   = form.getForm(),
            formEl      = form.getEl(),
            nameField   = form.getForm().findField('nombre');

        //alert(arrayrecord.length);
        // require title field to have a value
        if(!nameField.getValue()) {
            return;
        }

        form.getForm().submit({   
            //target : '_blank', 
            method : 'POST', 
            //standardSubmit:true, 
            url: './php/proyectos/treeProyectActions.php',
            waitTitle: 'Espere',    //Titulo del mensaje de espera
            waitMsg: 'Enviando datos...',    //Mensaje de espera
            params: {
                accion: 'InsertarElemento'
            },
            success: function(){
                nameField.reset();
                grid.getStore().load();
                nameField.focus();
                if(arrayrecord.length < 4){ me.handleReloadClick(); }               
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
    },
	
    showActionsGrid: function(view, task, node, rowIndex, e) {
        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon){
            Ext.get(icon).removeCls('x-hidden');
        });
    },

    hideActionsGrid: function(view, task, node, rowIndex, e) {
        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon){
            Ext.get(icon).addCls('x-hidden');
        });
    },
	
    updateGridElement: function(element) {
        
        var me          = this,
            grid        = me.getGridProyectos(),
            treerecord  = this.getTreeProyectos().getSelectionModel().getSelection()[0],
            arrayrecord = treerecord.get('id').split('.'),
            form        = me.getFormGridProyectos(),
            idElement   = form.getForm().findField('idElement').getValue() + '.' + element.get('id'),
            nameElement = element.get('nombre');

        // require title field to have a value
        if(!element.get('nombre')) {
            return;
        }

        Ext.Ajax.request({ //dispara la petición
												
            url: './php/proyectos/treeProyectActions.php',
            method:'POST', 
            params:{accion: 'updateGridElement', idElement: idElement, nameElement: nameElement},
            success: function(result, request) { 
                var jsonData = Ext.JSON.decode(result.responseText);
                if(jsonData.failure){

                    Ext.MessageBox.show({
                       title: 'Mensaje del Sistema',
                       msg: jsonData.message,
                       buttons: Ext.MessageBox.OK,
                       icon: Ext.MessageBox.ERROR
                    });	
                }
                else{
                    grid.getStore().load();
                    if(arrayrecord.length < 4){ me.handleReloadClick(); }
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
    },
	
    handleEditIconClick: function(view, rowIndex, colIndex, column, e) {
        
        var me     = this,
            grid   = me.getGridProyectos(),
            record = view.findTargetByEvent(e);
	
        grid.getPlugin('elementGridNameEditing').startEdit(view.getRecord(record),0);
    },
	
    handleGridDeleteIconClick: function(view, rowIndex, colIndex, column, e) {
        this.deleteElementGrid(this.getFileproyectStore().getAt(rowIndex));
    },
	
    deleteElementGrid: function(element, successCallback) {
        
        var me          = this,
            grid        = me.getGridProyectos(),
            treerecord  = this.getTreeProyectos().getSelectionModel().getSelection()[0],
            arrayrecord = treerecord.get('id').split('.'),
            form        = me.getFormGridProyectos(),
            idElement   = form.getForm().findField('idElement').getValue() + '.' + element.get('id'),
            nameElement = element.get('nombre');
			
        Ext.Msg.show({
            title: 'Confirmaci\xF3n',
            msg: 'Esta seguro que desea eliminar permanentemente el elemento "' + nameElement + '" de la estructura de Proyectos?',
            buttons: Ext.Msg.YESNO,
            fn: function(response) {
                if(response === 'yes') {

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/proyectos/treeProyectActions.php',
                        method:'POST', 
                        params:{accion: 'destroyGridElement', idElement: idElement, nameElement: nameElement},
                        success: function(result, request) { 
                            var jsonData = Ext.JSON.decode(result.responseText);
                            if(jsonData.failure){

                                Ext.MessageBox.show({
                                   title: 'Mensaje del Sistema',
                                   msg: jsonData.message,
                                   buttons: Ext.MessageBox.OK,
                                   icon: Ext.MessageBox.ERROR
                                });	
                            }
                            else{
                                grid.getStore().load();
                                if(arrayrecord.length < 4){ me.handleReloadClick(); }
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
            }
        });
    }

});
