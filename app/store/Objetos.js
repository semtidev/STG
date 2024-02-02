Ext.define('SEMTI.store.Objetos', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Objetos',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: './php/proyectos/objetosComboList.php',
        reader: {
            type: 'json',
            root: 'objetos',
            successProperty: 'success',
            messageProperty: 'message'
        }
    }/*,
    listeners: {
        load: function(store) {
            store.add({nombre: 'Todas'});
        }
    }*/
});