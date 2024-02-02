Ext.define('SEMTI.controller.Garantia', {
    extend: 'Ext.app.Controller',
    stores: ['Gtiasd', 'Gtiagridtemp'],
    models: ['Gtiasd', 'Gtiagridtemp'],
    views: [
        'garantia.Gtiasd',
        'garantia.GtiaFormObjectGrid'
    ],
    refs: [{
            ref: 'archivoSD',
            selector: 'gtiasd'
        }, {
            ref: 'formSD',
            selector: 'gtiasdform'
        }, {
            ref: 'formFiltros',
            selector: 'gtiasdfiltros'
        }, {
            ref: 'reportSD',
            selector: 'gtiasdinforme'
        }, {
            ref: 'formDptos',
            selector: 'DptosForm'
        }, {
            ref: 'gridDptos',
            selector: 'DptosGrid'
        }, {
            ref: 'formProblemas',
            selector: 'gtiaproblemasform'
        }, {
            ref: 'gridProblemas',
            selector: 'gtiaproblemasgrid'
        }, {
            ref: 'sdformGrid',
            selector: 'GtiaFormObjectGrid'
    }],
    
    init: function() {

        this.control({
            '#ListarSDGroup': {
                change: this.listarSD
            },
            '#gtia_sd_busqueda_check': {
                change: this.checkFiltros
            },
            '#viewSDcomboProyects': {
                change: this.filterSDProyect
            },
            '#viewSDcomboEstado': {
                change: this.filterSDEstado
            },
            '#viewSDcomboTipo': {
                change: this.filterSDTipo
            },            
            'gtiasd': {
                itemmouseenter: this.showSdActionsGrid,
                itemmouseleave: this.hideSdActionsGrid
            },
            'gtiasd menu[lid=mainSD] menuitem[lid=newSD]': {
                click: this.nuevaSD
            },
            'gtiasd menu[lid=mainSD] menuitem[lid=actualizarArchivoSD]': {
                click: this.actualizarArchivoSD
            },
            'gtiasd actioncolumn[id=gtiasdColumnUpd]': {
                click: this.updateSD
            },
            'GtiaFormObjectGrid': {
                editclick: this.handleSdFormGridEdit,
                recordedit: this.updateSDFormGridElement,
                deleteclick: this.handleSdFormGridDelete
            },
            'gtiasdform button[action=guardar]': {
                click: this.actualizarSD
            },
            'gtiasdform button[action=guardar_cerrar]': {
                click: this.actualizarSD
            },
            'gtiasdform button[id=SdFormAddGrid]': {
                click: this.sdFormAddGrid
            },
            'gtiasd actioncolumn[id=gtiasdColumnDel]': {
                click: this.deleteSD
            },
            'gtiasd menu[lid=mainSD] menuitem[lid=delSD]': {
                click: this.deleteCheckSD
            },
            '#gtiasdColumnDel': {
                headerclick: this.deleteCheckSD
            },
            'gtiasd menu[lid=mainSD] menuitem[lid=filtros]': {
                click: this.filtroSD
            },
            'gtiasd button[action=buscar]': {
                click: this.filtroSD
            },
            'gtiasd dataview': {
                itemdblclick: this.updateDataviewSD
            },
            'gtiasd menu[lid=mainSD] menuitem[lid=updSD]': {
                click: this.updateDataviewSD
            },
            'gtiasd menu[lid=mainSD] menuitem[lid=reportSD]': {
                click: this.newReportSD
            },
            'gtiasdinforme button[action=generar]': {
                click: this.reportGtiasd
            },
            'gtiasdfiltros button[action=filtrar]': {
                click: this.setfiltroSD
            },            
            'DptosForm #DptosFormNombre': {
                specialkey: this.dptosFormHandleSpecialKey
            },
            'DptosGrid': {
                recordedit: this.dptosUpdateGridElement,
                deleteclick: this.dptosHandleGridDeleteIconClick,
                editclick: this.dptosGridhandleEditIconClick,
                itemmouseenter: this.showActionsDptosGrid,
                itemmouseleave: this.hideActionsDptosGrid
            },
            'gtiaproblemasform #GtiaproblemasFormDescripcion': {
                specialkey: this.problemasFormHandleSpecialKey
            },
            'gtiaproblemasgrid': {
                recordedit: this.problemasUpdateGridElement,
                deleteclick: this.problemasHandleGridDeleteIconClick,
                editclick: this.problemasGridhandleEditIconClick,
                itemmouseenter: this.showActionsProblemasGrid,
                itemmouseleave: this.hideActionsProblemasGrid
            }            
        });
    },
    /*******************************************************
     ********        SOLICITUDES DEFECTACION        ********
     ******************************************************/

    listarSD: function(rg, sel) {

        var selection = sel['listar_sd'];

        if (!(selection instanceof Object)) {

            var store = this.getGtiasdStore();
            store.getProxy().setExtraParam("listar", selection);
            store.load();
        }
    },
    filterSDProyect: function(combo, newValue) {

        var estado = Ext.getCmp('viewSDcomboEstado').value;
        if (estado === null) {
            estado = 'Todos';
        }
        var tiposd = Ext.getCmp('viewSDcomboTipo').value;
        if (tiposd === null) {
            tiposd = 'Todos';
        }
        if (newValue === null) {
            newValue = 'Todos';
        }

        var filtro = newValue + '.' + estado + '.' + tiposd,
            store  = this.getGtiasdStore();

        store.getProxy().setExtraParam("listar", filtro);
        store.load();
    },
    filterSDEstado: function(combo, newValue) {

        var proyecto = Ext.getCmp('viewSDcomboProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
        }
        var tiposd = Ext.getCmp('viewSDcomboTipo').value;
        if (tiposd === null) {
            tiposd = 'Todos';
        }
        if (newValue === null) {
            newValue = 'Todos';
        }

        var filtro = proyecto + '.' + newValue + '.' + tiposd,
            store  = this.getGtiasdStore();

        store.getProxy().setExtraParam("listar", filtro);
        store.load();
    },
    filterSDTipo: function(combo, newValue) {

        var proyecto = Ext.getCmp('viewSDcomboProyects').value;
        if (proyecto === null) {
            proyecto = 'Todos';
        }
        var estado = Ext.getCmp('viewSDcomboEstado').value;
        if (estado === null) {
            estado = 'Todos';
        }
        if (newValue === null) {
            newValue = 'Todas';
        }

        var filtro = proyecto + '.' + estado + '.' + newValue,
            store  = this.getGtiasdStore();

        store.getProxy().setExtraParam("listar", filtro);
        store.load();
    },
    
    checkFiltros: function(checkbox, newValue) {

        var store = this.getGtiasdStore();
        if (newValue === false) {
            Ext.getCmp('gtia_sd_busqueda_check').setVisible(false);
            Ext.getCmp('gtia_sd_busqueda_check').setValue(false);
            //Ext.getCmp('viewSDcomboProyects').setValue(null);
            //Ext.getCmp('viewSDcomboEstado').setValue(null);
            //checkbox.setValue(false);
            store.getProxy().setExtraParam("filtrar", "");
            store.load();
        }
    },
    
    newReportSD: function() {

        var informe = Ext.create('SEMTI.view.garantia.GtiasdInforme');
        informe.show();
        Ext.getCmp('gtiasd_informe_titulo').focus();
    },
    
    sdFormAddGrid: function(button) {

        var win = button.up('window'),
                form           = win.down('form'),
                cmp_objeto     = form.getForm().findField('objeto_parte'),
                cmp_ubicacion  = form.getForm().findField('ubicacion'),
                cmp_gridobject = Ext.getCmp('GtiaFormObjectGrid'),
                values         = form.getValues(),
                objetoparte    = values.objeto_parte,
                ubicacion      = values.ubicacion,
                estado         = values.objeto_parte_estado;
        
        if (objetoparte == '') {

            Ext.MessageBox.show({
                title: 'Mensaje del Sistema',
                msg: 'Debe seleccionar un Objeto o Parte antes de realizar esta operaci\xF3n.',
                icon: Ext.MessageBox.WARNING,
                buttons: Ext.Msg.OK
            });
        }
        else {

            // Agregar el objeto/parte a la nueva SD
            Ext.Ajax.request({
                url: './php/garantia/SdActions.php',
                method: 'POST',
                params: {accion: 'sdFormAddGrid', ruta: objetoparte, ubicacion: ubicacion, estado: estado},
                success: function(result, request) {
                    cmp_objeto.inputEl.dom.value = '';
                    cmp_ubicacion.setValue('');
                    cmp_gridobject.getStore().load();
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
    },
    
    handleSdFormGridEdit: function(gridView, rowIndex, colIndex, column, e) {

        var me = this,
                grid = me.getSdformGrid(),
                target = gridView.findTargetByEvent(e),
                record = gridView.getRecord(target);

        grid.getPlugin('sdFormGridEditing').startEdit(gridView.getRecord(record), 1);
    },
    updateSDFormGridElement: function(record) {

        var me        = this,
            grid      = me.getSdformGrid(),
            oldvalue  = record.oldvalue,
            newvalue  = record.newvalue,
            id_row    = record.id_row,
            ubicacion = record.ubicacion,
            estado    = record.estado;

        if (oldvalue == newvalue) {
            return;
        }

        Ext.Ajax.request({ //dispara la petición

            url: './php/garantia/SdActions.php',
            method: 'POST',
            params: {accion: 'updateSDFormGridElement', id_row: id_row, ubicacion: ubicacion, estado: estado},
            success: function(result, request) {
                grid.getStore().load();
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
    handleSdFormGridDelete: function(gridView, rowIndex, colIndex, column, e) {

        var target = gridView.findTargetByEvent(e),
                record = gridView.getRecord(target),
                id_row = record.get('id');

        // Eliminar el objeto/parte de la nueva SD
        Ext.Ajax.request({
            url: './php/garantia/SdActions.php',
            method: 'POST',
            params: {accion: 'sdFormDelGrid', id_row: id_row},
            success: function(result, request) {
                gridView.getStore().load();
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
    filtroSD: function() {

        var filtros = Ext.create('SEMTI.view.garantia.GtiasdFiltros');
        filtros.show();

        var win = this.getFormFiltros(),
                form = win.down('form'),
                objeto = form.getForm().findField('objeto_parte'),
                proyectcombo = Ext.getCmp('viewSDcomboProyects').getValue();

        // Si existe una Busqueda se cargan los datos
        if (Ext.getCmp('gtia_sd_busqueda_check').value === true || (proyectcombo != null && proyectcombo.length > 0)) {

            // Cargar datos del formulario
            form.getForm().load({
                url: './php/garantia/SdActions.php',
                params: {
                    accion: 'SdFiltrosLoad'
                },
                failure: function(form, action) {
                    Ext.MessageBox.show({
                        title: 'Carga Fallida',
                        msg: 'La carga de los parametros de la B\xFAsqueda Avanzada no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema.',
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.Msg.OK
                    });
                }
            });

            // Cargar datos del campo Objeto Parte
            Ext.Ajax.request({
                url: './php/garantia/SdActions.php',
                method: 'POST',
                params: {accion: 'SdFiltrosObjectLoad'},
                success: function(result, request) {
                    var jsonData = Ext.JSON.decode(result.responseText);
                    objeto.inputEl.dom.value = jsonData.objeto;
                    objeto.inputEl.dom.style = 'color: #000000; width: 100%';
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
        Ext.getCmp('gtiasd_filtros_desc').focus();
    },
    reportGtiasd: function(button) {

        var win = button.up('window'),
                form = win.down('form'),
                values = form.getValues(),
                check = win.down('checkboxfield'),
                grid = this.getArchivoSD(),
                store = grid.getStore(),
                total = store.getTotalCount();

        /*if (total > 500) {
         
         Ext.MessageBox.show({
         title: 'Mensaje del Sistema',
         msg: 'Solo se generan Informes de hasta 500 renglones. Por favor, establezca filtros de b\xFAsqueda para acotar de manera m\xE1s precisa el resultado de su informaci\xF3n.',
         buttons: Ext.MessageBox.OK,
         icon: Ext.MessageBox.WARNING
         });
         }
         else {*/

        var url, imagen = store.getAt(0).get('imagen');

        // Definir la url de generacion del informe
        if (values.formato === 'PDF') {
            url = './php/informes/pdf_Gtiasd.php';
        }
        else if (values.formato === 'PPT') {
            url = './php/informes/ppt_Gtiasd.php';
        }

        if (form.isValid()) {

            // Si se muestra la tabla en el informe        
            if (check.getValue() == true) {

                var recordsToSend = [];

                //store.sort('id','ASC');
                store.each(function(record) {
                    recordsToSend.push(Ext.apply(record.data));
                }, this);

                recordsToSend = Ext.encode(recordsToSend);
                var sdstore = recordsToSend;

                form.getForm().submit({
                    target: '_blank',
                    method: 'POST',
                    standardSubmit: true,
                    submitEmptyText: false,
                    url: url,
                    params: {
                        sdstore: sdstore, imagen: imagen, total_registros: total
                    }
                });                                
            }
            else {
                form.getForm().submit({
                    target: '_blank',
                    method: 'POST',
                    standardSubmit: true,
                    submitEmptyText: false,
                    url: url,
                    params: {
                        imagen: imagen, total_registros: total
                    }
                });
            }
            
            window.setTimeout(function(){
                win.close();
            },3000);
        }
        // }

    },
    setfiltroSD: function(button) {

        var win          = button.up('window'),
            form         = win.down('form'),
            compra_imp   = form.getForm().findField('compra_imp'),
            compra_nac   = form.getForm().findField('compra_nac'),
            values       = form.getValues(),
            dpto         = values.dpto,
            store        = this.getGtiasdStore(),
            count        = 0,
            fieldsToSend = '';

        // Validar Filtros
        if (values.descripcion.length > 0)
            count++;
        if (values.numero.length > 0)
            count++;
        if (values.problema.length > 0)
            count++;
        if (values.objeto_parte.length > 0)
            count++;
        if (values.dpto.length > 0)
            count++;
        if (values.constructiva.length > 0)
            count++;
        if (values.suministro.length > 0) {
            // Validar Compra
            if (compra_imp.checked == false && compra_nac.checked == false) {
                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: 'Debe seleccionar al menos un Tipo de Compra para las SD que necesiten Suministros.',
                    icon: Ext.MessageBox.WARNING,
                    buttons: Ext.Msg.OK
                });
                return;
            }
            count++;
        }
        if (values.afecta_explotacion.length > 0)
            count++;
        if (values.reportes_desde.length > 0)
            count++;
        if (values.reportes_hasta.length > 0)
            count++;
        if (values.solucion_desde.length > 0)
            count++;
        if (values.solucion_hasta.length > 0)
            count++;
        if (values.demora === 'on')
            count++;
        if (values.hascosto === 'on')
            count++;

        if (count == 0) {

            Ext.MessageBox.show({
                title: 'Mensaje del Sistema',
                msg: 'Debe seleccionar al menos un filtro para realizar la B\xFAsqueda Avanzada.',
                icon: Ext.MessageBox.WARNING,
                buttons: Ext.Msg.OK
            });
        }
        else {

            fieldsToSend  = values.descripcion + '*';
            fieldsToSend += values.numero + '*';
            fieldsToSend += dpto.toString() + '*';
            fieldsToSend += values.problema + '*';
            fieldsToSend += values.objeto_parte + '*';
            //fieldsToSend += values.estado + '*';
            fieldsToSend += values.constructiva + '*';
            fieldsToSend += values.afecta_explotacion + '*';
            fieldsToSend += values.suministro + '*';
            fieldsToSend += values.reportes_desde + '*';
            fieldsToSend += values.reportes_hasta + '*';
            fieldsToSend += values.solucion_desde + '*';
            fieldsToSend += values.solucion_hasta + '*';
            if (values.demora === 'on') {
                fieldsToSend += values.demora + '*';
                fieldsToSend += values.criteriodemora + '*';
                fieldsToSend += values.diasdemora;
            } else {
                fieldsToSend += '*';
                fieldsToSend += '*';
                fieldsToSend += '-*';
            }
            if (values.suministro == 1) {
                fieldsToSend += compra_imp.checked + '*';
                fieldsToSend += compra_nac.checked;
            } else {
                fieldsToSend += '*';
                fieldsToSend += '*';
            }
            if (values.hascosto === 'on') {
                fieldsToSend += values.hascosto + '*';
                fieldsToSend += values.criteriocosto + '*';
                fieldsToSend += values.costo;
            } else {
                fieldsToSend += '*';
                fieldsToSend += '*';
                fieldsToSend += '-*';
            }

            win.close();
            Ext.getCmp('gtia_sd_busqueda_check').setVisible(true);
            Ext.getCmp('gtia_sd_busqueda_check').setValue(true);
            //Ext.getCmp('gtia_sd_paging').setDisabled(true);
            store.getProxy().setExtraParam("filtrar", fieldsToSend);
            store.load();
        }
    },
    actualizarArchivoSD: function() {

        var grid = this.getArchivoSD();
        grid.getStore().load();
    },
    nuevaSD: function(grid, record) {

        var agregar = Ext.create('SEMTI.view.garantia.GtiasdForm'),
                storeTemp = this.getGtiagridtempStore();

        storeTemp.load();
        agregar.setTitle('Nueva Solicitud de Defectaci\xF3n');
        Ext.getCmp('gtiasd_form_descripcion').focus();
    },
    updateSD: function(gridView, rowIndex, colIndex, column, e, record) {

        var id_sd     = record.get('id'),
            grid      = this.getArchivoSD(),
            storeTemp = this.getGtiagridtempStore();

        var editar = Ext.create('SEMTI.view.garantia.GtiasdForm');
        editar.setTitle('Modificar Solicitud de Defectaci\xF3n');
        editar.show();
        
        var win = this.getFormSD(),
            form = win.down('form'),
            button = Ext.getCmp('guardar_cerrar');

        button.setVisible(false);

        // Cargar datos del formulario
        form.getForm().load({
            url: './php/garantia/SdActions.php',
            params: {
                accion: 'SdFormLoad', id_sd: id_sd
            },
            failure: function(form, action) {
                editar.close();
                Ext.MessageBox.show({
                    title: 'Carga Fallida',
                    msg: 'La carga de los parametros de la Solicitud de Defectaci\xF3n no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema.',
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        });

        // Cargar Objetos/Partes en la tabla temporal
        Ext.Ajax.request({
            url: './php/garantia/SdActions.php',
            method: 'POST',
            params: {accion: 'SdFormObjectLoad', id_sd: id_sd},
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
                    storeTemp.load();
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

        Ext.getCmp('gtiasd_form_descripcion').focus();
    },
    updateDataviewSD: function() {

        var grid = this.getArchivoSD(),
            record = grid.getSelectionModel().getSelection()[0],
            id_sd = record.get('id'),
            storeTemp = this.getGtiagridtempStore(),
            editar = Ext.create('SEMTI.view.garantia.GtiasdForm');

        editar.setTitle('Modificar Solicitud de Defectaci\xF3n');
        editar.show();

        console.log(id_sd);

        var win = this.getFormSD(),
                form = win.down('form'),
                //objeto  = form.getForm().findField('objeto_parte'),
                button = Ext.getCmp('guardar_cerrar');

        button.setVisible(false);

        // Cargar datos del formulario
        form.getForm().load({
            url: './php/garantia/SdActions.php',
            params: {
                accion: 'SdFormLoad', id_sd: id_sd
            },
            success: function() {
                var tipo_compra = Ext.getCmp('sdFormTipoCompra').getValue();
                if (tipo_compra == 'Inv') {
                    Ext.getCmp('SdFormCompra').setValue({compra: 'Imp'})
                }
                if (tipo_compra == 'Nac') {
                    Ext.getCmp('SdFormCompra').setValue({compra: 'Nac'})
                }
            },
            failure: function(form, action) {
                editar.close();
                Ext.MessageBox.show({
                    title: 'Carga Fallida',
                    msg: 'La carga de los parametros de la Solicitud de Defectaci\xF3n no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema.',
                    icon: Ext.MessageBox.ERROR,
                    buttons: Ext.Msg.OK
                });
            }
        });

        // Cargar datos del campo Objeto Parte
        Ext.Ajax.request({
            url: './php/garantia/SdActions.php',
            method: 'POST',
            params: {accion: 'SdFormObjectLoad', id_sd: id_sd},
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
                    storeTemp.load();
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

        Ext.getCmp('gtiasd_form_descripcion').focus();
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
    actualizarSD: function(button) {

        var win      = button.up('window'),
            form     = win.down('form'),
            values   = form.getValues(),
            dptos    = values.dpto,
            grid     = this.getSdformGrid(),
            store    = this.getGtiasdStore(),
            cmp_grid = Ext.getCmp('GtiaFormObjectGrid');

        if (form.isValid()) {

            // Capturar los Objetos o Locales que fueron asignados a la SD
            var grid_data = grid.getStore();

            if (grid_data.getCount() > 0) {            //!Ext.isEmpty(grid_data)

                var recordsToSend = [];

                grid_data.each(function(record) {
                    recordsToSend.push(Ext.apply(record.data));
                }, this);

                //recordsToSend = Ext.encode(recordsToSend);
                recordsToSend = JSON.stringify(recordsToSend);
                var objectArray = recordsToSend;

                if (values.id > 0) { //Si Hay algun Valor, entra en Modo de Actualizacion

                    ////     ACTUALIZAR   

                    form.getForm().submit({
                        method: 'POST',
                        submitEmptyText: false,
                        url: './php/garantia/SdActions.php',
                        waitTitle: 'Espere', //Titulo del mensaje de espera
                        waitMsg: 'Procesando datos...', //Mensaje de espera
                        params: {
                            accion: 'SdUdate', objectArray: objectArray, dptoValue: dptos.toString()
                        },
                        success: function() {
                            Ext.getCmp('gtiasd_form_problema').getStore().load();
                            win.close();
                            store.load();
                        },
                        failure: function(form, action) {
                            var data = Ext.decode(action.response.responseText);
                            console.log(data);
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
                        url: './php/garantia/SdActions.php',
                        submitEmptyText: false,
                        waitTitle: 'Espere', //Titulo del mensaje de espera
                        waitMsg: 'Procesando datos...', //Mensaje de espera
                        params: {
                            accion: 'SdInsert', objectArray: objectArray, dptoValue: dptos.toString()
                        },
                        success: function() {
                            if (button.getId() === 'guardar') {
                                form.getForm().reset();
                                Ext.getCmp('gtiasd_form_objeto').inputEl.dom.value = '';
                                Ext.getCmp('gtiasd_form_problema').getStore().load();
                                Ext.getCmp('gtiasd_form_descripcion').focus();
                                cmp_grid.getStore().load();
                            }
                            else {
                                Ext.getCmp('gtiasd_form_problema').getStore().load();
                                win.close();
                            }
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
            else {

                Ext.MessageBox.show({
                    title: 'Mensaje del Sistema',
                    msg: 'No ha seleccionado los Objetos o Locales que ser\xE1n defectados en esta Solicitud de Defectaci\xF3n. Por favor, complete este par\xE1metro del formulario y vuelva a enviar la información.',
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.WARNING
                });
            }
        }
    },
    deleteSD: function(gridView, rowIndex, colIndex, column, e, record) {

        var id_sd = record.get('id'),
                grid = this.getArchivoSD();

        Ext.Msg.confirm("Confirmaci\xF3n", "La Solicitud de Defectaci\xF3n seleccionada ser\xE1 eliminada definitivamente del sistema. Confirma que desea realizar esta operaci\xF3n?", function(btnText) {
            if (btnText === "yes") {

                grid.el.mask('Eliminando...', 'x-mask-loading');

                Ext.Ajax.request({ //dispara la petición

                    url: './php/garantia/SdActions.php',
                    method: 'POST',
                    params: {accion: 'SdDelete', idSD: id_sd},
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
    deleteCheckSD: function() {

        var grid      = this.getArchivoSD(),
            selection = grid.getSelectionModel().getSelection();

        if (!Ext.isEmpty(selection)) {

            Ext.Msg.confirm("Confirmaci\xF3n", "Las Solicitudes de Defectaci\xF3n seleccionadas ser\xE1n eliminadas definitivamente del sistema. Confirma que desea realizar esta operaci\xF3n?", function(btnText) {
                if (btnText === "yes") {

                    grid.el.mask('Eliminando...', 'x-mask-loading');

                    var recordsToSend = [];

                    Ext.each(selection, function(record) {
                        recordsToSend.push(Ext.apply(record.data));
                    });

                    recordsToSend = Ext.encode(recordsToSend);
                    var parametros = recordsToSend;

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/garantia/SdActions.php',
                        method: 'POST',
                        params: {accion: 'SdCheckDelete', parametros: parametros},
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
                msg: 'Debe seleccionar al menos una Solicitud de Defectaci\xF3n para realizar esta operaci\xF3n.',
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.WARNING
            });
        }
    },
    /******************************************************
     ********      FIN SOLICITUDES DEFECTACION      ********
     ******************************************************/


    /************************************
     ********        DPTOS        ********
     ************************************/

    showActionsDptosGrid: function(view, task, node, rowIndex, e) {

        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon) {
            Ext.get(icon).removeCls('x-hidden');
        });
    },
    hideActionsDptosGrid: function(view, task, node, rowIndex, e) {

        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon) {
            Ext.get(icon).addCls('x-hidden');
        });
    },
    dptosFormHandleSpecialKey: function(field, e) {

        if (e.getKey() === e.ENTER) {
            this.newGridDptosElement();
        }
    },
    dptosGridhandleEditIconClick: function(view, rowIndex, colIndex, column, e) {

        var me = this,
                grid = me.getGridDptos(),
                record = view.findTargetByEvent(e);

        grid.getPlugin('elementGridNameEditing').startEdit(view.getRecord(record), 1);
    },
    newGridDptosElement: function() {

        var me = this,
                grid = me.getGridDptos(),
                form = me.getFormDptos(),
                nameField = form.getForm().findField('nombre');

        // require title field to have a value
        if (!nameField.getValue()) {
            return;
        }

        form.getForm().submit({
            method: 'POST',
            url: './php/sistema/DptosActions.php',
            waitTitle: 'Espere', //Titulo del mensaje de espera
            waitMsg: 'Enviando datos...', //Mensaje de espera
            params: {
                accion: 'InsertarDpto'
            },
            success: function() {
                nameField.reset();
                grid.getStore().load();
                nameField.focus();
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
    dptosUpdateGridElement: function(record) {

        var me = this,
                grid = me.getGridDptos(),
                id = record.get('id'),
                nombre = record.get('nombre');

        // require title field to have a value
        if (!record.get('nombre')) {
            grid.getStore().load();
            return;
        }

        Ext.Ajax.request({ //dispara la petición

            url: './php/sistema/DptosActions.php',
            method: 'POST',
            params: {accion: 'ActualizarDpto', id: id, nombre: nombre},
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
    dptosHandleGridDeleteIconClick: function(view, rowIndex, colIndex, column, e) {

        var grid = this.getGridDptos();
        this.dptosDeleteElementGrid(grid.getStore().getAt(rowIndex));
    },
    dptosDeleteElementGrid: function(record, successCallback) {

        var me = this,
                grid = me.getGridDptos(),
                id_dpto = record.get('id');

        Ext.Msg.show({
            title: 'Confirmaci\xF3n',
            msg: 'La eliminaci\xF3n de este Departamento puede generar perdidas de informaci\xF3n en el sistema. Confirma que desea realizar esta operaci\xF3n?',
            buttons: Ext.Msg.YESNO,
            icon: Ext.MessageBox.WARNING,
            fn: function(response) {
                if (response === 'yes') {

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/sistema/DptosActions.php',
                        method: 'POST',
                        params: {accion: 'EliminarDpto', id: id_dpto},
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
    /************************************
     ********      FIN DPTOS      ********
     ************************************/


    /****************************************
     ********        PROBLEMAS        ********
     ****************************************/

    showActionsProblemasGrid: function(view, task, node, rowIndex, e) {

        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon) {
            Ext.get(icon).removeCls('x-hidden');
        });
    },
    hideActionsProblemasGrid: function(view, task, node, rowIndex, e) {

        var icons = Ext.DomQuery.select('.x-action-col-icon', node);
        Ext.each(icons, function(icon) {
            Ext.get(icon).addCls('x-hidden');
        });
    },
    problemasFormHandleSpecialKey: function(field, e) {

        if (e.getKey() === e.ENTER) {
            this.newGridProblemasElement();
        }
    },
    problemasGridhandleEditIconClick: function(view, rowIndex, colIndex, column, e) {

        var me = this,
                grid = me.getGridProblemas(),
                record = view.findTargetByEvent(e);

        grid.getPlugin('elementGridNameEditing').startEdit(view.getRecord(record), 1);
    },
    newGridProblemasElement: function() {

        var me = this,
                grid = me.getGridProblemas(),
                form = me.getFormProblemas(),
                nameField = form.getForm().findField('descripcion');

        // require title field to have a value
        if (!nameField.getValue()) {
            return;
        }

        form.getForm().submit({
            method: 'POST',
            url: './php/garantia/GtiaproblemasActions.php',
            waitTitle: 'Espere', //Titulo del mensaje de espera
            waitMsg: 'Enviando datos...', //Mensaje de espera
            params: {
                accion: 'InsertarProblema'
            },
            success: function() {
                nameField.reset();
                grid.getStore().load();
                nameField.focus();
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
    problemasUpdateGridElement: function(record) {

        var me = this,
                grid = me.getGridProblemas(),
                id = record.get('id'),
                descrip = record.get('descripcion');

        // require title field to have a value
        if (!record.get('descripcion')) {
            grid.getStore().load();
            return;
        }

        Ext.Ajax.request({ //dispara la petición

            url: './php/garantia/GtiaproblemasActions.php',
            method: 'POST',
            params: {accion: 'ActualizarProblema', id: id, descrip: descrip},
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
    problemasHandleGridDeleteIconClick: function(view, rowIndex, colIndex, column, e) {

        var grid = this.getGridProblemas();
        this.problemasDeleteElementGrid(grid.getStore().getAt(rowIndex));
    },
    problemasDeleteElementGrid: function(record, successCallback) {

        var me = this,
                grid = me.getGridProblemas(),
                id_problema = record.get('id');

        Ext.Msg.show({
            title: 'Confirmaci\xF3n',
            msg: 'La eliminaci\xF3n de este Tipo de Problema puede generar perdidas de informaci\xF3n en el sistema. Confirma que desea realizar esta operaci\xF3n?',
            buttons: Ext.Msg.YESNO,
            icon: Ext.MessageBox.WARNING,
            fn: function(response) {
                if (response === 'yes') {

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/garantia/GtiaproblemasActions.php',
                        method: 'POST',
                        params: {accion: 'EliminarProblema', id: id_problema},
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

    /****************************************
     ********      FIN PROBLEMAS      ********
     ****************************************/

});