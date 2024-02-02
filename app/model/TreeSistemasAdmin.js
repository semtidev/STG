Ext.define('SEMTI.model.TreeSistemasAdmin',{
	extend: 'Ext.data.Model',
	fields: [
        { name: 'id', type: 'int' },
        { name: 'text', type: 'string' },
        { name: 'modificar', type: 'boolean' },
        { name: 'lectura_exportar', type: 'boolean' },
        { name: 'lectura', type: 'boolean' },
        { name: 'escritura', type: 'boolean' }
    ]
});