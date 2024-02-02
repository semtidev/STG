Ext.define('SEMTI.view.graficas.GtiaSeguimTipoDefectoReportesSD', {
    extend: 'Ext.Panel',
    id: 'GtiaSeguimTipoDefectoReportesSD',
    alias: 'widget.GtiaSeguimTipoDefectoReportesSD',
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
            }, 8000);
        }
    },
    //viewConfig: {loadMask:true},
    items: [{
            xtype: 'box',
            id: 'iframeSDDefectosChart',
            minHeight: 580,
            padding: 0,
            renderTo: Ext.getBody(),
            autoEl: {
                tag: 'iframe',
                src: 'http://'+localStorage.getItem('ipserver')+'/semti.garantia/php/graficas/sd_defectos_reportes.php'
            }
        }],
    
    // override initComponent
    initComponent: function() {

        var comboProyects = Ext.create('SEMTI.view.proyectos.ProyectsComboAll', {
                editable: true,
                id: 'viewSDDefectosProyects',
                width: 170,
                margin: '5 10 0 5',
                allowBlank: true,
                name: 'proyecto',
                emptyText: 'Todos los Proyectos'
            });
        
        var comboEstado = Ext.create('SEMTI.view.garantia.GtiasdEstado', {
                allowBlank: true,
                id: 'viewSDDefectosEstado',
                editable: true,
                width: 160,
                name: 'estado',
                emptyText: 'Todos los Estados',
                margin: '5 10 0 0'
            });
                
        var comboTipo = Ext.create('SEMTI.view.garantia.GtiasdComunHabit', {
                allowBlank: true,
                id: 'viewSDDefectosTipo',
                editable: true,
                width: 170,
                name: 'tipo',
                emptyText: 'Todas las SD',
                margin: '5 20 0 0'
            });
        
        comboProyects.getStore().load();

        this.dockedItems = [{
                xtype: 'toolbar',
                cls: 'toolbar',
                ui: 'footer',
                height: 40,
                items: [comboProyects, comboEstado, comboTipo,'->',{
                        xtype: 'datefield',
                        id: 'ChartDefectosStart',
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
                        id: 'ChartDefectosEnd',
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
