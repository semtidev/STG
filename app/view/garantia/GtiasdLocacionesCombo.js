Ext.define('SEMTI.view.garantia.GtiasdLocacionesCombo', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.gtiasdlocacionescombo',
 	store: Ext.create('SEMTI.store.Gtialocaciones'),
	displayField: 'locacion',
	valueField: 'locacion',
	queryMode: 'local'
});
	