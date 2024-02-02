<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

include_once '../informes/HfoClass.php';
$hfo = new Hfo();

if (isset($_GET['proyecto'])) {

    $proyecto = $cadenas->utf8($cadenas->codificarBD_utf8($_GET['proyecto']));

    echo $hfo->hfoLoadZonas($proyecto);
        
}
