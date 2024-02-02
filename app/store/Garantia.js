Ext.define('SEMTI.store.Garantia', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Garantia',
    //autoLoad: true,
    pageSize: 10,
    autoLoad: {start: 0, limit: 10},
 
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/Garantia/GarantiaList.php'
        },
        reader: {
            type: 'json',
            root: 'garantia',
            successProperty: 'success',
			messageProperty	: 'message'
        },
        writer: {
            type: 'json',
            //writeAllFields: true,
            encode: true,
            root: 'garantia'
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