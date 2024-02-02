Ext.define('SEMTI.store.Zonas', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Zonas',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: './php/proyectos/zonasComboList.php',
        reader: {
            type: 'json',
            root: 'zonas',
            successProperty: 'success',
			messageProperty	: 'message'
        }
    }/*,
    listeners: {
        load: function(store) {
            store.add({nombre: 'Todas'});
        }
    }*/
});