Ext.define('SEMTI.controller.Hfo', {
    extend: 'Ext.app.Controller',
    stores: ['Hfo','Hfodata','Zonas','Objetos'],
    models: ['Hfo','Hfodata','Zonas','Objetos'],
    views: [
        'informes.HfoGrid','informes.HfodataGrid'
    ],
    refs: [{
            ref: 'informesHfo',
            selector: 'hfogrid'
        },{
            ref: 'informesHfodata',
            selector: 'hfodatagrid'
        }],
    init: function() {

        this.control({
            'hfogrid': {
                itemmouseenter: this.showSdActionsGrid,
                itemmouseleave: this.hideSdActionsGrid,
                selectionchange: this.loaDataHFO
            },
            'hfodatagrid': {
                recordedit: this.hfodataComent,
            },
            'hfogrid  button[action=agregar]': {
                click: this.nuevoInforme
            },
            'hfogrid menu[lid=exportHfo] menuitem[lid=docpdf]': {
                click: this.exportHfoPDF
            },
            'hfogrid menu[lid=exportHfo] menuitem[lid=docppt]': {
                click: this.exportHfoPPT
            },
            '#hfoformProyect': {
                change: this.loadProyectZonasObjectos
            },
            '#hfoformZona': {
                change: this.loadObjectos
            },
            'hfogrid actioncolumn[id=gtiahfoColumnUpd]': {
                click: this.editarInforme
            },
            'hfoform button[action=guardar]': {
                click: this.actualizarHFO
            },
            'hfogrid actioncolumn[id=gtiahfoColumnDel]': {
                click: this.deleteInforme
            },
            '#gtiahfoColumnDel': {
                headerclick: this.deleteCheckInformes
            },
            'hfogrid button[action=buscar]': {
                click: this.filtroSD
            },
            'hfogrid dataview': {
                itemdblclick: this.editarInforme
            }
        });
    },
    
    /*******************************************
     ********       INFORMES HFO        ********
     *******************************************/

    
    loaDataHFO: function(view, records) {

        var hfodataGrid = this.getInformesHfodata();
                
        if(records.length){
            
            var hfoGrid     = this.getInformesHfo(),
                hfoRecord   = hfoGrid.getSelectionModel().getSelection()[0],
                id_informe  = hfoRecord.get('id');
        
            hfodataGrid.getStore().getProxy().setExtraParam("loadInfoData", id_informe);
            hfodataGrid.getStore().load();
        }
        else{
            hfodataGrid.getStore().removeAll();
        }
    },
    hfodataComent: function(element) {

        var id_row        = element.get('id'),
            observaciones = element.get('observaciones');

        Ext.Ajax.request({ //dispara la petición

            url: './php/informes/HfoActions.php',
            method: 'POST',
            params: {accion: 'hfodataComent', id_row: id_row, observaciones: observaciones},
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
    exportHfoPDF: function(){
        
        var grid       = this.getInformesHfo(),
            gridata    = this.getInformesHfodata(),
            record     = grid.getSelectionModel().getSelection()[0],
            id_informe = record.get('id'),
            store_data = gridata.getStore();
        
        var recordsToSend = [];

        store_data.each(function(record) {
            recordsToSend.push(Ext.apply(record.data));
        }, this);

        recordsToSend = Ext.encode(recordsToSend);
        var storeHfodata = recordsToSend;
        let ipserver = localStorage.getItem('ipserver');
        window.open('http://'+ipserver+'/semti.garantia/php/informes/pdf_Hfo.php?id='+id_informe+'&datastore='+storeHfodata, '_blank');
    },
    exportHfoPPT: function(){
        
        var grid       = this.getInformesHfo(),
            gridata    = this.getInformesHfodata(),
            record     = grid.getSelectionModel().getSelection()[0],
            id_informe = record.get('id'),
            store_data = gridata.getStore();
        
        var recordsToSend = [];

        store_data.each(function(record) {
            recordsToSend.push(Ext.apply(record.data));
        }, this);

        recordsToSend = Ext.encode(recordsToSend);
        var storeHfodata = recordsToSend;
        let ipserver = localStorage.getItem('ipserver');
        window.open('http://'+ipserver+'/semti.garantia/php/informes/ppt_Hfo.php?id='+id_informe+'&datastore='+storeHfodata, '_blank');
    },
    loadProyectZonasObjectos: function(combo, newValue) {

        var proyecto   = newValue,
            zonaCombo  = Ext.getCmp('hfoformZona'),
            objectCombo = Ext.getCmp('hfoformObjeto');
        
        zonaCombo.setDisabled(false);
        zonaCombo.setValue('');
        objectCombo.setDisabled(false);
        objectCombo.setValue('');

        zonaCombo.getStore().getProxy().setExtraParam("proyecto", proyecto);
        zonaCombo.getStore().load();
        
        var proyecto_zona = proyecto + '.Todas';
        objectCombo.getStore().getProxy().setExtraParam("proyecto_zona", proyecto_zona);
        objectCombo.getStore().load();
    },
    loadObjectos: function(combo, newValue) {

        var proyectCombo = Ext.getCmp('hfoformProyect'),
            proyecto     = proyectCombo.getValue(),
            objetCombo   = Ext.getCmp('hfoformObjeto');
            
        if(newValue === null){ var zona = 'Todas'; }else{ var zona = newValue; }
        
        var proyecto_zona = proyecto + '.' + zona;
        objetCombo.getStore().getProxy().setExtraParam("proyecto_zona", proyecto_zona);
        objetCombo.getStore().load();
    },
    nuevoInforme: function(grid, record) {

        var agregar = Ext.create('SEMTI.view.informes.HfoForm');
        agregar.setTitle('Nuevo Informe de Habitaciones fuera de orden');
        Ext.getCmp('hfo_form_titulo').focus();
    },
    editarInforme: function(grid, record) {

        var grid       = this.getInformesHfo(),
            record     = grid.getSelectionModel().getSelection()[0],
            id_informe = record.get('id');
        
        var editar = Ext.create('SEMTI.view.informes.HfoForm'),
            titulo = Ext.getCmp('hfo_form_titulo'),
            zona   = Ext.getCmp('hfoformZona'),
            objeto = Ext.getCmp('hfoformObjeto');

        editar.setTitle('Modificar Informe de Habitaciones fuera de orden');
        editar.show();
        titulo.focus();
        zona.setDisabled(false);
        objeto.setDisabled(false);

        var form = editar.down('form');

        form.getForm().load({
            url: './php/informes/HfoActions.php',
            method: 'POST',
            params: {
                accion: 'LoadInforme', id: id_informe
            },
            failure: function(form, action) {
                editar.close();
                Ext.Msg.alert("Carga Fallida", "La carga de los parametros del Usuario no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
            }
        });
    },
    showSdActionsGrid: function(view, task, node, rowIndex, e) {

        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon) {
            Ext.get(icon).removeCls('x-hidden');
        });
    },
    hideSdActionsGrid: function(view, task, node, rowIndex, e) {

        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon) {
            Ext.get(icon).addCls('x-hidden');
        });
    },
    actualizarHFO: function(button) {

        var win        = button.up('window'),
            form       = win.down('form'),
            values     = form.getValues(),
            zona       = values.zona,
            objeto     = values.objeto,
            controller = this,
            store      = controller.getHfoStore(),
            gridHfo    = controller.getInformesHfo(),
            HfoRecords = gridHfo.getChecked;

        if (form.isValid()) {

            if (values.id > 0) { //Si Hay algun Valor, entra en Modo de Actualizacion

                ////     ACTUALIZAR   

                form.getForm().submit({
                    method: 'POST',
                    //submitEmptyText: false,
                    url: './php/informes/HfoActions.php',
                    waitTitle: 'Espere', //Titulo del mensaje de espera
                    waitMsg: 'Procesando datos...', //Mensaje de espera
                    params: {
                        accion: 'HfoUdate', zonaValue: zona.toString(), objetoValue: objeto.toString()
                    },
                    success: function() {
                        win.close();
                        store.load();
                        controller.loaDataHFO(gridHfo,HfoRecords);
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


            } else { //De Lo contrario, si la accion fue para agregar, se inserta un registro

                ////    INSERTAR  

                form.getForm().submit({
                    method: 'POST',
                    url: './php/informes/HfoActions.php',
                    //submitEmptyText: false,
                    waitTitle: 'Espere', //Titulo del mensaje de espera
                    waitMsg: 'Procesando datos...', //Mensaje de espera
                    params: {
                        accion: 'HfoInsert', zonaValue: zona.toString(), objetoValue: objeto.toString()
                    },
                    success: function() {
                        win.close();
                        store.load();
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
        }
    },
    deleteInforme: function(gridView, rowIndex, colIndex, column, e, record) {

        var grid = this.getInformesHfo();
        //if(grid.getSelectionModel().getSelection()[0]) { var record = grid.getSelectionModel().getSelection()[0]; }
        var id = record.get('id');

        Ext.Msg.confirm("Confirmaci\xF3n", "El informe ser\xE1 eliminado definitivamente del sistema. Confirma que desea realizar esta operaci\xF3n?", function(btnText) {
            if (btnText === "yes") {

                grid.el.mask('Eliminando...', 'x-mask-loading');

                Ext.Ajax.request({ //dispara la petición

                    url: './php/informes/HfoActions.php',
                    method: 'POST',
                    params: {accion: 'HfoDelete', id: id},
                    success: function(result, request) {
                        var jsonData = Ext.JSON.decode(result.responseText);
                        if (jsonData.failure) {

                            Ext.MessageBox.show({
                                title: 'Mensaje del Sistema',
                                msg: jsonData.message,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                        else {

                            grid.getStore().load();
                            grid.el.unmask();
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
        });
    },
    deleteCheckInformes: function() {

        var grid = this.getInformesHfo(),
            selection = grid.getSelectionModel().getSelection();
        
        if (!Ext.isEmpty(selection)) {

            Ext.Msg.confirm("Confirmaci\xF3n", "Los Informes seleccionados ser\xE1n eliminados definitivamente del sistema. Confirma que desea realizar esta operaci\xF3n?", function(btnText) {
                if (btnText === "yes") {

                    grid.el.mask('Eliminando...', 'x-mask-loading');

                    var recordsToSend = [];

                    Ext.each(selection, function(record) {
                        recordsToSend.push(Ext.apply(record.data));
                    });

                    recordsToSend = Ext.encode(recordsToSend);
                    var parametros = recordsToSend;

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/informes/HfoActions.php',
                        method: 'POST',
                        params: {accion: 'HfoCheckDelete', parametros: parametros},
                        success: function(result, request) {

                            var jsonData = Ext.JSON.decode(result.responseText);

                            if (!jsonData.failure) {

                                grid.store.load();
                                grid.el.unmask();
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
        }
        else {

            Ext.MessageBox.show({
                title: 'Mensaje del Sistema',
                msg: 'Debe seleccionar al menos un Informe para realizar esta operaci\xF3n.',
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.WARNING
            });
        }
    }

});