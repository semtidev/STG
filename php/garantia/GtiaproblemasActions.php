<?php
	include_once 'GtiaproblemasClass.php';
	$problemas = new Problemas();
	
	// Leer Todas las SD
	if((!isset($_POST['accion'])) && (!isset($_GET['accion']))){
		
		// Recibir parametros del Store para paginacion
		echo $problemas->ReadProblemas();
	}
	
	// Insertar Problema
	elseif($_POST['accion'] == 'InsertarProblema'){
		
		$descripcion = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['descripcion']));
		
		echo $problemas->NewProblema($descripcion);
	}
	
	// Actualizar Problema
	elseif($_POST['accion'] == 'ActualizarProblema'){
		
		$id_problema = $_POST['id']; 
		$descripcion = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['descrip']));
		
		echo $problemas->UpdProblema($id_problema,$descripcion);
	}
	
	// Eliminar Problema
	elseif($_POST['accion'] == 'EliminarProblema'){
		
		$id_problema = $_POST['id']; 
		
		echo $problemas->DelProblema($id_problema);
	}
?>