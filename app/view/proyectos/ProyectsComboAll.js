Ext.define('SEMTI.view.proyectos.ProyectsComboAll', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'ProyectsComboAll',
    store: Ext.create('SEMTI.store.ProyectsComboAll'),
    displayField: 'nombre',
    valueField: 'nombre',
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
	