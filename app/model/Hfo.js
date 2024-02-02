Ext.define('SEMTI.model.Hfo', {
    extend: 'Ext.data.Model',
    fields: ['id', 'id_user', 'titulo', 'proyecto', 'zona', 'objeto', {name:'desde', type:'date', dateFormat: 'c'}, {name:'hasta', type:'date', dateFormat: 'c'}, {name:'fechamod', type:'date', dateFormat: 'c'}]
});