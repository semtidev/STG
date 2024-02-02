Ext.define('SEMTI.view.proyectos.ObjectsCombo', {
    extend: 'Ext.form.field.ComboBox',
    xtype : 'ObjectsCombo',
    store: Ext.create('SEMTI.store.Objetos'),
    displayField: 'nombre',
    valueField: 'nombre',
    multiSelect: true,
    queryMode: 'local',
    listConfig: {
        getInnerTpl: function() {
            var tpl = '<div>'+
                      '<img src="./resources/images/icons/house.png" align="absmiddle">&nbsp;&nbsp;'+
                      '{nombre}</div>';
            return tpl;
        }
    }
});
	