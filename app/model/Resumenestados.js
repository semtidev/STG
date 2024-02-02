Ext.define('SEMTI.model.Resumenestados', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_resumen', 'indicador', 'enproceso', 'noproceden', 'poresolver', 'reclamadas', 'firmadas', 'total', {name:'fechamod', type:'date', dateFormat: 'c'}]
});