Ext.define('SEMTI.view.proyectos.ProyectGridForm', {
    extend: 'Ext.form.Panel',
    xtype: 'ProyectGridForm',
    id: 'ProyectGridForm',
    requires: [
       'Ext.layout.container.HBox',
       'Ext.form.field.Date',
       'Ext.ux.TreePicker'
    ],
    layout: 'hbox',
    cls: 'tasks-new-form',

    initComponent: function() {
        
        this.items = [{
                xtype: 'textfield',
                name: 'idElement',
                id: 'FileFormElementId',
                hidden: true
            },{
                xtype: 'component',
                width: 40,
                height: 24
            },{
                xtype: 'textfield',
                name: 'nombre',
                id: 'FileFormElementName',
                emptyText: 'Agregar nuevo elemento',
                flex: 1,
                margin: '0 18 0 0',
            },{
                xtype: 'component',
                //cls: 'tasks-new',
                width: 28,
                height: 24
            },{
                xtype: 'component',
                //cls: 'tasks-new',
                width: 28,
                height: 24
            }
            /*{
                xtype: 'treepicker',
                name: 'list_id',
                displayField: 'name',
                store: Ext.create('SimpleTasks.store.Lists', {storeId: 'Lists-TaskForm'}),
                width: 195
            },
            {
                xtype: 'datefield',
                name: 'due',
                value: new Date(),
                width: 95
            }*/
        ];

        this.callParent(arguments);
    }

});