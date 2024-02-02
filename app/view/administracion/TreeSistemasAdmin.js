// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('SEMTI.view.administracion.TreeSistemasAdmin' ,{
    extend: 'Ext.tree.Panel',
	xtype: 'treesistemasadmin',
	id: 'treesistemasadmin',
	requires: ['Ext.grid.*','Ext.data.*','Ext.util.*','Ext.state.*','Ext.form.*'],
	
	rootVisible: false,
	border:false,
	columnLines: true,
	store: 'Treesistemasadmin',
	//multiSelect: true,
	plugins: Ext.create('Ext.grid.plugin.CellEditing', {
        pluginId: 'cellplugin',
		clicksToMoveEditor: 1,
        autoCancel: false
    }),
	height: 400,
	
	getChecked: function( prop ){
        var prop = prop || null;
        var checked = [];

        this.getView().getStore().getRootNode().cascadeBy(function(node){
           if( node.data.checked ){
                if( prop && node.data[prop] ) checked.push(node.data[prop]);
                else checked.push(node);
           }
        });

        return checked;
    },
	
	columns: [{
		xtype: 'treecolumn', 
		text: 'Mapa de Navegaci\xF3n',
		menuDisabled: true,
		width: 400,
		sortable: true,
		dataIndex: 'text',
		locked: true,
		filter: {
			type: 'string'
		}
	},{
		xtype: 'checkcolumn',
		id: 'TreeSistemasAdminCheckRead',
		text: 'Leer',
		menuDisabled: true,
		sortable: false,
		dataIndex: 'lectura',
		width: 90,
		align: 'center',
		editor: {
			xtype: 'checkbox',
			cls: 'x-grid-checkheader-editor'
		}
	},{
		xtype: 'checkcolumn',
		id: 'TreeSistemasAdminCheckReadExp',
		header: 'Leer y Exportar',
		menuDisabled: true,
		sortable: false,
		dataIndex: 'lectura_exportar',
		flex: 1,
		align: 'center',
		editor: {
			xtype: 'checkbox',
			cls: 'x-grid-checkheader-editor'
		}
	},{
		xtype: 'checkcolumn',
		id: 'TreeSistemasAdminCheckWrite',
		header: 'Escribir',
		menuDisabled: true,
		sortable: false,
		dataIndex: 'escritura',
		width: 100,
		align: 'center',
		editor: {
			xtype: 'checkbox',
			cls: 'x-grid-checkheader-editor'
		}
	},{
		xtype: 'checkcolumn',
		id: 'TreeSistemasAdminCheckModify',
		header: 'Modificar',
		menuDisabled: true,
		sortable: false,
		dataIndex: 'modificar',
		width: 110,
		align: 'center',
		editor: {
			xtype: 'checkbox',
			cls: 'x-grid-checkheader-editor'
		}
	}],
	
	viewConfig: { stripeRows: true },
 
    initComponent: function() {
     
        this.callParent(arguments);
    }
})
