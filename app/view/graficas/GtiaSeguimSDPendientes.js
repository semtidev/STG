Ext.define('SEMTI.view.graficas.GtiaSeguimSDPendientes', {
    extend: 'Ext.Panel',
    id: 'GtiaSeguimSDPendientes',
    alias: 'widget.GtiaSeguimSDPendientes',
    frame: false,
    iconCls: 'icon_reporte',
    layout: {
        type: 'vbox',    // Arrange child items vertically
        align: 'stretch'    // Each takes up full width
    },
    listeners: {
        'afterrender': function(view) {
            var me = this;
            //var myMask = new Ext.LoadMask(me, {msg:"Cargando..."});
            //myMask.show();
            me.setLoading('Cargando...');
            setTimeout(function(){ me.setLoading(false); },2000);
        }
    },
    //viewConfig: {loadMask:true},
    items: [{
            xtype: 'gtiagridsdpendientes',
            flex: 1,
            minHeight: 100
        }, {
            xtype: 'splitter', // A splitter between the two child items
            cls: 'splitter-active-background-color',
            size: 3
        }, {
            xtype: 'box',
            id: 'iframeSDPendChart',
            minHeight: 372,
            padding: 0,
            renderTo: Ext.getBody(),
            autoEl: {
                tag: 'iframe',
                src: 'http://'+localStorage.getItem('ipserver')+'/semti.garantia/php/graficas/sd_estados.php'
            }
        }],
    // override initComponent
    initComponent: function() {

        this.callParent();
    }
});
