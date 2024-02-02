Ext.define('SEMTI.store.Gtiasdpendientes', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Gtiasd',
    pageSize: 50,
    autoLoad: {start: 0, limit: 50},
    //autoLoad: false,
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/garantia/SdPendientes.php'
        },
        reader: {
            type: 'json',
            root: 'sd',
            successProperty: 'success',
            messageProperty: 'message',
            totalProperty: 'total'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'sd'
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
    },
    constructor: function() {
        this.callParent(arguments);
        if (this.autoLoad) {
            this.loading = true;
        }
    }
});