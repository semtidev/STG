Ext.define('SEMTI.model.Resumenpindicadores', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_resumen', 'indicador', 'periodo_ant', 'periodo_act', 'acumulado', 'meta', 'estado', 'tendencia', 'acciones', {name:'fechamod', type:'date', dateFormat: 'c'}]
}); 