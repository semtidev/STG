Ext.define('SEMTI.view.informes.ResumenWindow', {
    extend: 'Ext.window.Window',
    alias : 'widget.ResumenWindow',
  	requires: [
        'SEMTI.view.informes.ResumendataFormComentInicial',
        'SEMTI.view.informes.ResumendataEstadosPanel',
        'SEMTI.view.informes.ResumendataFormComentFinal'
    ],
    listeners: {
        'close': function() {
            Ext.Ajax.request({ //dispara la petición
                url: './php/informes/ResumenActions.php',
                method: 'POST',
                params: {accion: 'ResumenSectionValidateClean'},
                success: function(result) {
                    var jsonData = Ext.JSON.decode(result.responseText);
                    if (jsonData.failure) {
                        Ext.MessageBox.show({
                            title: 'Mensaje del Sistema',
                            msg: 'Ha ocurrido un error en el Sistema. Por favor, vuelva a intentar realizar la operacion, de continuar el problema consulte al Administrador del Sistema.',
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
    },
    //layout: 'fit',
    autoShow: true,
    width: 1100,
    height: 750,
    resizable: false,
    modal: true,
    maximizable: true,
    //maximized: true,
    title: 'Informe Resumen de Garant\xEDa',
    /*layout: {
        type: 'vbox',    // Arrange child items vertically
        align: 'border'    // Each takes up full width: stretch
    },*/
    layout: 'border',
     
    iconCls: 'icon_resumen',
 
    initComponent: function() {
        this.items = [{
                region: 'north',
                xtype: 'ResumenGrid',
                id: 'resumen-grid-panel',
                height: 200
            },
            Ext.create('Ext.tab.Panel', {
				region: 'center', // una region central siempre es requerida para border layout
				deferredRender: false,
                listeners: {
                    'tabchange': function(tabPanel, newCard) {                        
                        var id_item = newCard.id;
                        Ext.Ajax.request({ //dispara la petición
                            url: './php/informes/ResumenActions.php',
                            method: 'POST',
                            params: {accion: 'ResumenSectionValidate', id: id_item},
                            success: function(result) {
                                var jsonData = Ext.JSON.decode(result.responseText);
                                if (jsonData.failure) {
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
                },
				//plugins: Ext.create('Ext.ux.TabReorderer'),
				id: 'ResumenTabpanel',
				activeTab: 0,
				// Primer Tab
				items: [{
                        title: 'Comentario Inicial',
                        xtype: 'ResumendataFormComentInicial'
                    },{
                        title: 'Estado de las SD',
                        xtype: 'ResumendataEstadosPanel',
                        id: 'ResumendataEstadosPanel'
                    },{
                        title: 'SD Pendientes Por Resolver',
                        xtype: 'ResumendataSDPendientesGrid',
                        autoScroll: true,
                        id: 'ResumendataSDPendientesGrid'
                    },{
                        title: 'Principales Indicadores',
                        xtype: 'ResumendataPIndicadoresGrid',
                        id: 'ResumendataPIndicadoresGrid'
                    },{
                        title: 'Problemas con Mayor Repetitividad',
                        xtype: 'ResumendataRepetitividadGrid',
                        id: 'ResumendataRepetitividadGrid'
                    },{
                        title: 'Habitaciones Fuera de Orden',
                        xtype: 'ResumendataHfoGrid',
                        id: 'ResumendataHfoGrid'
                    },{
                        title: 'Comportamiento de las HFO',
                        xtype: 'ResumendataComportamHfoGrid',
                        id: 'ResumendataComportamHfoGrid'
                    },/*{
                        title: 'Principales Deficiencias Constructivas',
                        html: 'Tab8...',
                        id: 'CodirDeficienciasConstructivas'
                    },*/{
                        title: 'Comentario Final',
                        xtype: 'ResumendataFormComentFinal'
                }]
			})];
                
        this.callParent(arguments);
    }
});