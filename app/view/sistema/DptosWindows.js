Ext.define('SEMTI.view.sistema.DptosWindows', {
    extend: 'Ext.window.Window',
    xtype: 'DptosWindows',
    requires: ['SEMTI.view.sistema.DptosGrid'],
    layout: 'fit',
    autoShow: true,
    width: 500,
    height: 500,
    resizable: false,
    modal: true,
    maximizable: true,
    tools: [{
        type: 'refresh',
        tooltip: 'Actualizar',
        callback: function() {
            Ext.getCmp('DptosGrid').getStore().load();
        }
    }, {
        type: 'print',
        tooltip: 'Imprimir como PDF',
        callback: function(panel) {                
            window.open('http://'+localStorage.getItem('ipserver')+'/semti.garantia/php/sistema/pdf_Dptos.php', '_blank');
        }
    }],
    iconCls: 'icon_dptos',
    title: 'Departamentos',
    initComponent: function() {
        this.items = [{
                xtype: 'DptosGrid'
            }];

        this.callParent(arguments);
    }
});