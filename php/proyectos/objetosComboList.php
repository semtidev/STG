<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

include_once '../informes/HfoClass.php';
$hfo = new Hfo();

if (isset($_GET['proyecto_zona'])) {

    $proyecto_zona = explode('.', $_GET['proyecto_zona']);
    $proyecto      = $cadenas->utf8($cadenas->codificarBD_utf8($proyecto_zona[0]));
    $zona          = $proyecto_zona[1];

    echo $hfo->hfoLoadObjetos($proyecto,$zona);
        
}
