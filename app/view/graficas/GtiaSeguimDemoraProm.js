Ext.define('SEMTI.view.graficas.GtiaSeguimDemoraProm', {
    extend: 'Ext.Panel',
    id: 'GtiaSeguimDemoraProm',
    alias: 'widget.GtiaSeguimDemoraProm',
    frame: false,
    iconCls: 'icon_reporte',
    layout: {
        type: 'vbox', // Arrange child items vertically
        align: 'stretch'    // Each takes up full width
    },
    listeners: {
        'afterrender': function() {
            var me = this;
            me.setLoading('Cargando...');
            
            setTimeout(function() {
                me.setLoading(false);
            }, 2000);
        }
    },
    //viewConfig: {loadMask:true},
    items: [{
            xtype: 'box',
            id: 'iframeSDDemoraChart',
            minHeight: 580,
            padding: 0,
            renderTo: Ext.getBody(),
            autoEl: {
                tag: 'iframe',
                src: 'http://'+localStorage.getItem('ipserver')+'/semti.garantia/php/graficas/sd_demora.php'
            }
        }],
    
    // override initComponent
    initComponent: function() {

        var comboProyects = Ext.create('SEMTI.view.proyectos.ProyectsComboAll', {
                editable: true,
                id: 'viewSDDemoraProyects',
                width: 170,
                margin: '5 10 0 5',
                allowBlank: true,
                name: 'proyecto',
                emptyText: 'Todos los Proyectos'
            });
        
        var comboEstado = Ext.create('SEMTI.view.garantia.GtiasdEstado', {
                allowBlank: true,
                id: 'viewSDDemoraEstado',
                editable: true,
                width: 160,
                name: 'estado',
                emptyText: 'Todos los Estados',
                margin: '5 10 0 0'
            });
                
        var comboTipo = Ext.create('SEMTI.view.garantia.GtiasdComunHabit', {
                allowBlank: true,
                id: 'viewSDDemoraTipo',
                editable: true,
                width: 170,
                name: 'tipo',
                emptyText: 'Todas las SD',
                margin: '5 10 0 0'
            });
        
        comboProyects.getStore().load();

        this.dockedItems = [{
                xtype: 'toolbar',
                cls: 'toolbar',
                ui: 'footer',
                height: 40,
                items: [comboProyects, comboEstado, comboTipo,'->',{
                        xtype: 'datefield',
                        id: 'ChartDemSDStart',
                        fieldLabel: 'Desde',
                        labelWidth: 40,
                        editable: true,
                        width: 150,
                        allowBlank: true,
                        name: 'desde',
                        format: 'd/m/Y',
                        submitFormat: 'Y-m-d',
                        margin: '0 10 0 5'
                    },{
                        xtype: 'datefield',
                        id: 'ChartDemSDEnd',
                        fieldLabel: 'Hasta',
                        labelWidth: 37,
                        editable: true,
                        width: 150,
                        allowBlank: true,
                        name: 'hasta',
                        format: 'd/m/Y',
                        submitFormat: 'Y-m-d',
                        margin: '0 10 0 0'
                }]
            }];

        this.callParent();
    }
});
