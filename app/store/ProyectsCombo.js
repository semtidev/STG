Ext.define('SEMTI.store.ProyectsCombo', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.ProyectsCombo',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: './php/proyectos/ProyectsCombo.php',
        reader: {
            type: 'json',
            root: 'proyects',
            successProperty: 'success',
			messageProperty	: 'message'
        }
    }/*,
    listeners: {
        load: function(store) {
            store.add({id: 'Todos', nombre: 'Todos los Proyectos'});
        }
    }*/
});