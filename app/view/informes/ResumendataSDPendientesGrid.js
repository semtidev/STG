Ext.define('SEMTI.view.informes.ResumendataSDPendientesGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.ResumendataSDPendientesGrid',
    requires: ['Ext.grid.plugin.CellEditing','Ext.grid.feature.Grouping'],
    defaults: {
        bodyStyle: 'padding:0'
    },
    store: 'Resumensdpendientes',
    autoScroll: true,
    columnLines: true,
    features: [{
        ftype: 'grouping',
        groupHeaderTpl: '{name}',
        hideGroupedHeader: true,
        enableGroupingMenu: false
    }],
    initComponent: function() {
        var me = this,
            cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing', { pluginId:'CodirSdpendientesGridEditing', clicksToEdit: 2 });

        me.plugins = [cellEditingPlugin];
        me.columns = {
            defaults: {
                resizable: false,
                hideable: false
            },
            items: [
                {
                    text: "Descripci\xF3n",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    flex: 1,
                    minWidth: 400,
                    locked: false,
                    dataIndex: 'descripcion',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Zona(s)",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 80,
                    dataIndex: 'zonas',
                    align: 'center',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Objeto(s)",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 200,
                    dataIndex: 'objetos',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Local(es)",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 200,
                    dataIndex: 'locales',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Departamento(s)",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 170,
                    dataIndex: 'dpto',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="' + val + '"';
                        return val;
                    }
                }, {
                    text: "Comentario",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 300,
                    dataIndex: 'comentario',
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