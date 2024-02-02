Ext.define('SEMTI.store.Resumenpindicadores', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Resumenpindicadores',
    autoLoad: false,
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/informes/ResumenActions.php'
        },
        reader: {
            type: 'json',
            root: 'pindicadores',
            successProperty: 'success',
            messageProperty: 'message'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'pindicadores'
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