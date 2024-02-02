Ext.define('SEMTI.store.CurrentUser', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.CurrentUser',
	autoLoad: false,
	proxy: {
        type: 'ajax',
        api: {
            read: 'php/sistema/SystemActions.php'
        },
        reader: {
            type: 'json',
            root: 'currentuser',
            successProperty: 'success',
            messageProperty: 'message'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'currentuser'
        },
        listeners: {
            exception: function(proxy, response, operation) {

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