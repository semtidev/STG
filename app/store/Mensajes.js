Ext.define('SEMTI.store.Mensajes', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Mensajes',
    autoLoad: true, 
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/sistema/MensajesList.php'
        },
        reader: {
            type: 'json',
            root: 'mensajes',
            successProperty: 'success',
			messageProperty	: 'message'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'mensajes'
        },
		listeners: {
                  exception: function(proxy, response, operation){
                      
                     Ext.MessageBox.show({
                        title: 'Mensaje del Sistema',
                        msg: operation.getError(),
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.Msg.OK
                     });
                  }
                  
                  
	    }
    }
});