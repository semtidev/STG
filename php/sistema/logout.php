<?php
// Incluir la clase de conexion
include_once 'connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once 'cadenas.php';
$cadenas = new Cadenas();
 
// Incluir los mensajes del sistema
include_once 'message.php';

/////////////  FECHAS  ///////////////
$fecha_db = date("Y-n-j H:i:s");	


if((isset($_POST['accion'])) && ($_POST['accion'] == 'Logout')){	
	
	// le damos un mobre a la sesion (por si quisieramos identificarla)
	session_name('semtiGarantiaSession');
	
	// iniciamos sesiones
	session_start();
	
	// destruimos la session de usuarios.
	session_unset();
	session_destroy();
	
	echo json_encode(array(
		"success" => true
	));
	
}else{
	
	echo json_encode(array(
		"failure" => true,
		"message" => $message[2]
	));
}
