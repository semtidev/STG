Ext.define('SEMTI.view.garantia.GtiasdCriterioCosto', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'GtiasdCriterioCosto',
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