// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('SEMTI.view.garantia.GtiasdForm', {
    extend: 'Ext.window.Window',
    alias : 'widget.gtiasdform',
 
    requires: [
        'Ext.form.Panel',
        'Ext.form.field.Text',
        'Ext.form.field.ComboBox',
        'Ext.ux.TreePicker',
        'SEMTI.view.sistema.SystDptos',
        'SEMTI.view.garantia.Gtiaproblemas',
        'SEMTI.view.garantia.GtiasdEstado',
        'SEMTI.view.proyectos.ProyectsPicker',
        'SEMTI.view.proyectos.ProyectsCombo',
        'SEMTI.view.garantia.GtiasdTipoCompra'
    ],
    
    listeners: {
        'close': function(panel, e) {    
            var id_sd = this.down('#sdFormIdField').getValue();       
            if(id_sd > 0){          
                Ext.Ajax.request({
                    url: './php/garantia/SdActions.php',
                    method:'POST', 
                    params:{accion: 'SdCleanGridTemp'},
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
    },
 
    layout: 'fit',
    autoShow: true,
    width: 900,
    resizable: false,
    modal: true,
     
    iconCls: 'icon_SD',
 
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: '5 20 20 20',
            border: false,
            modal: true,
            style: 'background-color: #fff;',
                 
            fieldDefaults: {
            	anchor: '100%',
                labelAlign: 'left',
                labelWidth: 90,
                margin: '10 0 10 0',
                combineErrors: true,
                msgTarget: 'side'
            },
            items: [{
            	xtype: 'textfield',
                name: 'id',
                id: 'sdFormIdField',
                fieldLabel: 'id',
                hidden: true
            },{
            	xtype: 'textfield',
                id: 'sdFormTipoCompra',
                name: 'tipo_compra',
                fieldLabel: 'id',
                hidden: true
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                height: 45,
                msgTarget: 'none',
                padding: 0,
                items: [{
                    xtype: 'textfield',
                    flex: 1,
                    allowBlank: false,
                    name: 'descripcion',
                    emptyText: 'Describa el problema que desea reportar',
                    id: 'gtiasd_form_descripcion',
                    fieldLabel: 'Descripci\xF3n del problema',
                    labelAlign: 'top',
                    margin: '0 10 0 0'
                },{
                    xtype: 'textfield',
                    width: 70,
                    allowBlank: false,
                    name: 'numero',
                    fieldLabel: 'N\xFAmero',
                    labelAlign: 'top',
                    regex: /[0-9]+$/,
                    //minLength: 4,
                    margin: '0 10 10 0'
                },{
                    xtype: 'datefield',
                    editable: false,
                    width: 150,
                    margin: '0 0 10 0',
                    allowBlank: false,
                    name: 'fecha_reporte',
                    labelAlign: 'top',
                    fieldLabel: 'Fecha del Reporte',
                    labelWidth: 110,
                    format: 'd/m/Y',
                    emptyText: 'dd/mm/aaaa',
                    submitFormat: 'Y-m-d'
                }]
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                msgTarget: 'none',
                margin: '0 0 0 0',
                padding: '0 0 0 0',
                items: [{
                    xtype: 'ProyectsCombo',
                    allowBlank: false,
                    flex: 1,
                    margin: '0 10 0 0',
                    editable: false,
                    name: 'proyecto',
                    labelAlign: 'top',
                    fieldLabel: 'Proyecto'
                    /*listeners: {
                        'change': function(combo, newValue) {
                            if (newValue === '' || newValue === null) {
                                Ext.getCmp('sdFormFieldsetObjetosPartes').setDisabled(true);
                                Ext.getCmp('sdFormFieldsetObjetosPartes').setDisabled(true);
                            }
                            else {
                                
                                Ext.getCmp('sdFormFieldsetObjetosPartes').setDisabled(false);
                                Ext.getCmp('sdFormFieldsetObjetosPartes').setDisabled(false);
                                
                                var proyectPicker = Ext.getCmp('gtiasd_form_objeto');
                                proyectPicker.getPicker().getStore().getProxy().setExtraParam("loadProyect", newValue);
                                proyectPicker.getStore().load();
                            }
                        }
                    }*/
                 },{
                    xtype: 'SystDptos',
                    allowBlank: false,
                    flex: 1,
                    margin: '0 10 0 0',
                    editable: true,
                    name: 'dpto',
                    labelAlign: 'top',
                    emptyText: 'Responsable del problema',
                    fieldLabel: 'Departamento'
                 },{
                    xtype: 'Gtiaproblemascombo',
                    id: 'gtiasd_form_problema',
                    allowBlank: true,
                    flex: 1,
                    editable: true,
                    labelAlign: 'top',
                    margin: '0 0 10 0',
                    name: 'problema',
                    emptyText: 'Problema al que pertenece',
                    fieldLabel: 'Tipo Problema',
                    regex: /^[a-zA-Z áéíóúAÉÍÓÚÑñ]+$/
                 }]
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                msgTarget: 'none',
                margin: '0 0 0 0',
                padding: '0 0 0 0',
                items: [
                    {
                        xtype: 'textfield',
                        flex: 1,
                        allowBlank: true,
                        name: 'causa',
                        emptyText: 'Describa la causa que origin\xF3 el problema',
                        id: 'gtiasd_form_causa',
                        fieldLabel: 'Causa del problema',
                        labelAlign: 'top',
                        margin: '0 0 0 0'
                    }
                ]
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                margin: '7 0 5 0',
                items: [{
                    xtype: 'checkboxfield',
                    allowBlank: false,
                    name: 'constructiva',
                    fieldLabel: 'Constructiva',
                    width: 95,
                    labelWidth: 75,
                    labelAlign: 'left',
                    //checked: false
                },{
                    xtype: 'checkboxfield',
                    allowBlank: false,
                    name: 'afecta_explotacion',
                    fieldLabel: 'AEH',
                    width: 70,
                    margin: '10 0 0 30',
                    labelWidth: 50,
                    labelAlign: 'right',
                    checked: false
                },{
                    xtype: 'checkboxfield',
                    allowBlank: false,
                    name: 'suministro',
                    fieldLabel: 'Suministro',
                    width: 100,
                    margin: '10 0 0 30',
                    labelWidth: 80,
                    labelAlign: 'right',
                    checked: false,
                    listeners: {
                            'change': function(checkbox, newValue, oldValue, eOpts) {
                                    if(newValue == true){
                                            Ext.getCmp('SdFormCompra').setVisible(true);
                                            Ext.getCmp('SdFormCompraAlmacen').setVisible(true);
                                    }
                                    else{
                                            Ext.getCmp('SdFormCompra').setVisible(false);
                                            Ext.getCmp('SdFormCompraAlmacen').setVisible(false);
                                    }
                            }
                    }
                },{
                    xtype: 'GtiasdTipoCompra',
                    id: 'SdFormCompra',
                    hidden: true,
                    editable: false,
                    width: 240,
                    name: 'compra',
                    value: 'Interna',
                    fieldLabel: 'Tipo',
                    labelAlign: 'right',
                    labelWidth: 60,
                    margin: '10 0 0 10'
                 }, {
                    xtype: 'datefield',
                    id: 'SdFormCompraAlmacen',
                    hidden: true,
                    editable: false,
                    width: 275,
                    margin: '10 0 0 0',
                    allowBlank: true,
                    name: 'fecha_almacen',
                    labelAlign: 'top',
                    fieldLabel: 'Arribo al Almacen',
                    labelAlign: 'right',
                    labelWidth: 135,
                    format: 'd/m/Y',
                    emptyText: 'dd/mm/aaaa',
                    submitFormat: 'Y-m-d'
                }]
            },{
                xtype: 'fieldset',
                id: 'sdFormFieldsetObjetosPartes',
                flex: 1,
                disabled: false,
                collapsible: true,
                collapsed: false,
                title: 'Objectos/Locales',
                //defaultType: 'datefield', // each item will be a checkbox
                layout: 'anchor',
                padding: '0 20 0 20',
                margin: '0 0 10 0',
                baseCls: 'x-fieldset',
                defaults: {
                        anchor: '100%',
                        labelWidth: 100,
                        hideEmptyLabel: true
                },
                items: [{
                    xtype: 'fieldcontainer',
                    layout: 'hbox',
                    //height: 45,
                    msgTarget: 'none',
                    padding: 0,
                    items: [{
                        xtype: 'proyectsPicker',
                        id: 'gtiasd_form_objeto',
                        name: 'objeto_parte',
                        flex: 1,
                        labelAlign: 'top',
                        fieldLabel: 'Objeto/Local',
                        margin: '0 10 0 0'
                    },{
                        xtype: 'textfield',
                        allowBlank: true,
                        name: 'ubicacion',
                        width: 250,
                        labelAlign: 'top',
                        emptyText: 'Ubicaci\xF3n del problema',
                        fieldLabel: 'Ubicaci\xF3n',
                        margin: '0 10 0 0'
                    },{
                        xtype: 'gtiasdestado',
                        allowBlank: true,
                        name: 'objeto_parte_estado',
                        width: 130,
                        labelAlign: 'top',
                        value: 'Por Resolver',
                        fieldLabel: 'Estado',
                        margin: '0 10 0 0'
                    },{
                        xtype: 'button',
                        id: 'SdFormAddGrid',
                        text : 'Agregar',
                        margin: '20 0 0 0'
                    }]
                },{
                    xtype: 'GtiaFormObjectGrid',
                    name: 'GtiaFormObjectGrid',
                    id: 'GtiaFormObjectGrid',
                    flex: 1,
                    height: 100,
                    cls: 'grid-body-border-style',
                    margin: '0 0 20 0'
                 },] 
         },{
            xtype: 'fieldset',
            flex: 1,
            collapsible: true,
            collapsed: false,
            title: 'Datos Complementarios',
            //defaultType: 'datefield', // each item will be a checkbox
            layout: 'anchor',
            padding: '0 20 0 20',
            margin: '0 0 0 0',
            defaults: {
                    anchor: '100%',
                    labelWidth: 100,
                    hideEmptyLabel: true
            },
            items: [{
                    xtype: 'fieldcontainer',
                    layout: 'hbox',
                    //height: 45,
                    msgTarget: 'none',
                    padding: 0,
                    items: [{
                        xtype: 'gtiasdestado',
                        allowBlank: false,
                        editable: false,
                        width: 130,
                        name: 'estado',
                        value: 'Por Resolver',
                        fieldLabel: 'Estado',
                        labelAlign: 'top',
                        labelWidth: 45,
                        margin: '0 10 0 0'
                     },{
                        xtype: 'datefield',
                        editable: true,
                        flex: 1,
                        allowBlank: true,
                        name: 'fecha_solucion',
                        emptyText: 'Soluci\xF3n del reporte',
                        fieldLabel: 'Fecha Soluci\xF3n',
                        labelAlign: 'top',
                        labelWidth: 95,
                        format: 'd/m/Y',
                        submitFormat: 'Y-m-d',
                        margin: '0 10 10 0'
                    },{
                        xtype: 'filefield',
                        id: 'form-file',
                        flex: 2,
                        emptyText: 'Documento Escaneado',
                        labelAlign: 'top',
                        fieldLabel: 'Archivo Digital',
                        name: 'documento',
                        buttonText: 'Buscar...',
                        margin: '0 10 10 0'
                    }, {
                        xtype: 'numberfield',
                        minValue: 0,
                        allowBlank: true,
                        labelAlign: 'top',
                        fieldLabel: 'Costo ($)',
                        width: 100,
                        margin: '0 0 10 0',
                        name: 'costo',
                        step: .01,
                        emptyText: '1.00'
                    }]
            }/*{
                    xtype: 'textfield',
                    id: 'gtiaSDpresupuestofield',
                    allowBlank: true,
                    name: 'presupuesto',
                    fieldLabel: 'Presupuesto',
                    emptyText: 'Presupuesto utilizado en la soluci\xF3n del reporte',
                    regex: /[0-9]+$/
             },*/] 
         },{
            xtype: 'textfield',
            flex: 1,
            allowBlank: true,
            name: 'comentario',
            labelWidth: 70,
            labelAlign: 'top',
            fieldLabel: 'Comentario'
        }]
    }];
         
    this.dockedItems = [{
        xtype: 'toolbar',
        dock: 'bottom',
        id: 'buttons',
        ui: 'footer',
        items: [
            '->', 
            {
                cls: 'app-form-btn',
                text: '<i class="fas fa-check"></i>&nbsp;Guardar',
                action: 'guardar',
                id: 'guardar'
            },
            {
                cls: 'app-form-btn',
                text: '<i class="fas fa-check"></i>&nbsp;Guardar y Cerrar',
                action: 'guardar_cerrar',
                id: 'guardar_cerrar'
            },
            {
                cls: 'app-form-btn',
                text: '<i class="fas fa-times"></i>&nbsp;Cancelar',
                handler: function(button) {

                    var win = button.up('window');

                    Ext.Ajax.request({ //dispara la petición

                        url: './php/garantia/SdActions.php',
                        method: 'POST',
                        params: {accion: 'SdCleanGridTemp'},
                        success: function() {    //Función que se ejecutara si el parámetro devuelto por PHP es TRUE
                            win.close();
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
        ]
    }];
 
    this.callParent(arguments);
    }
});