Ext.define('SEMTI.view.informes.HfoWindow', {
    extend: 'Ext.window.Window',
    alias : 'widget.hfowindow',
  	
    //layout: 'fit',
    autoShow: true,
    width: 1100,
    height: 600,
    resizable: false,
    modal: true,
    maximizable: true,
    title: 'Informes Habitaciones Fuera de Orden',
    layout: {
        type: 'vbox',    // Arrange child items vertically
        align: 'stretch'    // Each takes up full width
    },
     
    iconCls: 'icon_resumen',
 
    initComponent: function() {
        this.items = [{
                xtype: 'hfogrid',                
                height: 200,
                minHeight: 150,
                maxHeight: 200
            }, {
                xtype: 'splitter', // A splitter between the two child items
                cls: 'splitter-active-background-color',
                size: 3
            }, {
                xtype: "hfodatagrid",
                flex: 1,
                minHeight: 350,
                autoScroll: true
            }];
                        
        this.callParent(arguments);
    }
});