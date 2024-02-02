Ext.define('SEMTI.store.Usuarios', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Usuarios',
    pageSize: 50,
    autoLoad: {start: 0, limit: 50},
    autoLoad: false,
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/administracion/UsuariosActions.php'
        },
        reader: {
            type: 'json',
            root: 'usuarios',
            successProperty: 'success',
            messageProperty: 'message'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'usuarios'
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