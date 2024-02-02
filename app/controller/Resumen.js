Ext.define('SEMTI.controller.Resumen', {
    extend: 'Ext.app.Controller',
    stores: ['Zonas','Resumen','Resumenestados','Resumensdpendientes','Resumenpindicadores','Resumenrepetitividad','Resumenhfo','Resumencomportamhfo'],
    models: ['Zonas','Resumen','Resumenestados','Resumensdpendientes','Resumenpindicadores','Resumenrepetitividad','Resumenhfo','Resumencomportamhfo'],
    views: [
        'informes.ResumenGrid',        
        'informes.ResumendataEstadosPanel',
        'informes.ResumendataEstadosGrid',
        'informes.ResumendataSDPendientesGrid',
        'informes.ResumendataPIndicadoresGrid',
        'informes.ResumendataRepetitividadGrid',
        'informes.ResumendataHfoGrid',
        'informes.ResumendataComportamHfoGrid'
    ],
    refs: [
        {
            ref: 'informesResumen',
            selector: 'ResumenGrid'
        },{
            ref: 'dataResumenEstadosGrid',
            selector: 'ResumendataEstadosGrid'
        },{
            ref: 'dataResumenSdPendientesGrid',
            selector: 'ResumendataSDPendientesGrid'
        },{
            ref: 'dataResumenPIndicadoresGrid',
            selector: 'ResumendataPIndicadoresGrid'
        },{
            ref: 'dataResumenRepetitividadGrid',
            selector: 'ResumendataRepetitividadGrid'
        },{
            ref: 'resumenHfoGrid',
            selector: 'ResumendataHfoGrid'
        },{
            ref: 'resumenComportamHfoGrid',
            selector: 'ResumendataComportamHfoGrid'
        }
    ],
    init: function() {

        var me = this;

        me.control({
            'ResumenGrid': {
                itemmouseenter: this.showSdActionsGrid,
                itemmouseleave: this.hideSdActionsGrid,
                selectionchange: this.loaDataResumen
            },
            '#resumendataComentInicial': {
                blur: this.saveComentInicial   
            },
            '#resumendataComentFinal': {
                blur: this.saveComentFinal   
            },
            'ResumendataSDPendientesGrid': {
                recordedit: this.ResumendataSDPendientesGridComent,
            },
            'ResumendataPIndicadoresGrid': {
                recordedit: this.ResumendataPIndicadoresGridComent,
            },
            'ResumendataRepetitividadGrid': {
                recordedit: this.ResumendataRepetitividadGridComent,
            },
            'ResumendataHfoGrid': {
                recordedit: this.ResumendataHfoGridComent,
            },
            'ResumenGrid  button[action=agregar]': {
                click: this.nuevoInforme
            },
            'ResumenGrid menu[lid=exportResumen] menuitem[lid=docpdf]': {
                click: this.exportResumenPDF
            },
            'ResumenGrid menu[lid=exportResumen] menuitem[lid=docppt]': {
                click: this.exportResumenPPT
            },
            '#resumenformProyect': {
                change: this.loadProyectZonasObjectos
            },
            'ResumenGrid actioncolumn[id=gtiaresumenColumnUpd]': {
                click: this.editarInforme
            },
            'ResumenForm button[action=guardar]': {
                click: this.actualizarResumen
            },
            'ResumenValidate button[action=generar]': {
                click: this.generarInforme
            },
            'ResumenGrid actioncolumn[id=gtiaresumenColumnDel]': {
                click: this.deleteInforme
            },
            'ResumenGrid dataview': {
                itemdblclick: this.editarInforme
            }
        });
    },
    
    /*********************************************
     *******       INFORMES RESUMEN        *******
     *********************************************/

    saveComentInicial: function(field) {
        
        var resumenGrid   = this.getInformesResumen(),
            resumenRecord = resumenGrid.getSelectionModel().getSelection()[0],
            resumenId     = resumenRecord.get('id'),
            comentForm    = field.up('form'),
            valuesForm    = comentForm.getValues();
        
        if (comentForm.isValid()) {
                
            comentForm.getForm().submit({
                method: 'POST',
                //submitEmptyText: false,
                url: './php/informes/ResumenActions.php',
                params: {
                    accion: 'ResumenComentInicial', id_resumen: resumenId
                },
                success: function() {
                    resumenRecord.set('comentario_inicial', valuesForm.comentario_inicial);
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
    
    saveComentFinal: function(field) {
        
        var resumenGrid   = this.getInformesResumen(),
            resumenRecord = resumenGrid.getSelectionModel().getSelection()[0],
            resumenId     = resumenRecord.get('id'),
            comentForm    = field.up('form'),
            valuesForm    = comentForm.getValues();
        
        if (comentForm.isValid()) {
                
            comentForm.getForm().submit({
                method: 'POST',
                url: './php/informes/ResumenActions.php',
                params: {
                    accion: 'ResumenComentFinal', id_resumen: resumenId
                },
                success: function() {
                    resumenRecord.set('comentario_final', valuesForm.comentario_final);
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
    
    ResumendataSDPendientesGridComent: function(element) {

        var id_row     = element.get('id'),
            comentario = element.get('comentario');

        Ext.Ajax.request({ //dispara la petición

            url: './php/informes/ResumenActions.php',
            method: 'POST',
            params: {accion: 'ResumendataSDPendientesGridComent', id_row: id_row, comentario: comentario},
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
    
    ResumendataPIndicadoresGridComent: function(element) {

        var id_row     = element.get('id'),
            comentario = element.get('acciones');

        Ext.Ajax.request({ //dispara la petición

            url: './php/informes/ResumenActions.php',
            method: 'POST',
            params: {accion: 'ResumendataPIndicadoresGridComent', id_row: id_row, comentario: comentario},
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
    
    ResumendataRepetitividadGridComent: function(element) {

        var id_row     = element.get('id'),
            comentario = element.get('comentario');

        Ext.Ajax.request({ //dispara la petición

            url: './php/informes/ResumenActions.php',
            method: 'POST',
            params: {accion: 'ResumendataRepetitividadGridComent', id_row: id_row, comentario: comentario},
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
    
    ResumendataHfoGridComent: function(element) {

        var id_row     = element.get('id'),
            comentario = element.get('observaciones');

        Ext.Ajax.request({ //dispara la petición

            url: './php/informes/ResumenActions.php',
            method: 'POST',
            params: {accion: 'ResumendataHfoGridComent', id_row: id_row, comentario: comentario},
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
    
    loaDataResumen: function(view, record) {

        var me                    = this,
            resumenGrid           = me.getInformesResumen(),
            resumenEstadosGrid    = me.getDataResumenEstadosGrid(),
            resumenSdPendGrid     = me.getDataResumenSdPendientesGrid(),
            resumenRepetGrid      = me.getDataResumenRepetitividadGrid(),
            resumenPIndicGrid     = me.getDataResumenPIndicadoresGrid(),
            resumenHfoGrid        = me.getResumenHfoGrid(),
            resumenComportHfoGrid = me.getResumenComportamHfoGrid();
        
        
        if (resumenGrid.getStore().count() > 0) {
                
            if(resumenGrid.getSelectionModel().getSelection()[0]) {
                record = resumenGrid.getSelectionModel().getSelection()[0];
                console.log(record);
            }
            else{
                record = resumenGrid.getStore().getAt(0);
                resumenGrid.getSelectionModel().select(record);
                console.log('noselected');
            } 
            
            var id_informe = parseInt(record.get('id'));
            //Ext.Msg.alert('msg',id_informe);
            
                        
            // Habilitar los campos de comentario inicial y final
            Ext.getCmp('resumendataComentInicial').setDisabled(false); //!record.length
            Ext.getCmp('resumendataComentFinal').setDisabled(false);
            
                
            // Limpiar los datos de las pestañas antes de llenarlos
            resumenEstadosGrid.getStore().removeAll();
            resumenSdPendGrid.getStore().removeAll();
            resumenRepetGrid.getStore().removeAll();
            resumenPIndicGrid.getStore().removeAll();
            resumenHfoGrid.getStore().removeAll();
            resumenComportHfoGrid.getStore().removeAll();
            
            // Cargar Pestaña Comentario Inicial
            var comentInicialForm = Ext.getCmp('ResumendataFormComentInicial');
            comentInicialForm.loadRecord(record);  
            
            // Cargar Pestaña Comentario Final
            var comentFinalForm = Ext.getCmp('ResumendataFormComentFinal');
            comentFinalForm.loadRecord(record);
            
            // Cargar Pestaña Estados de las SD                
            resumenEstadosGrid.getStore().getProxy().setExtraParam("loadResumenDataEstados", id_informe);
            resumenEstadosGrid.getStore().load({
                callback: function() {
                    var rec_estados = me.getResumenestadosStore().getAt(0),
                        sdF         = rec_estados.get('firmadas'),
                        sdR         = rec_estados.get('reclamadas'),
                        sdNP        = rec_estados.get('noproceden'),
                        sdPR        = rec_estados.get('poresolver'),
                        sdEP        = rec_estados.get('enproceso'),
                        sdTotal     = rec_estados.get('total'),
                        frame       = Ext.getCmp('boxResumenChartEstados'); 
                    
                    if(frame && frame.rendered ){
                        let ipserver = localStorage.getItem('ipserver');
                        frame.getEl().dom.src = 'http://'+ipserver+'/semti.garantia/php/informes/resumenChartEstados.php?sdF='+sdF+'&sdR='+sdR+'&sdPR='+sdPR+'&sdNP='+sdNP+'&sdTotal='+sdTotal+'&sdEP='+sdEP;
                    }                    
                }                               
            });
            
            // Cargar Pestaña SD Pendientes
            resumenSdPendGrid.getStore().getProxy().setExtraParam("loadResumenDataSdPendientes", id_informe);
            resumenSdPendGrid.getStore().load();
            
            // Cargar Pestaña Principales Indicadores
            resumenPIndicGrid.getStore().getProxy().setExtraParam("loadResumenDataPIndicadores", id_informe);
            resumenPIndicGrid.getStore().load();
            
            // Cargar Pestaña Repetitividad
            resumenRepetGrid.getStore().getProxy().setExtraParam("loadResumenDataRepetitividad", id_informe);
            resumenRepetGrid.getStore().load();
                        
            // Cargar Pestaña HFO
            resumenHfoGrid.getStore().getProxy().setExtraParam("loadResumenDataHFO", id_informe);
            resumenHfoGrid.getStore().load();
            
            // Cargar Pestaña Comportamiento HFO
            resumenComportHfoGrid.getStore().getProxy().setExtraParam("loadResumenDataComportamHFO", id_informe);
            resumenComportHfoGrid.getStore().load();
        }
        else {
            // Limpiar los datos de las pestañas
            Ext.getCmp('resumendataComentInicial').setValue('');
            Ext.getCmp('resumendataComentFinal').setValue('');
            resumenEstadosGrid.getStore().removeAll();
            resumenSdPendGrid.getStore().removeAll();
            resumenRepetGrid.getStore().removeAll();
            resumenPIndicGrid.getStore().removeAll();
            resumenHfoGrid.getStore().removeAll();
            resumenComportHfoGrid.getStore().removeAll();

            var tab = Ext.getCmp('ResumenTabpanel');
            tab.setActiveTab(0);
        }
    },
    
    generarInforme: function(button){
        
        var winvalidate      = button.up('window'),
            formvalidate     = winvalidate.down('form'),
            valuesvalidate   = formvalidate.getValues(),
            exportto         = valuesvalidate.exportto,
            grid             = this.getInformesResumen(),
            record           = grid.getSelectionModel().getSelection()[0],
            id_informe       = record.get('id'),
            
            estadosData      = this.getDataResumenEstadosGrid(),
            estadoStore_data = estadosData.getStore(),
            
            sdpendientesData = this.getDataResumenSdPendientesGrid(),
            sdpendStore_data = sdpendientesData.getStore(),
            
            pindicadoresData = this.getDataResumenPIndicadoresGrid(),
            pindicStore_data = pindicadoresData.getStore(),
            
            repetitivData    = this.getDataResumenRepetitividadGrid(),
            repetStore_data  = repetitivData.getStore(),
            
            hfoData          = this.getResumenHfoGrid(),
            hfoStore_data    = hfoData.getStore(),
            
            hfoComportData   = this.getResumenComportamHfoGrid(),
            hfoCStore_data   = hfoComportData.getStore(),
            
            urlResumen       = '',
            storeEstadosData = '',
            storeSdpendData  = '',
            storePIndicData  = '',
            storeRepetData   = '',
            storeHfoData     = '',
            storeHfoCompData = '';
        
        // Validad el form
        if(valuesvalidate.comentini != 'on' && valuesvalidate.estados != 'on' && valuesvalidate.sdpendientes != 'on' && valuesvalidate.indicadores != 'on' && valuesvalidate.problemasrep != 'on' && valuesvalidate.hfo != 'on' && valuesvalidate.comportamientohfo != 'on' && valuesvalidate.deficonstruct != 'on' && valuesvalidate.comentfin != 'on'){
            Ext.MessageBox.show({
                title: 'Mensaje del Sistema',
                msg: 'Debe seleccionar al menos una secci\xF3n del informe para Generarlo.',
                icon: Ext.MessageBox.WARNING,
                buttons: Ext.Msg.OK
            });
        }
        else{
        
            // Estados
            if(valuesvalidate.estados == 'on'){
                
                var estadosRecordsToSend = [];
                
                estadoStore_data.each(function(record) {
                    estadosRecordsToSend.push(Ext.apply(record.data));
                }, this);
                
                estadosRecordsToSend = Ext.encode(estadosRecordsToSend);
                storeEstadosData = estadosRecordsToSend;                
            }
            
            // SD Pendientes
            if(valuesvalidate.sdpendientes == 'on'){
                
                var sdpendRecordsToSend  = [];
    
                sdpendStore_data.each(function(record) {
                     sdpendRecordsToSend.push(Ext.apply(record.data));
                }, this);
            
                sdpendRecordsToSend = Ext.encode(sdpendRecordsToSend);
                storeSdpendData  = sdpendRecordsToSend;
            }
            
            // Principales Indicadores
            if(valuesvalidate.indicadores == 'on'){
                
                var pindicRecordsToSend  = [];
    
                pindicStore_data.each(function(record) {
                     pindicRecordsToSend.push(Ext.apply(record.data));
                }, this);
            
                pindicRecordsToSend = Ext.encode(pindicRecordsToSend);
                storePIndicData  = pindicRecordsToSend;
            }
            
            // Problemas Repetitivos
            if(valuesvalidate.problemasrep == 'on'){
                
                var repetRecordsToSend  = [];
    
                repetStore_data.each(function(record) {
                     repetRecordsToSend.push(Ext.apply(record.data));
                }, this);
            
                repetRecordsToSend = Ext.encode(repetRecordsToSend);
                storeRepetData  = repetRecordsToSend;
            }
            
            // HFO
            if(valuesvalidate.hfo == 'on'){
                
                var hfoRecordsToSend  = [];
    
                hfoStore_data.each(function(record) {
                     hfoRecordsToSend.push(Ext.apply(record.data));
                }, this);
            
                hfoRecordsToSend = Ext.encode(hfoRecordsToSend);
                storeHfoData     = hfoRecordsToSend;
            }
            
            // Comportamiento HFO
            if(valuesvalidate.comportamientohfo == 'on'){
                
                var comporthfoRecordsToSend  = [];
    
                hfoCStore_data.each(function(record) {
                     comporthfoRecordsToSend.push(Ext.apply(record.data));
                }, this);
            
                comporthfoRecordsToSend = Ext.encode(comporthfoRecordsToSend);
                storeHfoCompData        = comporthfoRecordsToSend;
            }
                    
            // Definir hacia que formato se realizará la exportacion
            if(exportto == 'PDF'){
                urlResumen = './php/informes/pdf_Resumen.php';
            }
            else if(exportto == 'PPT'){
                urlResumen = './php/informes/ppt_Resumen.php';
            }
            
            formvalidate.getForm().submit({
                target: '_blank',
                method: 'POST',
                standardSubmit: true,
                submitEmptyText: false,
                url: urlResumen,
                params: {
                    id: id_informe, estadoStore: storeEstadosData, sdpendStore: storeSdpendData, hfoStore: storeHfoData, pindicStore: storePIndicData, repetStore: storeRepetData, comportHfoStore: storeHfoCompData
                }
            });
            
            window.setTimeout(function(){
                winvalidate.close();
            },1000);
        }
    },
    
    exportResumenPDF: function(){
        
        var inforesumen = Ext.create('SEMTI.view.informes.ResumenValidate');
        
        var form      = inforesumen.down('form'),
            exportto  = form.getForm().findField('exportto');
            
        exportto.setValue('PDF');
        
        var windowform = Ext.getCmp('ResumenValidate');
        windowform.el.mask('Espere...', 'x-mask-loading');        

        form.getForm().load({
            url: './php/informes/ResumenActions.php',
            method: 'POST',
            params: {
                accion: 'LoadResumenValidate'
            },
            success: function() { 			
				windowform.el.unmask();
            },
            failure: function() {
                editar.close();
                Ext.Msg.alert("Carga Fallida", "La carga de los parametros de validaci\xF3n del informe no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
            }
        });      
    },
	
    exportResumenPPT: function(){
        
        var inforesumen = Ext.create('SEMTI.view.informes.ResumenValidate');
        
        var form      = inforesumen.down('form'),
            exportto  = form.getForm().findField('exportto');
        
        exportto.setValue('PPT');
        
        var windowform = Ext.getCmp('ResumenValidate');
        windowform.el.mask('Espere...', 'x-mask-loading'); 

        form.getForm().load({
            url: './php/informes/ResumenActions.php',
            method: 'POST',
            params: {
                accion: 'LoadResumenValidate'
            },
            success: function() { 			
				windowform.el.unmask();
            },
            failure: function() {
                editar.close();
                Ext.Msg.alert("Carga Fallida", "La carga de los parametros de validaci\xF3n del informe no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
            }
        });        
    },
    
    loadProyectZonasObjectos: function(combo, newValue) {

        var proyecto   = newValue,
            zonaCombo  = Ext.getCmp('resumenformZona');
        
        zonaCombo.setDisabled(false);
        zonaCombo.setValue('');

        zonaCombo.getStore().getProxy().setExtraParam("proyecto", proyecto);
        zonaCombo.getStore().load();        
    },
    
    nuevoInforme: function(grid, record) {

        var agregar = Ext.create('SEMTI.view.informes.ResumenForm');
        agregar.setTitle('Nuevo Informe Resumen de Garant\xEDa');
        Ext.getCmp('resumen_form_titulo').focus();
    },
    
    editarInforme: function(grid, record) {

        var grid       = this.getInformesResumen(),
            record     = grid.getSelectionModel().getSelection()[0],
            id_informe = record.get('id');
        
        var editar = Ext.create('SEMTI.view.informes.ResumenForm'),
            titulo = Ext.getCmp('resumen_form_titulo'),
            zona   = Ext.getCmp('resumenformZona');

        editar.setTitle('Modificar Informe de Garant\xEDa');
        editar.show();
        titulo.focus();
        zona.setDisabled(false);

        var form = editar.down('form');

        form.getForm().load({
            url: './php/informes/ResumenActions.php',
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
    
    actualizarResumen: function(button) {

        var win        = button.up('window'),
            form       = win.down('form'),
            values     = form.getValues(),
            zona       = values.zona,
            controller = this,
            grid       = controller.getInformesResumen()
            store      = controller.getResumenStore();
            //gridHfo    = controller.getInformesHfo(),
            //HfoRecords = gridHfo.getChecked;

        if (form.isValid()) {

            if (values.id > 0) { //Si Hay algun Valor, entra en Modo de Actualizacion

                ////     ACTUALIZAR   

                form.getForm().submit({
                    method: 'POST',
                    //submitEmptyText: false,
                    url: './php/informes/ResumenActions.php',
                    waitTitle: 'Espere', //Titulo del mensaje de espera
                    waitMsg: 'Procesando datos...', //Mensaje de espera
                    params: {
                        accion: 'ResumenUdate', zonaValue: zona.toString()
                    },
                    success: function() {
                        win.close();
                        store.load();
                        //controller.loaDataHFO(gridHfo,HfoRecords);
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
                    url: './php/informes/ResumenActions.php',
                    //submitEmptyText: false,
                    waitTitle: 'Espere', //Titulo del mensaje de espera
                    waitMsg: 'Procesando datos...', //Mensaje de espera
                    params: {
                        accion: 'ResumenInsert', zonaValue: zona.toString()
                    },
                    success: function() {
                        win.close();
                        //store.load();
                        grid.getStore().load({
                            scope: this,
                            callback: function(records, operation, success) {
                                var record = grid.getStore().getAt(0);
                                grid.getSelectionModel().select(record);
                            }
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
        }
    },
    
    deleteInforme: function(gridView, rowIndex, colIndex, column, e, record) {

        var grid = this.getInformesResumen();
        /*if(grid.getSelectionModel().getSelection()[0]) {
            record = grid.getSelectionModel().getSelection()[0];
        }*/
        
        var id = record.get('id');

        Ext.Msg.confirm("Confirmaci\xF3n", "El informe ser\xE1 eliminado definitivamente del sistema. Confirma que desea realizar esta operaci\xF3n?", function(btnText) {
            if (btnText === "yes") {

                grid.el.mask('Eliminando...', 'x-mask-loading');

                Ext.Ajax.request({ //dispara la petición

                    url: './php/informes/ResumenActions.php',
                    method: 'POST',
                    params: {accion: 'ResumenDelete', id: id},
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
                            grid.getStore().load({
                                scope: this,
                                callback: function(records, operation, success) {
                                    if (grid.getStore().count() > 0) {
                                        var record = grid.getStore().getAt(0);
                                        grid.getSelectionModel().select(record);
                                    }
                                    
                                }
                            });
                            //var record = grid.getStore().getAt(0);
                            //grid.getSelectionModel().select(record);
                            //grid.fireEvent('selectionchange', grid, record);
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
    }

});