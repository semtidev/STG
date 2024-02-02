<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

include_once 'SdClass.php';
$sd = new SD();
	
// Recibir parametros del Store para paginacion
$page  = $_REQUEST['page'];
$limit = $_REQUEST['limit'];

if(!isset($_GET['listar'])){
    echo $sd->ReadSDPendientes($limit,$page,'');
}
else{
    $listar = $_GET['listar'];
    echo $sd->ReadSDPendientes($limit,$page,$listar);
}