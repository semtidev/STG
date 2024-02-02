Ext.define('SEMTI.model.Hfodata', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_infohfo', 'sd', 'habitaciones', 'ctdad_habit', 'pendientes', 'problema', 'observaciones', {name:'fechamod', type:'date', dateFormat: 'c'}]
}); 