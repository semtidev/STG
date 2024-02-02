Ext.define('SEMTI.view.sistema.SystDptos', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'SystDptos',
 	store: Ext.create('SEMTI.store.Systdptos'),
	displayField: 'nombre',
	valueField: 'nombre',
    multiSelect: true,
	queryMode: 'local'    
});
	