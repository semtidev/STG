Ext.define('SEMTI.model.Resumenhfo', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_resumen', 'sd', 'habitaciones', 'ctdad_habit', 'pendientes', 'problema', 'observaciones', {name:'fechamod', type:'date', dateFormat: 'c'}]
}); 