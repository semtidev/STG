Ext.define('SEMTI.view.informes.ResumendataEstadosPanel', {
    extend: 'Ext.Panel',
    id: 'ResumendataEstadosPanel',
    alias: 'widget.ResumendataEstadosPanel',
    layout: {
        type: 'vbox',    // Arrange child items vertically
        align: 'stretch'    // Each takes up full width
    },    
    items: [{
            xtype: 'ResumendataEstadosGrid',
            flex: 1,
            minHeight: 56,
            maxHeight: 56
        }, {
            xtype: 'splitter', // A splitter between the two child items
            style: 'background-color: #949699',
            size: 2
        },{
            xtype: 'box',
            id: 'boxResumenChartEstados',
            minHeight: 350,
            padding: 0,
            renderTo: Ext.getBody(),
            autoEl: {
                tag: 'iframe',
                src: ''
            }
        }],
    // override initComponent
    initComponent: function() {

        this.callParent();
    }    
    
});