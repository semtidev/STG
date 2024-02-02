Ext.define('SEMTI.store.Treesistemasadmin', {
	extend: 'Ext.data.TreeStore',
	model: 'SEMTI.model.TreeSistemasAdmin',
	proxy: {
		type: 'ajax', // Because it's a cross-domain request
		url : './php/sistema/treesistemasadmin.php',
		reader:{
			type: 'json'
		}
	}
});