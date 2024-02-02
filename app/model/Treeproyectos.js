Ext.define('SEMTI.model.Treeproyectos', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'text', type: 'string'},
        {name: 'ruta', type: 'string'},
        {name: 'presupuesto'},
        {name: 'polo', type: 'string'},
        {name: 'nombre_comercial', type: 'string'},
        {name: 'tipo', type: 'string'},
        {name: 'activo'},
        {name: 'fecha_ini'},
        {name: 'fecha_fin'},
        {name: 'fecha_inicio'},
        {name: 'fecha_terminacion'}
    ]
});