Ext.define('SEMTI.model.Garantia', {
    extend: 'Ext.data.Model',
    fields: ['id', 'no_sd', 'hotel', 'habitaciones', 'no_hab_fuera_orden', 'motivo', {name:'desde', type:'date', dateFormat: 'c'}, {name:'hasta', type:'date', dateFormat: 'c'}]
});