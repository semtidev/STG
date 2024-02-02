Ext.define('SEMTI.model.Gtiasd', {
    extend: 'Ext.data.Model',
    fields: ['id', 'numero', 'problema', 'descripcion', 'proyecto', 'id_proyecto', 'zona', 'objeto', 'dpto', {name:'fecha_reporte', type:'date', dateFormat: 'c'}, 'fechareporte_string', {name:'fecha_solucion', type:'date', dateFormat: 'c'}, 'demora', 'estado', 'constructiva', 'suministro', 'afecta_explotacion', 'comentario', {name:'fecha_mod', type:'date', dateFormat: 'c'},'documento', 'compra', 'imagen', 'causa', 'fecha_almacen', 'costo']
});