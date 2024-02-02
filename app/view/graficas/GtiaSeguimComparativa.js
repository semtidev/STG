Ext.define('SEMTI.view.graficas.GtiaSeguimComparativa', {
    extend: 'Ext.Panel',
    id: 'GtiaSeguimComparativa',
    alias: 'widget.GtiaSeguimComparativa',
    /*requires: [
        'Ext.window.MessageBox',
        'Ext.tip.*'
    ],*/
    frame: false,
    iconCls: 'icon_reporte',
    layout: {
        type: 'vbox', // Arrange child items vertically
        align: 'stretch'    // Each takes up full width
    },
    listeners: {
        'afterrender': function(view) {
            var me = this;
            me.setLoading('Cargando...');
            
            setTimeout(function() {
                me.setLoading(false);
            }, 2000);
            
            Ext.example.msg('Aviso', 'Estimando Usuario, debe seleccionar los Proyectos que ser\xE1n comparados en la gr\xE1fica para poder visualizar los resultados.');           
        }
    },
    //viewConfig: {loadMask:true},
    items: [{
            xtype: 'box',
            id: 'iframeSDComparativaChart',
            minHeight: 580,
            padding: 0,
            renderTo: Ext.getBody(),
            autoEl: {
                tag: 'iframe',
                src: 'http://'+localStorage.getItem('ipserver')+'/semti.garantia/php/graficas/sd_comparativa.php'
            }
        }],
    
    // override initComponent
    initComponent: function() {

        var comboProyects = Ext.create('SEMTI.view.proyectos.ProyectsComboAll', {
                editable: true,
                id: 'viewSDComparativaProyects',
                width: 270,
                margin: '5 10 0 5',
                allowBlank: true,
                multiSelect: true,
                name: 'proyecto',
                emptyText: 'Elija los proyectos que desea comparar'
            });
        
        var comboEstado = Ext.create('SEMTI.view.garantia.GtiasdEstado', {
                allowBlank: true,
                id: 'viewSDComparativaEstado',
                editable: true,
                width: 160,
                name: 'estado',
                emptyText: 'Todos los Estados',
                margin: '5 10 0 0',
                disabled: true
            });
                
        var comboTipo = Ext.create('SEMTI.view.garantia.GtiasdComunHabit', {
                allowBlank: true,
                id: 'viewSDComparativaTipo',
                editable: true,
                width: 170,
                name: 'tipo',
                emptyText: 'Todas las SD',
                margin: '5 20 0 0',
                disabled:true
            });
        
        comboProyects.getStore().load();

        this.dockedItems = [{
                xtype: 'toolbar',
                cls: 'toolbar',
                ui: 'footer',
                height: 40,
                items: [comboProyects, comboEstado, comboTipo]
            }];

        this.callParent();
    }
});
