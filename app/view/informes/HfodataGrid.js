Ext.define('SEMTI.view.informes.HfodataGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.hfodatagrid',
    requires: ['Ext.grid.plugin.CellEditing'],
    defaults: {
        bodyStyle: 'padding:0'
    },
    store: 'Hfodata',
    autoScroll: true,
    //cls: 'x-grid3-row',
    //selType: 'checkboxmodel',
    //title: 'Detalles del Informe',
    columnLines: true,
    
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
                    text: "SD",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    flex: 1,
                    minWidth: 100,
                    locked: false,
                    dataIndex: 'sd',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "No. Habitaciones",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    flex: 3,
                    dataIndex: 'habitaciones',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Ctdad HFO",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 90,
                    dataIndex: 'ctdad_habit',
                    align: 'center'
                }, {
                    text: "Pendientes",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 100,
                    dataIndex: 'pendientes',
                    align: 'center'
                }, {
                    text: "Causa",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    flex: 2,
                    dataIndex: 'problema',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Observaciones",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    flex: 3,
                    dataIndex: 'observaciones',
                    align: 'left',
                    editor: {
                        xtype: 'textfield',
                        selectOnFocus: true
                    },
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Fecha Actualizado",
                    lockable: true,
                    width: 135,
                    dataIndex: 'fechamod',
                    align: 'center',
                    xtype: 'datecolumn', // the column type
                    format: 'd/m/Y H:i'
            }]
        };

        me.callParent(arguments);

        me.addEvents(
            
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
     * Handles the CellEditing plugin's "edit" event
     * @private
     * @param {Ext.grid.plugin.CellEditing} editor
     * @param {Object} e                                an edit event object
     */
    handleCellEdit: function(editor, e) {
        this.fireEvent('recordedit', e.record);
    }
    
});