Ext.define('SEMTI.view.informes.ResumendataPIndicadoresGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.ResumendataPIndicadoresGrid',
    requires: ['Ext.grid.plugin.CellEditing'],
    defaults: {
        bodyStyle: 'padding:0'
    },
    store: 'Resumenpindicadores',
    autoScroll: true,
    columnLines: true,
    
    initComponent: function() {
        var me = this,
            cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing', { pluginId:'codirHfoEditing', clicksToEdit: 2 });

        me.plugins = [cellEditingPlugin];
        me.columns = {
            defaults: {
                resizable: false,
                hideable: false
            },
            items: [
                {
                    text: "Indicador",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    flex: 3,
                    minWidth: 350,
                    locked: false,
                    dataIndex: 'indicador',
                    align: 'left',
                    renderer: function(val, metaData) {
                        metaData['tdAttr'] = 'data-qtip="'+val+'"';
                        return val;
                    }
                }, {
                    text: "Periodo Anterior",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 125,
                    dataIndex: 'periodo_ant',
                    align: 'center'
                }, {
                    text: "Periodo Actual",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 120,
                    dataIndex: 'periodo_act',
                    align: 'center'
                }, {
                    text: "Acumulado",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 100,
                    dataIndex: 'acumulado',
                    align: 'center'
                }, {
                    text: "Meta",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 100,
                    dataIndex: 'meta',
                    align: 'center'
                }, {
                    text: "Estado",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 60,
                    dataIndex: 'estado',
                    align: 'center',
                    renderer: function(val, metaData, record) {
                        if (record.get('estado').length > 0 && record.get('estado') == 'Bien') {
                            return '<img src="/semti.garantia/resources/images/icons/HansUp.png" style="margin:0;">';
                        }else if (record.get('estado').length > 0 && record.get('estado') == 'Mal'){
                            return '<img src="/semti.garantia/resources/images/icons/HansDown.png" style="margin:0;">';
                        }
                        else{ return ''; }
                    }
                }, {
                    text: "Tendencia",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 85,
                    dataIndex: 'tendencia',
                    align: 'center',
                    renderer: function(val, metaData, record) {
                        if (record.get('estado') == 'Bien') {
                            if (val == 'asc') {
                                return '<img src="/semti.garantia/resources/images/icons/green_asc.png" style="margin:0;">';
                            }else if (val == 'desc') {
                                return '<img src="/semti.garantia/resources/images/icons/green_desc.png" style="margin:0;">';
                            }else if (val == 'const') {
                                return '<img src="/semti.garantia/resources/images/icons/green_const.png" style="margin:0;">';
                            }
                        }
                        else if (record.get('estado') == 'Mal'){
                            if (val == 'asc') {
                                return '<img src="/semti.garantia/resources/images/icons/red_asc.png" style="margin:0;">';
                            }else if (val == 'desc') {
                                return '<img src="/semti.garantia/resources/images/icons/red_desc.png" style="margin:0;">';
                            }else if (val == 'const') {
                                return '<img src="/semti.garantia/resources/images/icons/red_const.png" style="margin:0;">';
                            }
                        }
                        else{ return ''; }
                    }
                }, {
                    text: "Acciones",
                    lockable: true,
                    menuDisabled: true,
                    sortable: false,
                    width: 250,
                    dataIndex: 'acciones',
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