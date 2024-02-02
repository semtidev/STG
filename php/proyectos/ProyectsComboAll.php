<?php
// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir la clase de conexion
include_once("../sistema/connect.php");
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once("../sistema/cadenas.php");
$cadenas = new Cadenas();

// Validar Polo del usuario
$polo = -1;
if (intval($_SESSION['polo']) != 9) {
    $polo = intval($_SESSION['polo']);
}

// Construir el JSON de SD
$response = '{"success": true, "proyectsall": [';

if ($polo == -1) {
    $sql = "SELECT id, nombre FROM gtia_proyectos WHERE activo = 1 ORDER BY nombre ASC";
}
else {
    $sql = "SELECT id, nombre FROM gtia_proyectos WHERE activo = 1 AND id_polo = ". $polo ." ORDER BY nombre ASC";
}

// Ejecutar la consulta en la BD
$query = $adoMSSQL_SEMTI->Execute($sql);

if ($query->RecordCount() > 0) {

    $count = 0;
    while (!$query->EOF) {
        $count++;
        if ($count == 1) {
            $response .= '{"id": "' . $query->fields[0] . '", "nombre": "' . $cadenas->utf8($query->fields[1]) . '"}';
        } else {
            $response .= ',{"id": "' . $query->fields[0] . '", "nombre": "' . $cadenas->utf8($query->fields[1]) . '"}';
        }
        $query->MoveNext();
    }
}

$response .= ']}';

echo $response;
 
?>