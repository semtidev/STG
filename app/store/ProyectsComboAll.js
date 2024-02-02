Ext.define('SEMTI.store.ProyectsComboAll', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.ProyectsCombo',
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: './php/proyectos/ProyectsComboAll.php',
        reader: {
            type: 'json',
            root: 'proyectsall',
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