<?php
	// Incluir la clase de conexion
	include_once("../sistema/connect.php");
	$connect = new Connect();
	
	// Llamar la funcion que conecta a la BD
	$connect->connMYSQL_SEMTI();
	
	// Incluir la clase de tratamiento de cadenas
	include_once("../sistema/cadenas.php");
	$cadenas = new Cadenas();
	
	// Se inicializa el formato JSON
	$gtialocaciones = '{"success":true,"gtialocaciones":[';
	
	$sql   = "SELECT DISTINCT(locacion) FROM gtia_sd ORDER BY locacion ASC";
	$query = $adoMYSQL_SEMTI->Execute($sql);
	$total = $query->RecordCount();
	$count = 0;
	
	if($total > 0){
		
		while($result = $query->FetchRow()){
			
			$count++;
			$gtialocaciones .= '{"locacion":"'.utf8_encode($result['locacion']).'"';
			
			if($count < $total) $gtialocaciones .= '},'; else $gtialocaciones .= '}';
		}
	}
	
	// Se cierra el formato JSON
	$gtialocaciones .= ']}';
	
	// Imprimir formato JSON
	echo $gtialocaciones;
		 
?>