Ext.define('SEMTI.view.garantia.GtiasdTipoCompra', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.GtiasdTipoCompra',
    store: Ext.create('Ext.data.Store', {
            fields: ['abbr', 'name'],
            data : [
                    {"name":"Compra Interna", "abbr":"Interna"},
                    {"name":"Compra Local", "abbr":"Local"},
                    {"name":"Compra por Importaci\xF3n", "abbr":"Import"},
            ]
    }),
    displayField: 'name',
    valueField: 'abbr',
    queryMode: 'local'/*,
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>'+
                      '<img src="./resources/images/icons/{icon}.png" align="absmiddle">&nbsp;&nbsp;'+
                      '{name}</div>';
            return tpl;
        }
    }*/
});	