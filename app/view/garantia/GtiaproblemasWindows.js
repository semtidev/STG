Ext.define('SEMTI.view.garantia.GtiaproblemasWindows', {
    extend: 'Ext.window.Window',
    alias : 'widget.gtiaproblemaswindows',
  	
    requires: ['SEMTI.view.garantia.GtiaproblemasGrid'],

    layout: 'fit',
    autoShow: true,
    width: 550,
    height: 500,
    resizable: false,
    modal: true,
    maximizable: true,
    tools: [{
        type: 'refresh',
        tooltip: 'Actualizar',
        callback: function() {
            Ext.getCmp('gtiaproblemasgrid').getStore().load();
        }
    }, {
        type: 'print',
        tooltip: 'Imprimir como PDF',
        callback: function(panel) {                
            let ipserver = localStorage.getItem('ipserver');
            window.open('http://'+ipserver+'/semti.garantia/php/garantia/pdf_Problemas.php', '_blank');
        }
    }],
     
    iconCls: 'icon_dptos',
	title: 'Tipos de Problemas',
 
    initComponent: function() {
        this.items = [{
        	xtype: 'gtiaproblemasgrid'
		}];
         
        this.callParent(arguments);
    }
});