Ext.define('SEMTI.view.garantia.GridProyectos' ,{
    extend: 'Ext.grid.Panel',  
    alias: 'widget.gridproyectos',
 
	listeners: {
            'selectionchange': function(view, records) {
                this.down('#edit').setDisabled(!records.length);//Se Habilita el Boton Editar
				this.down('#delete').setDisabled(!records.length);//Se Habilita el Boton Delete
            }
    },
 
	//title: 'Proyectos',
	store: 'Proyectos',
    columnLines: true,
	//hideHeaders: true,
	columns: [{
        header: "Proyectos",
        menuDisabled: true,
        flex:1,
        dataIndex: 'nombre',
    }],
	
	viewConfig: { stripeRows: true },
 
    initComponent: function() {
    	this.dockedItems = [{
            xtype: 'toolbar',
			cls: 'toolbar',
			dock: 'bottom',
			items: [{
                iconCls: 'icon-add',
				cls: 'toolbar_button',
                text: 'Agregar',
				tooltip: 'Agregar Usuario',
                action: 'agregar'
            },{
                itemId: 'edit',
				iconCls: 'icon-edit',
				cls: 'toolbar_button',
                text: 'Editar',
				disabled: true,
				tooltip: 'Modificar Usuario',
                action: 'editar'
            },{
                itemId: 'delete',
				iconCls: 'icon-delete',
				cls: 'toolbar_button',
                text: 'Eliminar',
				disabled: true,
				tooltip: 'Eliminar Usuario',
                action: 'eliminar'
            }]
        }];
     
        this.callParent(arguments);
    }
});