// Documento de Funciones Empleadas en la Aplicacion

////////////////////////////////////////
///////    FUNCIONES PORTADA    ////////
////////////////////////////////////////


// Cerrar sesion usuario
function logout(){
	
	Ext.Msg.confirm("Finalizar Sesi\xF3n", "Confirma que desea finalizar su sesi\xF3n de usuario en el sistema?", function(btnText){
		if(btnText === "yes"){
			
			Ext.Ajax.request({ //dispara la petición
										
				url: './php/sistema/logout.php',
				method:'POST', 
				//waitTitle: 'Espere',   
				//waitMsg: 'Enviando datos..',    
				params:{accion: 'Logout'},
				success: function(result, request) { 
					
					var jsonData = Ext.JSON.decode(result.responseText);
					
					if(!jsonData.failure){
					
						document.location.reload();
					}
					else{
						
						Ext.MessageBox.show({
						   title: 'Mensaje del Sistema',
						   msg: jsonData.message,
						   buttons: Ext.MessageBox.OK,
						   icon: Ext.MessageBox.ERROR
						});	
					}
				},
				failure: function() { 
											
					Ext.MessageBox.show({
					   title: 'Mensaje del Sistema',
					   msg: 'Ha ocurrido un error en el Sistema. Por favor, vuelva a intentar realizar la operacion, de continuar el problema consulte al Administrador del Sistema.',
					   buttons: Ext.MessageBox.OK,
					   icon: Ext.MessageBox.ERROR
					});
				}
			});
		}
	}, this);
}

// Mostrar Mensajes del usuario
function showmessage(){
	
	var panel = Ext.getCmp('msgconsole');
	
	if (panel.getCollapsed() === false) 
	panel.collapse();
    else 
	panel.expand();
}

// Mostrar Perfil del usuario
function userPerfil(){
	
	var userPerfil = Ext.create('SEMTI.view.sistema.UserPerfil'),
	    formPerfil = userPerfil.down('form');

        formPerfil.getForm().load({
            url: './php/sistema/SystemActions.php',
            method: 'POST',
            params: {
                accion: 'LoadUserPerfil', id: currentUserData.get('id')
            },
            failure: function(form, action) {
                editar.close();
                Ext.Msg.alert("Carga Fallida", "La carga de los parametros del Usuario no se ha realizado. Por favor, intentelo de nuevo, de mantenerse el problema contacte con el Administrador del Sistema. ");
            }
        });
}
////////////////////////////////////////
////////////////////////////////////////
