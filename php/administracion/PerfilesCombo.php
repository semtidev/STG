<?php

// Incluir la clase de conexion
include_once("../sistema/connect.php");
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once("../sistema/cadenas.php");
$cadenas = new Cadenas();

// Construir el JSON de SD
$response = '{"success": true, "perfilescombo": [';

if (isset($_GET['id_usuario'])) {

    $sql = "SELECT syst_perfiles.id, syst_perfiles.nombre FROM syst_perfiles,syst_usuarios_perfil WHERE (syst_usuarios_perfil.id_usuario = " . $_GET['id_usuario'] . ") AND (syst_perfiles.id != syst_usuarios_perfil.id_perfil)";
} else {

    $sql = "SELECT id,nombre FROM syst_perfiles";
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