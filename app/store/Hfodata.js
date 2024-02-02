Ext.define('SEMTI.store.Hfodata', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Hfodata',
    autoLoad: false,
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/informes/HfoActions.php'
        },
        reader: {
            type: 'json',
            root: 'hfodata',
            successProperty: 'success',
            messageProperty: 'message'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'hfo'
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