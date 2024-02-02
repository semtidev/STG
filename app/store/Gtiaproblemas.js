Ext.define('SEMTI.store.Gtiaproblemas', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Gtiaproblemas',
	autoLoad: true,
	proxy: {
		type: 'ajax',
		url : './php/garantia/GtiaproblemasActions.php',
		reader: {
			type: 'json',
			root: 'gtiaproblemas',
			successProperty: 'success'
		}
	}	
});