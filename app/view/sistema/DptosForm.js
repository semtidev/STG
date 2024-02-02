Ext.define('SEMTI.view.sistema.DptosForm', {
    extend: 'Ext.form.Panel',
    xtype: 'DptosForm',
    requires: [
        'Ext.layout.container.HBox',
    ],
    layout: 'hbox',
    cls: 'tasks-new-form',
    initComponent: function() {
        this.items = [
            {
                xtype: 'textfield',
                name: 'id',
                hidden: true
            }, {
                xtype: 'component',
                width: 35,
                height: 24
            }, {
                xtype: 'textfield',
                id: 'DptosFormNombre',
                name: 'nombre',
                emptyText: 'Agregar nuevo departamento',
                flex: 1
            }, {
                xtype: 'component',
                width: 28,
                height: 24
            }, {
                xtype: 'component',
                width: 28,
                height: 24
            }
        ];

        this.callParent(arguments);
    }

});