Ext.define('SEMTI.view.garantia.GtiasdInforme', {
    extend: 'Ext.window.Window',
    alias: 'widget.gtiasdinforme',
    requires: [
        'Ext.form.Panel',
        'Ext.form.field.Text',
        'Ext.form.field.ComboBox',
        'SEMTI.view.sistema.FormatosCombo',
        /*'SEMTI.view.sistema.ProyectsPickerInformes',
        'SEMTI.view.garantia.GtiasdLocacionesCombo',
        'SEMTI.view.sistema.Ccodptos',
        'SEMTI.view.garantia.Gtiaproblemas',
        'SEMTI.view.garantia.GtiasdEstado',
        'SEMTI.view.garantia.GtiasdConSumAEH',
        'SEMTI.view.garantia.GtiasdCriterioDemora'*/
    ],
    layout: 'fit',
    autoShow: true,
    width: 800,
    resizable: false,
    modal: true,
    iconCls: 'icon_resumen',
    title: 'Configuraci\xF3n de los Par\xE1metros del Informe',
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                padding: '15 20 10 20',
                border: false,
                modal: true,
                style: 'background-color: #fff;',
                fieldDefaults: {
                    anchor: '100%',
                    labelWidth: 150,
                    combineErrors: true,
                    msgTarget: 'side',
                    margin: '0 0 10 0'
                },
                items: [{
                        xtype: 'textfield',
                        name: 'id',
                        fieldLabel: 'id',
                        hidden: true
                    }, {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        //height: 45,
                        msgTarget: 'none',
                        padding: 0,
                        items: [{
                            xtype: 'textfield',
                            id: 'gtiasd_informe_titulo',
                            allowBlank: false,
                            flex: 1,
                            fieldLabel: 'T\xEDtulo del Informe',
                            labelAlign: 'top',
                            maxLength: 100,
                            margin: '0 10 0 0',
                            name: 'titulo'
                        }, {
                            xtype: 'formatoscombo',
                            editable: false,
                            width: 230,
                            margin: '10 0 10 0',
                            allowBlank: false,
                            name: 'formato',
                            fieldLabel: 'Formato del Informe',
                            margin: '0 0 10 0',
                            labelAlign: 'top'
                        }]
                    },{
                        xtype: 'textareafield',
                        height: 130,
                        fieldLabel: 'Comentario al Inicio del Informe',
                        labelAlign: 'top',
                        //maxLength: 70,
                        name: 'comentario_inicio'
                    },{
                        xtype: 'textareafield',
                        height: 130,
                        fieldLabel: 'Comentario al Final del Informe',
                        labelAlign: 'top',
                        //maxLength: 70,
                        name: 'comentario_final'
                    }]
            }];

        this.dockedItems = [{
                xtype: 'toolbar',
                dock: 'bottom',
                id: 'buttons',
                ui: 'footer',
                items: [{
                        xtype: 'checkboxfield',
                        boxLabel  : 'Mostrar Listado de SD en el Informe (Hasta 150 renglones)',
                        name      : 'verlistado',
                        inputValue: '1',
                        margin: '0 0 0 12',
                        checked   : true
                    },'->', {
                        cls: 'app-form-btn',
                        text: '<i class="fas fa-check"></i>&nbsp;Aceptar',
                        action: 'generar'
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