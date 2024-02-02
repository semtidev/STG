Ext.define('SEMTI.store.Gtiagridtemp', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Gtiagridtemp',
	autoLoad: true,
	proxy: {
		type: 'ajax',
		url : './php/garantia/Gtiagridtemp.php',
		reader: {
			type: 'json',
			root: 'gtiagridtemp',
			successProperty: 'success'
		}
	}	
});