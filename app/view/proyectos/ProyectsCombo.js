Ext.define('SEMTI.view.proyectos.ProyectsCombo', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'ProyectsCombo',
    store: Ext.create('SEMTI.store.ProyectsCombo'),
    displayField: 'nombre',
    valueField: 'nombre',
    emptyText: 'Obra Constructiva',
    queryMode: 'local',
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>'+
                      '<img src="./resources/images/icons/16x16-free-application-icons/png/16x16/Company.png" align="absmiddle">&nbsp;&nbsp;'+
                      '{nombre}</div>';
            return tpl;
        }
    }
});
	