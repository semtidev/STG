<?php

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

// Incluir los menajes del sistema
include_once '../sistema/message.php';

// Construir el JSON de SD
$response = '{"success": true, "polos": [';

if (isset($_GET['action']) && ($_GET['action'] = 'ProjectForm' || $_GET['action'] = 'UserForm')) {
    $polo = -1;
    if (isset($_SESSION['polo']) && $_SESSION['polo'] > 0 && $_SESSION['polo'] != 9) {
        $polo = intval($_SESSION['polo']);
    }

    if ($polo == -1) {
        $sql = "SELECT id, nombre, abbr FROM syst_polos";
    }
    else {
        $sql = "SELECT id, nombre, abbr FROM syst_polos WHERE id = $polo";
    }
}
else {
    $sql = "SELECT id, nombre, abbr FROM syst_polos";
}

// Ejecutar la consulta en la BD
$query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

if($query->RecordCount() > 0){
    
    $count = 0;
    while(!$query->EOF){
        $count++;
        if($count > 1){
            $response .= ',';
        }
        $response .= '{"id": "'.$query->fields[0].'", "nombre": "'.  utf8_encode($query->fields[1]).'", "abbr": "'.$query->fields[2].'"}';
        $query->MoveNext();
    }
}

$response .= ']}';

echo $response;
