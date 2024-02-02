Ext.define('SEMTI.store.Treeseguimiento', {
	extend: 'Ext.data.TreeStore',
	model: 'SEMTI.model.Treeseguimiento',
	proxy: {
		type: 'ajax', // Because it's a cross-domain request
		url : './php/sistema/treeseguimiento.php',
		reader:{
			type: 'json'
		}
	}/*,
	root: {
		name: 'root',
		expanded: true
	}*/
});