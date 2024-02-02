Ext.define('SEMTI.view.sistema.Poloscombo', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.poloscombo',
	id: 'poloscombo',
 	store: Ext.create('SEMTI.store.Polos'),
	editable: false,
	displayField: 'nombre',
	valueField: 'id'
});
	