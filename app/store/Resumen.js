Ext.define('SEMTI.store.Resumen', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Resumen',
    autoLoad: true,
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/informes/ResumenActions.php'
        },
        reader: {
            type: 'json',
            root: 'resumen',
            successProperty: 'success',
            messageProperty: 'message'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'resumen'
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