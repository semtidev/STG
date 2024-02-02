Ext.define('SEMTI.store.Gtialocaciones', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Gtialocaciones',
	autoLoad: true,
	proxy: {
		type: 'ajax',
		url : './php/garantia/GtiaLocacionesCombo.php',
		reader: {
			type: 'json',
			root: 'gtialocaciones',
			successProperty: 'success'
		}
	}	
});