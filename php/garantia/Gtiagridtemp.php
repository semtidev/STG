<?php
	// Inicializar la sesion activa
	session_name('semtiGarantiaSession');
	session_start();
	
	// Incluir la clase de conexion
	include_once '../sistema/connect.php';
	$connect = new Connect();
	
	// Llamar la funcion que conecta a la BD
	$connect->connMSSQL_SEMTI();
	
	// Incluir la clase de tratamiento de cadenas
	include_once '../sistema/cadenas.php';
	$cadenas = new Cadenas();
	
	$id_user = $_SESSION['idsession'];
	
	// Se inicializa el formato JSON
	$gtiagridtemp = '{"success":true,"gtiagridtemp":[';
        
    $sql    = "SELECT id,id_user,ruta,ubicacion,estado FROM gtia_sd_objetospartes_temp WHERE id_user = $id_user";
	$query  = $adoMSSQL_SEMTI->Execute($sql);
	$total  = $query->RecordCount();
	$count  = 0;
	$estado = '';
	
	if($total > 0){		
            while(!$query->EOF){

                $count++;
				
                $gtiagridtemp .= '{"id":"'.$query->fields[0].'", "id_user":"'.$query->fields[1].'", "ruta":"'.$cadenas->utf8($query->fields[2]).'", "ubicacion":"'.utf8_encode($query->fields[3]).'", "estado":"'.$query->fields[4].'"';

                if($count < $total) $gtiagridtemp .= '},'; else $gtiagridtemp .= '}';
                $query->MoveNext();
            }
	}
	
	// Se cierra el formato JSON
	$gtiagridtemp .= ']}';
	
	// Imprimir formato JSON
	echo $gtiagridtemp;
