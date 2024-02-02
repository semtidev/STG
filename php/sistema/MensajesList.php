<?php
// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

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

//////////////////////////////////////////////////////////////

$sql  = "SELECT id, descripcion, tipo FROM syst_msg WHERE activo = 1";

if((isset($_GET['listar'])) && ($_GET['listar'] == 'urgente')) {

	$sql .= " AND tipo = 'urgente'";
}
elseif((isset($_GET['listar'])) && ($_GET['listar'] == 'alerta')) {

	$sql  .= " AND tipo = 'alerta'";
}
elseif((isset($_GET['listar'])) && ($_GET['listar'] == 'info')) {

	$sql  .= " AND tipo = 'info'";
}

$sql  .= " ORDER BY descripcion ASC";

// Construir el JSON de SD
$response = '{"success": true, "mensajes": [';

// Ejecutar el SP en la BD
$query = $adoMSSQL_SEMTI->Execute($sql);

if ($query->RecordCount() > 0) {

	$count = 0;
	while (!$query->EOF) {
		
		$count++;
		if ($count > 1) { $response .= ','; }
		$response .= '{"id": "' . $query->fields[0] . '",
					   "descripcion": "' . $GLOBALS['cadenas']->utf8($query->fields[1]) . '",
					   "tipo": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '"
					  }';
					  
		$query->MoveNext();
	}
}

$response .= ']}';

echo $response;
 
