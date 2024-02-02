Ext.define('SEMTI.view.sistema.MainAccordion' ,{
    extend: 'Ext.panel.Panel',
	alias: 'widget.mainaccordion',
    listeners: {
        'beforerender': function() {            
            Ext.getCmp('mainAccordionProyects').setVisible(true);
        }
    },
    layout: {
        // layout-specific configs go here
        type: 'accordion',
        titleCollapse: true,
        animate: true,
        activeOnTop: false
    },
    items: [
         
        {
            id: 'accord-syst',
            title: '<i class="fas fa-puzzle-piece"></i> &nbsp;Sistema',
            xtype:'treesistemas',
            margin: 0,
            scroll: true,        
            autoScroll: true
        }, 
        /*{
            id: 'accord-chart',
            title: '<i class="fas fa-chart-bar"></i> &nbsp;Seguimiento a Indicadores',
            xtype:'treeseguimiento',
            margin: 0,
            scroll: true,        
            autoScroll: true
        },*/
        {
            id: 'mainAccordionProyects',
            title: '<i class="fas fa-sitemap"></i> &nbsp;Estructuras de Proyectos',
            hidden: true,
            xtype:'treeProyectos',
            margin: 0,
            scroll: true,
            autoScroll: true
        }
    ]
});