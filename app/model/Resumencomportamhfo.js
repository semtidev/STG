Ext.define('SEMTI.model.Resumencomportamhfo', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_resumen', 'indicador', 'demora', 'ctdad', 'meta', 'estado', 'tendencia', {name:'fechamod', type:'date', dateFormat: 'c'}]
}); 