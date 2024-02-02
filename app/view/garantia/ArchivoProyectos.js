Ext.define('SEMTI.view.garantia.ArchivoProyectos', {
	extend: 'Ext.window.Window',
	alias: 'widget.archivoproyectos',

	title: 'Archivo de Proyectos',
	layout: {
        type: 'hbox',
        align: 'stretch'
    },
	modal: true,
	width: 900,
	height: 550,

	// override initComponent
	initComponent: function() {
		this.items = [{
			xtype: 'gridproyectos',
			flex: 1,
			border: 1
			//split: true
			//region: 'west'  //center north
		},{
			//xtype: 'contactosdetail',
			html: 'zonas...',
			flex: 1,
			border: 1
			
		},{
			//xtype: 'contactosdetail',
			html: 'objetos...',
			flex: 1,
			border: 1
			
		},{
			//xtype: 'contactosdetail',
			html: 'partes...',
			flex: 1,
			border: 1
			
		}];
		
		/*this.dockedItems = [{
            xtype: 'toolbar',
			cls: 'toolbar',
			height: 38,
			items: [{
                iconCls: 'icon-add',
				cls: 'toolbar_button',
                text: 'Agregar',
				tooltip: 'Agregar Contacto',
                action: 'agregar'
            },{
                itemId: 'delete',
				iconCls: 'icon-delete',
				cls: 'toolbar_button',
                text: 'Eliminar',
				disabled: true,
				tooltip: 'Eliminar Contacto(s)',
                action: 'eliminar'
            },'-',{
                itemId: 'print',
				iconCls: 'icon-exppdf',
				cls: 'toolbar_button',
                text: 'Imprimir',
				disabled: true,
				tooltip: 'Imprimir Contactos Seleccionados como Documento PDF',
                action: 'imprimir'
            }]
        }];*/
		
		// call the superclass's initComponent implementation
		this.callParent();
	}                                          
});