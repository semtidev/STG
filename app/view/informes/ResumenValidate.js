// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('SEMTI.view.informes.ResumenValidate', {
    extend: 'Ext.window.Window',
    alias : 'widget.ResumenValidate',
    id: 'ResumenValidate',
    requires: [
        'Ext.form.Panel',
    ],
     
    layout: 'fit',
    autoShow: true,
    width: 430,
    resizable: false,
    modal: true,
    title: 'Generar Informe Resumen de Garant\xEDa',  
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
                name: 'exportto',
                hidden: true
            },{
                xtype: 'fieldcontainer',
                layout: 'hbox',
                msgTarget: 'none',
                margin: '5 0 5 0',
                padding: '0 0 0 0',
                items: [{
                    xtype: 'component',
                    width: 22,
                    height: 22,
                    cls: 'component_emotion',
                    margin: '15 5 0 0'
                },{
                    xtype: 'component',
                    width: 350,
                    style: 'text-align:justify',
                    html: 'Estimado usuario, estas son las secciones que usted ha validado y que ser\xE1n generadas en el informe CODIR. Si falta por validar alguna secci\xF3n puede mostrarla marcando la casilla correspondiente antes de generar el informe.',
                    margin: '15 0 10 0'
                }]
            },{
                xtype: 'checkboxfield',
                boxLabel  : 'Comentario Inicial',
                name      : 'comentini',
                margin: '0 0 5 20'
            }, {
                xtype: 'checkboxfield',
                boxLabel  : 'Estado de las SD',
                name      : 'estados',
                margin: '0 0 5 20'
            },{
                xtype: 'checkboxfield',
                boxLabel  : 'SD Pendientes Por Resolver',
                name      : 'sdpendientes',
                margin: '0 0 5 20'
            },{
                xtype: 'checkboxfield',
                boxLabel  : 'Principales Indicadores',
                name      : 'indicadores',
                margin: '0 0 5 20'
            },{
                xtype: 'checkboxfield',
                boxLabel  : 'Problemas con Mayor Repetitividad',
                name      : 'problemasrep',
                margin: '0 0 5 20'
            },{
                xtype: 'checkboxfield',
                boxLabel  : 'Habitaciones Fuera de Orden',
                name      : 'hfo',
                margin: '0 0 5 20'
            },{
                xtype: 'checkboxfield',
                boxLabel  : 'Comportamiento de las HFO',
                name      : 'comportamientohfo',
                margin: '0 0 5 20'
            },/*{
                xtype: 'checkboxfield',
                boxLabel  : 'Principales Deficiencias Constructivas',
                name      : 'deficonstruct',
                margin: '0 0 5 20'
            },*/{
                xtype: 'checkboxfield',
                boxLabel  : 'Comentario Final',
                name      : 'comentfin',
                margin: '0 0 5 20'
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
            action: 'generar',
            /*listeners: {
                'click': function(buttom) {
                    buttom.findParentByType('window').close();
                }
            }*/
        },{
            cls: 'app-form-btn',
            text: '<i class="fas fa-times"></i>&nbsp;Cancelar',
            scope: this,
            handler: this.close
        }]
    }];
 
    this.callParent(arguments);
    }
});