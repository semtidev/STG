<?php

// Incluir la clase de conexion
include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

if (isset($_GET['proyectId'])) {

    $proyectId = $_GET['proyectId'];
    $array_proyectId = explode('.', $proyectId);

    if (count($array_proyectId) == 2) {

        $id_proyect = $array_proyectId[1];
        $sql = "SELECT id,nombre FROM gtia_zonas WHERE id_proyecto = $id_proyect ORDER BY nombre ASC";
    } elseif (count($array_proyectId) == 3) {

        $id_zone = $array_proyectId[2];
        $sql = "SELECT id,nombre FROM gtia_objetos WHERE id_zona = $id_zone ORDER BY nombre ASC";
    } elseif (count($array_proyectId) == 4) {

        $id_object = $array_proyectId[3];
        $sql = "SELECT id,nombre FROM gtia_partes WHERE id_objeto = $id_object ORDER BY nombre ASC";
    }
} else {

    $sql = "SELECT id,nombre FROM gtia_proyectos ORDER BY nombre ASC";
}

// Ejecutar la consulta en la BD
$query = $adoMSSQL_SEMTI->Execute($sql);

$response = '{"success": true, "proyect": [';

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

$response .= ']}';

//encoda para formato JSON
echo $response;
