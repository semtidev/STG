Ext.define('SEMTI.store.Polos', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Polos',
	autoLoad: true,
	proxy: {
		type: 'ajax',
		url : './php/sistema/Polos.php',
		reader: {
			type: 'json',
			root: 'polos',
			successProperty: 'success'
		}
	}	
});