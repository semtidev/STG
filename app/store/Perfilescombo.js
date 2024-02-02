Ext.define('SEMTI.store.Perfilescombo', {
	extend: 'Ext.data.Store',
	model: 'SEMTI.model.Perfilescombo',
	//fields: ['id','nombre'],
	//autoLoad: true,
	proxy: {
		type: 'ajax',
		url : './php/administracion/PerfilesCombo.php',
		reader: {
			type: 'json',
			root: 'perfilescombo',
			successProperty: 'success'
		}
	}	
});