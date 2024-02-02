Ext.define('SEMTI.store.Perfiles', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Perfiles',
    pageSize: 50,
    autoLoad: {start: 0, limit: 50},
    autoLoad: false,
            proxy: {
                type: 'ajax',
                api: {
                    read: 'php/administracion/PerfilesActions.php'
                },
                reader: {
                    type: 'json',
                    root: 'perfiles',
                    successProperty: 'success',
                    messageProperty: 'message'
                },
                writer: {
                    type: 'json',
                    encode: true,
                    root: 'perfiles'
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