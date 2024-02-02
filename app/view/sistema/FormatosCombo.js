Ext.define('SEMTI.view.sistema.FormatosCombo', {
    extend: 'Ext.form.field.ComboBox',
    alias : 'widget.formatoscombo',
    store: Ext.create('Ext.data.Store', {
            fields: ['abbr', 'name', 'icon'],
            data : [
                    {"abbr":"PDF","name":"Documento PDF","icon":"pdf"},
                    {"abbr":"PPT","name":"Presentaci\xF3n de PowerPoint","icon":"pp"}/*,
                    {"abbr":"XLS","name":"Libro de Excel","icon":"xl"},
                    {"abbr":"DOC","name":"Documento de Word","icon":"wd"}*/
            ]
    }),
    displayField: 'name',
    valueField: 'abbr',
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