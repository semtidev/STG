Ext.define('SEMTI.store.Proyectspicker', {
	extend: 'Ext.data.TreeStore',
	model: 'SEMTI.model.Proyectspicker',
	proxy: {
		type: 'ajax', // Because it's a cross-domain request
		api: {
            read: 'php/proyectos/pickerProyects.php'
        },
		reader: {
            type: 'json',
            messageProperty: 'message'
        }
	},
	root: {
        expanded: false,
        id: -1,
        text: 'Todos los Proyectos'
    }
});