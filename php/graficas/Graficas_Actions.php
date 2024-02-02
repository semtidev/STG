<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

include_once '../garantia/SdClass.php';
$charts = new SD();

// GRAFICAS

// Actualizar Meta SDDemora
if($_POST['accion'] == 'ActualizarMetaSDDemora'){
    
    $meta = $_POST['meta'];     
    echo $charts->ActualizarMetaSDDemora($meta);
}