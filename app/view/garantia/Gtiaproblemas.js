Ext.define('SEMTI.view.garantia.Gtiaproblemas', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.Gtiaproblemascombo',
 	store: Ext.create('SEMTI.store.Gtiaproblemas'),
	displayField: 'descripcion',
	valueField: 'descripcion',
});
	