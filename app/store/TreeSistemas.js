Ext.define('SEMTI.store.TreeSistemas', {
	extend: 'Ext.data.TreeStore',
	model: 'SEMTI.model.TreeSistemas',
	proxy: {
		type: 'ajax', // Because it's a cross-domain request
		url : './php/sistema/treesistemasusers.php',
		reader:{
			type: 'json'
		}
	}/*,
	root: {
		name: 'root',
		expanded: true
	}*/
});