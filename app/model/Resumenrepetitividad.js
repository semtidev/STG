Ext.define('SEMTI.model.Resumenrepetitividad', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_resumen', 'problema_descripcion', 'sd_descripcion', 'sd_ctdad', 'comentario', {name:'fechamod', type:'date', dateFormat: 'c'}]
});