Ext.define('SEMTI.view.garantia.GtiaproblemasForm', {
    extend: 'Ext.form.Panel',
    xtype: 'gtiaproblemasform',
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
                id: 'GtiaproblemasFormDescripcion',
                name: 'descripcion',
                emptyText: 'Agregar nuevo tipo de problema',
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