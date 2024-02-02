Ext.define('SEMTI.store.Systdptos', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Systdptos',
	autoLoad: true,
	proxy: {
		type: 'ajax',
		url : './php/sistema/DptosActions.php',
		reader: {
			type: 'json',
			root: 'dptos',
			successProperty: 'success'
		}
	}	
});