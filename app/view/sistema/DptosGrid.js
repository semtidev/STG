Ext.define('SEMTI.view.sistema.DptosGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'DptosGrid',
    id: 'DptosGrid',
    requires: [
        'Ext.grid.plugin.CellEditing',
        'Ext.grid.column.Action',
        'SEMTI.view.sistema.DptosForm'
    ],
    store: Ext.create('SEMTI.store.Systdptos'),
    viewConfig: {
        getRowClass: function(record, rowIndex, rowParams, store) {
            var due = record.get('due');
            if (record.get('done')) {
                return 'tasks-completed-task';
            } else if (due && (due < Ext.Date.clearTime(new Date()))) {
                return 'tasks-overdue-task';
            }
        },
        columnLines: false
    },
    dockedItems: [
        {
            xtype: 'DptosForm',
            dock: 'top',
            // the grid's column headers are a docked item with a weight of 100.
            // giving this a weight of 101 causes it to be docked under the column headers
            weight: 101,
            bodyStyle: {
                'background-color': '#E4E5E7'
            }
        }
    ],
    initComponent: function() {
        var me = this,
                cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing', {pluginId: 'elementGridNameEditing', clicksToEdit: 2});

        me.plugins = [cellEditingPlugin];
        me.getStore().load();
        me.columns = {
            defaults: {
                //draggable: false,
                resizable: false,
                hideable: false
            },
            items: [
                {
                    xtype: 'rownumberer',
                    text: 'No',
                    width: 35,
                    align: 'center',
                }, {
                    text: 'Nombre',
                    id: 'FileGridTitleName',
                    dataIndex: 'nombre',
                    flex: 1,
                    emptyCellText: '',
                    align: 'left',
                    editor: {
                        xtype: 'textfield',
                        selectOnFocus: true
                    }
                }, {
                    xtype: 'actioncolumn',
                    cls: 'tasks-icon-column-header tasks-edit-column-header',
                    width: 28,
                    icon: './resources/images/icons/edit2.png',
                    iconCls: 'x-hidden',
                    align: 'center',
                    tooltip: 'Renombrar',
                    menuDisabled: true,
                    sortable: false,
                    handler: Ext.bind(me.handleEditClick, me)
                }, {
                    xtype: 'actioncolumn',
                    cls: 'tasks-icon-column-header tasks-delete-column-header',
                    width: 28,
                    icon: './resources/images/icons/Delete.png',
                    iconCls: 'x-hidden',
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
     * Handles a "checkchange" event on the "done" column
     * @private
     * @param {SimpleTasks.ux.StatusColumn} column
     * @param {Number} rowIndex
     * @param {Boolean} checked
     */
    handleCheckChange: function(column, rowIndex, checked) {
        this.fireEvent('recordedit', this.store.getAt(rowIndex));
    },
    /**
     * Handles a "select" event on the reminder column
     * @private
     * @param {SimpleTasks.model.Task} task    the underlying record of the row that was clicked to show the reminder menu
     * @param {String|Number} value      The value that was selected
     */
    handleReminderSelect: function(task, value) {
        this.fireEvent('reminderselect', task, value);
    },
    /**
     * Handles the CellEditing plugin's "edit" event
     * @private
     * @param {Ext.grid.plugin.CellEditing} editor
     * @param {Object} e                                an edit event object
     */
    handleCellEdit: function(editor, e) {
        this.fireEvent('recordedit', e.record);
    },
    /**
     * Reapplies the store's current filters. This is needed because when data in the store is modified
     * after filters have been applied, the filters do not automatically get applied to the new data.
     */
    refreshFilters: function() {
        var store = this.store,
                filters = store.filters;

        // save a reference to the existing task filters before clearing them
        filters = filters.getRange(0, filters.getCount() - 1);

        // clear the tasks store's filters and reapply them.
        store.clearFilter();
        store.filter(filters);
    },
    /**
     * Renderer for the list column
     * @private
     * @param {Number} value
     * @param {Object} metaData
     * @param {SimpleTasks.model.Task} task
     * @param {Number} rowIndex
     * @param {Number} colIndex
     * @param {SimpleTasks.store.Tasks} store
     * @param {Ext.grid.View} view
     */
    renderList: function(value, metaData, task, rowIndex, colIndex, store, view) {
        var listsStore = Ext.getStore('Lists'),
                node = value ? listsStore.getNodeById(value) : listsStore.getRootNode();

        return node.get('name');
    }

});