Ext.define('SEMTI.store.Gtiachartsdpendientes', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Gtiachartsdpendientes',
	autoLoad: true,
	proxy: {
		type: 'ajax',
		url : './php/garantia/Gtiachartsdpendientes.php',
		reader: {
			type: 'json',
			root: 'chartsdpendientes',
			successProperty: 'success'
		}
	}	
});