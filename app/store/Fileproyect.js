Ext.define('SEMTI.store.Fileproyect', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Fileproyect',
	autoLoad: false,
	proxy: {
        type: 'ajax',
        api: {
            read:    './php/proyectos/gridproyectList.php',
            /*update:  './php/sistema/treeProyectsUpdate.php',*/
            destroy: './php/sistema/treeProyectsDestroy.php'
        },
        reader: {
            type: 'json',
            root: 'proyect',
            successProperty: 'success',
            messageProperty: 'message'
        }
    },
	root: {
        expanded: true,
        id: -1,
        name: 'Proyects'
    }
});