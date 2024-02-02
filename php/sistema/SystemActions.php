<?php
	include_once 'SystemClass.php';
	$system = new System();
	
	// Cargar el Store del Usuario actual 
	if((!isset($_POST['accion'])) && (!isset($_GET['accion']))){
		
		echo $system->LoadCurrentUser();
	}
	
	// Cargar Perfil de Usuario
	if($_POST['accion'] == 'LoadUserPerfil'){
				
		$id_user = $_POST['id'];

		echo $system->LoadUserPerfil($id_user);
	}

	// Actualizar Perfil de Usuario
	if($_POST['accion'] == 'UpdateCurrentUser'){
				
		$image     = $_FILES['avatar'];
		$typeImage = $image['type'];
		$sizeImage = $image['size'];
    	$nameImage = $image['name'];
    	if ($_POST['notificaciones'] == 'on'){ $notificaciones = 'Si'; }
   		else{ $notificaciones = 'No'; }

		echo $system->UpdateCurrentUser($image,$typeImage,$nameImage,$sizeImage,$notificaciones);
	}

	// Cargar Configuraciones
	if($_POST['accion'] == 'LoadConfig'){
				
		echo $system->LoadConfig();
	}
	
	// Cargar Portada del usuario
	if($_POST['accion'] == 'LoadPortada'){
				
		echo $system->LoadPortada();
	}
	
	// Actualizar Portada del usuario
	if($_POST['accion'] == 'UpdatePortada'){
		
		if ($_POST['presentacion'] == 'on'){ $portada = 'Show'; }
		else{ $portada = 'Controlpanel'; }	
		
		echo $system->UpdatePortada($portada);
	}
		
	// Actualizar Configuraciones
	if($_POST['accion'] == 'UpdateConfig'){
		
		$ipserver  = $_POST['ipserver'];

		echo $system->UpdateConfig($ipserver);
	}
	