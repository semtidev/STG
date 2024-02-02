Ext.define('SEMTI.model.Resumensdpendientes', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_resumen', 'problema_sd', 'descripcion', 'zonas', 'objetos', 'locales', 'dpto', 'comentario', {name:'fechamod', type:'date', dateFormat: 'c'}]
});