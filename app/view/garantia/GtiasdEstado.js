Ext.define('SEMTI.view.garantia.GtiasdEstado', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.gtiasdestado',
    store: Ext.create('Ext.data.Store', {
            fields: ['name', 'icon'],
            data : [
                    {"name":"Por Resolver","icon":"sad"},
                    {"name":"No Procede","icon":"confuced"},
                    {"name":"Reclamada","icon":"angry"},
                    {"name":"En Proceso","icon":"smile"},
                    {"name":"Firmada","icon":"grin"}
            ]
    }),
    displayField: 'name',
    valueField: 'name',
    queryMode: 'local',
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>'+
                      '<img src="./resources/images/icons/{icon}.png" align="absmiddle">&nbsp;&nbsp;'+
                      '{name}</div>';
            return tpl;
        }
    }
});	