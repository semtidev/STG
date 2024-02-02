Ext.define('SEMTI.store.Treeproyectos', {
    extend: 'Ext.data.TreeStore',
    model: 'SEMTI.model.Treeproyectos',
    proxy: {
        type: 'ajax', // Because it's a cross-domain request
        api: {
            create:  './php/proyectos/treeProyectsCreate.php',
            read:    './php/proyectos/treeProyectActions.php',
            update:  './php/proyectos/treeProyectsUpdate.php',
            destroy: './php/proyectos/treeProyectsDestroy.php'
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