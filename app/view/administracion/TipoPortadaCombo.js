Ext.define('SEMTI.view.administracion.TipoPortadaCombo', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.TipoPortadaCombo',
    store: Ext.create('Ext.data.Store', {
            fields: ['value', 'name'],
            data : [
                    {"name":"Presentaci\xF3n", "value":"Show"},
                    {"name":"Panel de Control", "value":"Controlpanel"}
            ]
    }),
    displayField: 'name',
    valueField: 'value',
    queryMode: 'local'
});	