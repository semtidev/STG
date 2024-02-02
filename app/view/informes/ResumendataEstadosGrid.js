Ext.define('SEMTI.view.informes.ResumendataEstadosGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.ResumendataEstadosGrid',
    id: 'ResumendataEstadosGrid',
    store: 'Resumenestados',
    defaults: {
        bodyStyle: 'padding:0'
    },
    autoScroll: true,
    columnLines: true,
    columns: [{
            header: "Indicador",
            flex: 1,
            dataIndex: 'indicador',
            lockable: false,
            menuDisabled: true,
            sortable: false
        }, {
            header: "En Proceso",
            width: 100,
            align: 'center',
            dataIndex: 'enproceso',
            lockable: false,
            menuDisabled: true,
            sortable: false
        }, {
            header: "Firmadas",
            width: 100,
            align: 'center',
            dataIndex: 'firmadas',
            lockable: false,
            menuDisabled: true,
            sortable: false
        }, {
            header: "Por Resolver",
            width: 120,
            align: 'center',
            dataIndex: 'poresolver',
            lockable: false,
            menuDisabled: true,
            sortable: false
        }, {
            header: "Reclamadas",
            width: 100,
            align: 'center',
            dataIndex: 'reclamadas',
            lockable: false,
            menuDisabled: true,
            sortable: false
        }, {
            header: "No Proceden",
            width: 110,
            align: 'center',
            dataIndex: 'noproceden',
            lockable: false,
            menuDisabled: true,
            sortable: false
        }, {
            header: "Total",
            width: 85,
            align: 'center',
            dataIndex: 'total',
            lockable: false,
            menuDisabled: true,
            sortable: false
        }],
    
    //viewConfig: {stripeRows: true},
    initComponent: function() {
        
        this.callParent(arguments);
        //this.getStore().load();
    }
});