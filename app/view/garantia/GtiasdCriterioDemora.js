Ext.define('SEMTI.view.garantia.GtiasdCriterioDemora', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'GtiasdCriterioDemora',
 	store: Ext.create('Ext.data.Store', {
		fields: ['abbr', 'name'],
		data : [
			{"name":"<"},
			{"name":"<="},
			{"name":">"},
			{"name":">="},
			{"name":"="}
		]
	}),
	displayField: 'name',
	valueField: 'name',
    queryMode: 'local'
});