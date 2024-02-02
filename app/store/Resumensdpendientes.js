Ext.define('SEMTI.store.Resumensdpendientes', {
    extend: 'Ext.data.Store',
    model: 'SEMTI.model.Resumensdpendientes',
    autoLoad: false,
    //sorters: {property: 'problema_sd', direction: 'ASC'},
    groupField: 'problema_sd',
    proxy: {
        type: 'ajax',
        api: {
            read: 'php/informes/ResumenActions.php'
        },
        reader: {
            type: 'json',
            root: 'sdpendientes',
            successProperty: 'success',
            messageProperty: 'message'
        },
        writer: {
            type: 'json',
            encode: true,
            root: 'sdpendientes'
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