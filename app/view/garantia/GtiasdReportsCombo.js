Ext.define('SEMTI.view.garantia.GtiasdReportsCombo', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'GtiasdReportsCombo',
 	store: Ext.create('Ext.data.Store', {
		fields: ['abbr', 'name'],
		data : [
			{"abbr":"SDPendientes","name":"SD Pendientes por Resolver"},
			{"abbr":"indicadores","name":"Principales Indicadores de Garant\xEDa"},
			{"abbr":"CoDir","name":"Resumen Consejo de la Administraci\xF3n"}
		]
	}),
	displayField: 'name',
	valueField: 'abbr',
    queryMode: 'local',
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>'+
                      '<img src="./resources/images/icons/16x16-free-application-icons/png/16x16/Report.png" align="absmiddle">&nbsp;&nbsp;'+
                      '{name}</div>';
            return tpl;
        }
    }
});	