Ext.define('SEMTI.view.proyectos.ProyectForm', {
    extend: 'Ext.window.Window',
    alias: 'widget.proyectform',
    requires: ['Ext.form.Panel', 'Ext.form.field.Text'],
    layout: 'fit',
    autoShow: true,
    width: 500,
    modal: true,
    iconCls: 'icon_proyecto',
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
                    margin: '0 0 10 0',
                    labelWidth: 120,
                    combineErrors: true,
                    msgTarget: 'side'
                },
                items: [{
                        xtype: 'textfield',
                        name: 'id',
                        hidden: true
                    }, {
                        xtype: 'poloscombo',
                        id: 'formproyectospolo',
                        allowBlank: false,
                        flex: 1,
                        margin: '0 0 10 0',
                        name: 'polo',
                        emptyText: 'Elija el Polo del proyecto',
                        fieldLabel: 'Polo Tur&iacute;stico'
                     }, {
                        xtype: 'textfield',
                        allowBlank: false,
                        fieldLabel: 'Nombre Proyecto',
                        emptyText: 'Escriba el nombre',
                        name: 'text'
                    }, {
                        xtype: 'textfield',
                        allowBlank: true,
                        fieldLabel: 'Nombre Comercial',
                        emptyText: 'Escriba el nombre',
                        name: 'nombre_comercial'
                    }, {
                        xtype: 'datefield',
                        editable: false,
                        allowBlank: false,
                        name: 'fecha_inicio',
                        emptyText: 'Fecha en que inicia el periodo de Garant\xEDa',
                        fieldLabel: 'Inicio Garant\xEDa',
                        format: 'd/m/Y',
                        submitFormat: 'Y-m-d'
                    },{
                        xtype: 'datefield',
                        editable: false,
                        allowBlank: false,
                        name: 'fecha_terminacion',
                        emptyText: 'Fecha en que termina el periodo de Garant\xEDa',
                        fieldLabel: 'Final Garant\xEDa',
                        format: 'd/m/Y',
                        submitFormat: 'Y-m-d'
                    }, {
                        xtype: 'filefield',
                        id: 'form-file',
                        emptyText: 'Imagen Identificativa del Proyecto',
                        fieldLabel: 'Imagen',
                        name: 'imagen',
                        buttonText: 'Buscar...'
                    }, {
                        xtype: 'fieldcontainer',
                        combineErrors: true,
                        msgTarget: 'none', // qtip  title  under
                        //fieldLabel: 'Your Name',
                        //labelStyle: 'font-weight:bold;padding:0;',
                        layout: 'hbox',
                        //defaultType: 'textfield',
                        items: [{
                                xtype: 'numberfield',
                                name: 'presupuesto',
                                width: 370,
                                margin: '0 20 10 0',
                                emptyText: 'Presupuesto de Gasto de Garant\xEDa',
                                editable: true,
                                decimalSeparator: '.',
                                fieldLabel: 'Presupuesto'
                            },{
                                xtype: 'checkboxfield',
                                allowBlank: false,
                                name: 'activo',
                                fieldLabel: 'Activo',
                                labelWidth: 50,
                                flex: 1,
                                margin: '0 0 5 0',
                                labelAlign: 'right',
                                checked: true
                            }]
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

        let formproyecto_polo = Ext.getCmp('formproyectospolo'),
            store = formproyecto_polo.getStore();
        store.getProxy().setExtraParam('action', 'ProjectForm');
        store.load();
    }
});