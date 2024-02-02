// Localizacion de los UX
Ext.Loader.setPath('Ext.ux', 'js/extjs4/includes/ux/');

Ext.application({
    name: 'SEMTI',
    appFolder: 'app',
    controllers: ['App','Administracion','Proyectos','Garantia','Graficas','Hfo','Resumen'],
    models:['SEMTI.model.CurrentUser'],
    stores:['SEMTI.store.CurrentUser'],
	launch: function() {
        
        currentUser = Ext.create('SEMTI.store.CurrentUser');
		currentUser.load({
            callback: function(records, operation, success) {
                scope: this,
                currentUserData = records[0];
		        Ext.Element.get('currentuser_avatar').setHTML('<img src="resources/images/users/' + currentUserData.get('avatar') + '"/>');
                Ext.Element.get('currentuser_name').setHTML(currentUserData.get('nombre'));
		        Ext.Element.get('currentuser_lastname').setHTML(currentUserData.get('apellidos'));
                portada = 'index.html';
                if(currentUserData.get('portada') == 'Controlpanel'){
                    portada = 'controlpanel/index.php';
                }
                Ext.Element.get('Portada').update('<iframe name="content_pages" id="iframe" width="100%" height="100%"" src="./app/view/portada/'+ portada +'" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" style="overflow:auto;"></iframe>');
            }                               
        });
		
		Ext.create('SEMTI.view.App');
    }	
});