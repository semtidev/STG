Ext.define('SEMTI.model.Resumen', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_user', 'titulo', 'proyecto', 'zona', {name:'desde', type:'date', dateFormat: 'c'}, {name:'hasta', type:'date', dateFormat: 'c'}, 'comentario_inicial','comentario_final', {name:'fechamod', type:'date', dateFormat: 'c'}]
});