// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.define('Ext.calendar.view.EventosGrid' ,{
    extend: 'Ext.tree.Panel',
	alias: 'widget.eventosgrid',
	id: 'eventosgrid',
	rootVisible: false,
	border:false,
	//collapsible: true,
    //useArrows: true,
    store: 'Eventos',
	multiSelect: true,
    //singleExpand: true,
	//selType: 'checkboxmodel',
 	features: [{ftype: 'filters', encode: true, local: true, filters: [{type: 'boolean', dataIndex: 'visible'}]}],
    //columnLines: true,
	/*features: [{
		id: 'group',
		ftype: 'groupingsummary',
		groupHeaderTpl: 'T\xEDtulo: {name}',
		hideGroupedHeader: false,
		enableGroupingMenu: false,
		startCollapsed: false,
		enableNoGroups: false
	}],*/
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
		xtype: 'treecolumn', //this is so we know which column will show the tree
		text: 'T\xEDtulo',
		menuDisabled: true,
		width: 300,
		sortable: true,
		dataIndex: 'titulo',
		locked: true,
		filter: {
			type: 'string'
		}
	},{
        text: "Inicia",
		xtype: 'datecolumn',
		//menuDisabled: true,
		sortable: true,
        width: 220,
		sortable: true,
        align: 'left',
        dataIndex: 'start',
		hideable: false,
		summaryType: 'min',
		renderer: Ext.util.Format.dateRenderer('l d/m/Y h:i a'),
		summaryRenderer: Ext.util.Format.dateRenderer('l d/m/Y h:i a'),
    },{
        text: "Termina",
		xtype: 'datecolumn', 
		//menuDisabled: true,
		sortable: true,
        width: 220,
        align: 'left',
        dataIndex: 'end',
		summaryType: 'max',
		renderer: Ext.util.Format.dateRenderer('l d/m/Y h:i a'),
		summaryRenderer: Ext.util.Format.dateRenderer('l d/m/Y h:i a'),
    },{
        text: "Todo el D\xEDa",
		menuDisabled: true,
		sortable: false,
        width: 130,
        align: 'center',
        dataIndex: 'allday',
		renderer: function(value, metaData, record, colIndex, store){
			if(value == 'Si')
			return '<img src="/semtiAGENDA/resources/images/icons/16x16-free-application-icons/png/16x16/Yes.png">';
			else
			return '<img src="/semtiAGENDA/resources/images/icons/16x16-free-application-icons/png/16x16/No.png">';
		}
    },{
        text: "Calendario",
		//menuDisabled: true,
		sortable: true,
        flex: 1,
		minWidth: 250,
        align: 'left',
        dataIndex: 'calendar',
		renderer: function(value, metaData, record, colIndex, store){
			var color = record.get('calendar_color');
			return '<span style="color:'+color+';">'+value+'</span>';
		}
    },{
		menuDisabled: true,
		sortable: false,
		xtype: 'actioncolumn',
		text: 'Modificar',
		align: 'center',
		width: 90,
		locked: true,
		items: [{
			icon   : 'resources/images/icons/edit.png',  // Use a URL in the icon config
			tooltip: 'Modificar Evento',
			handler: function(grid, rowIndex, colIndex) {
				
				var rec = grid.store.getAt(rowIndex),
				    id  = rec.get('id');
				
				var editar = Ext.create('Ext.calendar.view.ContactosForm');
				editar.setTitle('Modificar Evento');
				editar.show();
				
				var form = editar.down('form');
				
				form.getForm().load({
					url: './php/agenda/ContactoFormLoad.php',
					method : 'POST',
					params: {
						id_contacto: id
					},
					failure: function(form, action) {
						editar.close();
						Ext.Msg.alert("Carga Fallida", "La carga de los parametros del Usuario no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
					}
				});
			}
		}]
	}],
	
	viewConfig: { stripeRows: true },
 
    initComponent: function() {
     
        this.callParent(arguments);
    }
});