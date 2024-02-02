Ext.define('SEMTI.model.Usuarios', {
    extend: 'Ext.data.Model',
    fields: ['id_usuario', 'nombre', 'apellidos', 'cargo', 'usuario',  'password', 'activo', 'email', 'expira']
});