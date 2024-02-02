Ext.define('SEMTI.view.garantia.GtiasdConSumAEH', {
    extend: 'Ext.form.field.ComboBox',
    xtype: 'GtiasdConSumAEH',
    store: Ext.create('Ext.data.Store', {
        fields: ['abbr', 'name', 'icon'],
        data: [
            {"abbr": "1", "name": "Si", "icon": "check"},
            {"abbr": "0", "name": "No", "icon": "check2"}
        ]
    }),
    displayField: 'name',
    valueField: 'abbr',
    queryMode: 'local',
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>' +
                    '<img src="./resources/images/icons/{icon}.png" align="absmiddle">&nbsp;&nbsp;' +
                    '{name}</div>';
            return tpl;
        }
    }
});	