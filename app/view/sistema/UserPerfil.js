Ext.define('SEMTI.view.sistema.UserPerfil', {
    extend: 'Ext.window.Window',
    alias: 'widget.userperfil',
    layout: 'fit',
    autoShow: true,
    width: 450,
    height: 450,
    resizable: false,
    modal: true,
    maximizable: false,
    title: 'Perfil de Usuario',
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                border: false,
                modal: true,
                bodyStyle: 'background:url(resources/images/bg/perfil-bg.png)',
                     
                fieldDefaults: {
                    anchor: '100%',
                    labelAlign: 'left',
                    labelWidth: 110,
                    combineErrors: true,
                    msgTarget: 'side'
                },
                items: [{
                    xtype: 'component',
                    width: 100,
                    height: 100,
                    id: 'avatarUser',
                    cls: 'avatar_perfil',
                    margin: '20 0 10 165'
                },{
					xtype: 'filefield',
					id: 'perfil-avatarfile',
					emptyText: 'Imagen 100x100',
					name: 'avatar',
					editable: false,
					margin: '0 0 10 160',
					buttonText: 'Cambiar \xC1vatar...',
					buttonOnly: true
				},{
					xtype: 'component',
					id: 'nameUser',
                    cls: 'nombre_perfil',
                    margin: '5 0 30 0'
				},{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					margin: '0 0 10 0',
					items: [{
							xtype: 'component',
							html: 'Usuario:',
							cls: 'parametros_perfil',
							margin: '0 10 0 0'
						},{
							xtype: 'component',
							id: 'loginUser',
							cls: 'descripcion_perfil',
							margin: '0 0 0 0'
					}]
				},{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					margin: '10 0 10 0',
					items: [{
							xtype: 'component',
							html: 'Cargo:',
							cls: 'parametros_perfil',
							margin: '0 10 0 0'
						},{
							xtype: 'component',
							id: 'rolUser',
							cls: 'descripcion_perfil',
							margin: '0 0 0 0'
					}]
				},{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					margin: '0 0 10 0',
					items: [{
							xtype: 'component',
							html: 'E-Mail:',
							cls: 'parametros_perfil',
							margin: '0 10 0 0'
						},{
							xtype: 'component',
							id: 'emailUser',
							cls: 'descripcion_perfil',
							margin: '0 0 0 0'
					}]
				},{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					margin: '0 0 10 0',
					items: [{
							xtype: 'component',
							html: 'Polo:',
							cls: 'parametros_perfil',
							margin: '0 10 0 0'
						},{
							xtype: 'component',
							html: localStorage.getItem('polo_name'),
							cls: 'descripcion_perfil',
							margin: '0 0 0 0'
					}]
				},{
					xtype: 'box',
					html: '<center>Semti Garantía v2.0</center>',
					width: 350,
					margin: '48 50 0 50'
				}]
            }];

        this.callParent(arguments);
    }
});