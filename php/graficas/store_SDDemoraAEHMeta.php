<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir script de conexion a BD
include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

// Incluir los menajes del sistema
include_once '../sistema/message.php';

$qry_meta = $adoMSSQL_SEMTI->Execute("SELECT meta FROM gtia_indicadores WHERE nombre = 'Demora Promedio en SD AEH'");

$meta     = $qry_meta->fields[0];
$response = '{ sddemoraeh: [{"meta": "'.$meta.'"}] }';
echo $response;