Ext.define('SEMTI.store.Treeusuarioproyectos', {
	extend: 'Ext.data.TreeStore',
	model: 'SEMTI.model.Treeusuarioproyectos',
	proxy: {
		type: 'ajax', // Because it's a cross-domain request
		url : './php/sistema/treeusuarioproyectos.php',
		reader:{
			type: 'json'
		}
	}
});