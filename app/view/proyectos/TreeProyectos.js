Ext.define('SEMTI.view.proyectos.TreeProyectos' ,{
    extend: 'Ext.tree.Panel',
    xtype: 'treeProyectos',
    id: 'treeProyectos',
    requires: [
        'Ext.grid.plugin.CellEditing',
        'Ext.grid.column.Action',
	    'SEMTI.view.proyectos.ProyectGrid'
    ],
    rootVisible: false,

    listeners: {
            'selectionchange': function(view, records) {
                this.down('#edit').setDisabled(!records.length);//Se Habilita el Boton Editar
				this.down('#delete').setDisabled(!records.length);//Se Habilita el Boton Delete
            }
    },
	
    store: 'Treeproyectos',
    hideHeaders: true,
    dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            cls: 'topbarTree',
            items: [{
                iconCls: 'icon-add',
                tooltip: 'Nuevo Proyecto',
                cls: 'topbarTree_button',
                action: 'nuevo',
                hidden: (localStorage.getItem('perfiles') != '10' && localStorage.getItem('perfiles') != '17') ? false : true
            },{
                iconCls: 'proyects-rename',
                id: 'edit',
                tooltip: 'Modificar',
                cls: 'topbarTree_button',
                disabled: true,
                hidden: (localStorage.getItem('perfiles') != '10' && localStorage.getItem('perfiles') != '17') ? false : true
            },{
                iconCls: 'proyects-delete',
                id: 'delete',
                tooltip: 'Eliminar',
                cls: 'topbarTree_button',
                disabled: true,
                hidden: (localStorage.getItem('perfiles') != '10' && localStorage.getItem('perfiles') != '17') ? false : true
            },{
                iconCls: 'proyects-down-tree',
                tooltip: 'Expandir todos los Proyectos',
                cls: 'topbarTree_button'
            },{
                iconCls: 'proyects-up-tree',
                tooltip: 'Contraer todos los Proyectos',
                cls: 'topbarTree_button'
            },{
                iconCls: 'proyects-reload',
                tooltip: 'Actualizar Arbol',
                cls: 'topbarTree_button'
            }]
    }],
	
    initComponent: function() {
        var me = this,
            cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing', { 
                pluginId:'elementNameEditing',
                clicksToEdit: 2,
            });
        
        //plugins : [Ext.create('Ext.grid.plugin.CellEditing', { pluginId:'elementNameEditing', clicksToEdit: 2 })],
        if (localStorage.getItem('perfiles') != '10' && localStorage.getItem('perfiles') != '17') {
            me.plugins = [cellEditingPlugin];
        }

        me.columns = [{
            xtype: 'treecolumn',
            dataIndex: 'text',
            flex: 1,
            editor: {
                xtype: 'textfield',
                selectOnFocus: true,
                allowOnlyWhitespace: false
            }/*,
            renderer: Ext.bind(me.renderName, me)*/
        },{
            xtype: 'actioncolumn',
            width: 25,
            icon: './resources/images/icons/Delete.png',
            iconCls: 'x-hidden',
            tooltip: 'Eliminar',
            handler: Ext.bind(me.handleDeleteClick, me)
        }];
        
        me.callParent(arguments);
        //me.getStore().load();

        me.addEvents('deleteclick', 'taskdrop', 'listdrop', 'recordedit');

        //me.on('edit', me.handleAfterEdit, me);
        me.on('edit', me.handleCellEdit, me);
        me.relayEvents(me.getView(), ['taskdrop', 'listdrop']);
    },

    handleDeleteClick: function(gridView, rowIndex, colIndex, column, e) {
        // Fire a "deleteclick" event with all the same args as this handler
        this.fireEvent('deleteclick', gridView, rowIndex, colIndex, column, e);
    },

    handleAfterEdit: function(editingPlugin, e) {
        var new_value  = e.record.get('text'),
            id_array   = e.record.get('id').split('.'),
            nivel      = id_array[0],
            ruta       = e.record.get('ruta'),
            ruta_array = ruta.split(',');		

        if(nivel === 1){

            Ext.getCmp('FileGridTitleProyect').setText('Proyecto ' + new_value);
        }
        else if(nivel === 2){

            Ext.getCmp('FileGridTitleProyect').setText(ruta_array[0] + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + new_value);
        }
        else if(nivel === 3){

            Ext.getCmp('FileGridTitleProyect').setText(ruta_array[0] + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + ruta_array[1] + '\xA0\xA0\xA0\xBB\xA0\xA0\xA0' + new_value);
        }

        var grid = Ext.create('SEMTI.view.proyectos.ProyectGrid');
        grid.store.load();
    },
/*
    renderName: function(value, metaData, record, rowIndex, colIndex, store, view) {
        
		var count  = record.get('total'),
			arr_id = record.get('id').split('.');	
        
		if((arr_id.length == 2 && arr_id[0] == 1) || (arr_id.length == 3 && arr_id[0] == 4)) {
			
			return value;
		}
		else{
			return value + ' (' + count + ')';
		}
    },*/
    
    handleCellEdit: function(editor, e) {
        this.fireEvent('recordedit', e.record);
    },
    
    refreshView: function() {
        // refresh the data in the view.  This will trigger the column renderers to run, making sure the task counts are up to date.
        this.getView().refresh();
    }
})
