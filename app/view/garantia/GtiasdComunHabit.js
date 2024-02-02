Ext.define('SEMTI.view.garantia.GtiasdComunHabit', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.GtiasdComunHabit',
    store: Ext.create('Ext.data.Store', {
            fields: ['name'],
            data : [
                    {"name":"SD Comunes"},
                    {"name":"SD Habitaciones"}
            ]
    }),
    displayField: 'name',
    valueField: 'name',
    queryMode: 'local',
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>'+
                      '<img src="./resources/images/icons/inbox-table.png" align="absmiddle">&nbsp;&nbsp;'+
                      '{name}</div>';
            return tpl;
        }
    }
});	