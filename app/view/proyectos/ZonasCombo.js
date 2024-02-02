Ext.define('SEMTI.view.proyectos.ZonasCombo', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'ZonasCombo',
    store: Ext.create('SEMTI.store.Zonas'),
    displayField: 'nombre',
    valueField: 'nombre',
    multiSelect: true,
    queryMode: 'local',
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>'+
                      '<img src="./resources/images/icons/zonas.png" align="absmiddle">&nbsp;&nbsp;'+
                      '{nombre}</div>';
            return tpl;
        }
    }
});
	