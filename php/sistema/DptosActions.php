<?php
	include_once 'DptosClass.php';
	$dptos = new Departamentos();
	
	// Leer Todos los Dptos
	if((!isset($_POST['accion'])) && (!isset($_GET['accion']))){
		
		// Recibir parametros del Store para paginacion
		echo $dptos->ReadDptos();
	}
	
	// Insertar Dpto
	elseif($_POST['accion'] == 'InsertarDpto'){
		
		$nombre = $cadenas->codificarBD_utf8($_POST['nombre']);
		
		echo $dptos->NewDpto($nombre);
	}
	
	// Actualizar Dpto
	elseif($_POST['accion'] == 'ActualizarDpto'){
		
		$id_dpto = $_POST['id']; 
		$nombre  = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['nombre']));
		
		echo $dptos->UpdDpto($id_dpto,$nombre);
	}
	
	// Eliminar Dpto
	elseif($_POST['accion'] == 'EliminarDpto'){
		
		$id_dpto = $_POST['id']; 
		
		echo $dptos->DelDpto($id_dpto);
	}
?>