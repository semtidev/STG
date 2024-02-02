Ext.define('SEMTI.view.garantia.GtiaFormObjectGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.GtiaFormObjectGrid',
    requires: [
        'Ext.grid.plugin.CellEditing',
        'Ext.grid.column.Action',
        'SEMTI.view.garantia.GtiasdEstado'
    ],
    store: 'Gtiagridtemp',
    viewConfig: {
        /*getRowClass: function(record, rowIndex, rowParams, store){
            var due = record.get('due');
            if(record.get('done')) {
                return 'tasks-completed-task';
            } else if(due && (due < Ext.Date.clearTime(new Date()))) {
                return 'tasks-overdue-task';
            }
        },*/
	columnLines: true
    },
	
    hideHeaders: true,
    //border: true,

    /*dockedItems: [
        {
            xtype: 'gtiaproblemasform',
            dock: 'top',
            // the grid's column headers are a docked item with a weight of 100.
            // giving this a weight of 101 causes it to be docked under the column headers
            weight: 101,
            bodyStyle: {
                'background-color': '#E4E5E7'
            }
        }
    ],*/

    initComponent: function() {
        var me = this,
            cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing', { pluginId:'sdFormGridEditing', clicksToEdit: 2 });

        me.plugins = [cellEditingPlugin];
        //me.getStore().load();
        me.columns = {
            defaults: {
                resizable: false,
                hideable: false
            },
            items: [
                {
                    dataIndex: 'ruta',
                    flex: 1
                    
                },{
                    dataIndex: 'ubicacion',
                    width: 260,
                    editor: {
                        xtype: 'textfield',
                        selectOnFocus: true
                    }
                },{
                    dataIndex: 'estado',
                    width: 150,
                    editor: {
                        xtype: 'gtiasdestado',
                        selectOnFocus: true
                    }
                },{
                    xtype: 'actioncolumn',
                    cls: 'tasks-icon-column-header tasks-edit-column-header',
                    width: 28,
                    icon: './resources/images/icons/edit2.png',
                    //iconCls: 'x-hidden',
					align: 'center',
                    tooltip: 'Modificar',
                    menuDisabled: true,
                    sortable: false,
                    handler: Ext.bind(me.handleEditClick, me)
                },{
                    xtype: 'actioncolumn',
                    cls: 'tasks-icon-column-header tasks-delete-column-header',
                    width: 28,
                    icon: './resources/images/icons/Delete.png',
					//iconCls: 'x-hidden',
					align: 'center',
                    tooltip: 'Eliminar',
                    menuDisabled: true,
                    sortable: false,
                    handler: Ext.bind(me.handleDeleteClick, me)
                }
            ]
        };

        me.callParent(arguments);

        me.addEvents(
            /**
             * @event editclick
             * Fires when an edit icon is clicked
             * @param {Ext.grid.View} view
             * @param {Number} rowIndex
             * @param {Number} colIndex
             * @param {Ext.grid.column.Action} column
             * @param {EventObject} e
             */
            'editclick',

            /**
             * @event deleteclick
             * Fires when a delete icon is clicked
             * @param {Ext.grid.View} view
             * @param {Number} rowIndex
             * @param {Number} colIndex
             * @param {Ext.grid.column.Action} column
             * @param {EventObject} e
             */
            'deleteclick',

            /**
             * @event edit
             * Fires when a record is edited using the CellEditing plugin or the statuscolumn
             * @param {SimpleTasks.model.Task} task     The task record that was edited
             */
            'recordedit',
            
            /**
             * @event reminderselect
             * Fires when a reminder time is selected from the reminder column's dropdown menu
             * @param {SimpleTasks.model.Task} task    the underlying record of the row that was clicked to show the reminder menu
             * @param {String|Number} value      The value that was selected
             */
            'reminderselect'
        );

        cellEditingPlugin.on('edit', me.handleCellEdit, this);

    },

    /**
     * Handles a click on the edit icon
     * @private
     * @param {Ext.grid.View} gridView
     * @param {Number} rowIndex
     * @param {Number} colIndex
     * @param {Ext.grid.column.Action} column
     * @param {EventObject} e
     */
    handleEditClick: function(gridView, rowIndex, colIndex, column, e) {
        // Fire a "deleteclick" event with all the same args as this handler
        this.fireEvent('editclick', gridView, rowIndex, colIndex, column, e);
    },

    /**
     * Handles a click on a delete icon
     * @private
     * @param {Ext.grid.View} gridView
     * @param {Number} rowIndex
     * @param {Number} colIndex
     * @param {Ext.grid.column.Action} column
     * @param {EventObject} e
     */
    handleDeleteClick: function(gridView, rowIndex, colIndex, column, e) {
        // Fire a "deleteclick" event with all the same args as this handler
        this.fireEvent('deleteclick', gridView, rowIndex, colIndex, column, e);
    },

    /**
     * Handles the CellEditing plugin's "edit" event
     * @private
     * @param {Ext.grid.plugin.CellEditing} editor
     * @param {Object} e                                an edit event object
     */
    handleCellEdit: function(editor, e) {
        var record = {
            oldvalue: e.originalValue,
            newvalue: e.value,
            id_row: e.record.get('id'),
            ubicacion: e.record.get('ubicacion'),
            estado: e.record.get('estado')
        }
        this.fireEvent('recordedit', record);
    }

});