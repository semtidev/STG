<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

include_once 'HfoClass.php';
$hfo = new Hfo();

// Leer las HFO
if ((!isset($_POST['accion'])) && (!isset($_GET['accion'])) && (!isset($_GET['loadZonas'])) && (!isset($_GET['loadInfoData']))) {

    echo $hfo->ReadHfo();
}

// Leer zonas de un proyecto
elseif ($_GET['loadZonas'] != '') {

    $proyecto = $cadenas->utf8($cadenas->codificarBD_utf8($_GET['loadZonas']));

    echo $hfo->hfoLoadZonas($proyecto);
}

// Leer los datos de un Informe
elseif ($_GET['loadInfoData'] != '') {

    $informe = $_GET['loadInfoData'];

    echo $hfo->loadInfoData($informe);
}

// Insertar nueva HFO
elseif ($_POST['accion'] == 'HfoInsert') {

    $titulo   = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['titulo']));
    $proyecto = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['proyecto']));
    $zona     = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['zonaValue']));
    $objeto   = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['objetoValue']));
    $desde    = $_POST['desde'];
    $hasta    = $_POST['hasta'];
    
    echo $hfo->HfoInsert($titulo, $proyecto, $zona, $objeto, $desde, $hasta);
}

// Cargar campos Form Hfo
elseif ($_POST['accion'] == 'LoadInforme') {

    $id = $_POST['id'];
    echo $hfo->HfoFormLoad($id);
}

elseif ($_POST['accion'] == 'HfoUdate') {

    $id       = $_POST['id'];
    $titulo   = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['titulo']));
    $proyecto = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['proyecto']));
    $zona     = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['zonaValue']));
    $objeto   = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['objetoValue']));
    $desde    = $_POST['desde'];
    $hasta    = $_POST['hasta'];
    
    echo $hfo->HfoUpdate($id, $titulo, $proyecto, $zona, $objeto, $desde, $hasta);
}

// Eliminar Informe HFO
elseif ($_POST['accion'] == 'HfoDelete') {

    $id = $_POST['id'];
    echo $hfo->HfoDelete($id);
}

// Eliminar Informes HFO Check
elseif ($_POST['accion'] == 'HfoCheckDelete') {

    $parametros = $_POST['parametros'];
    echo $hfo->HfoCheckDelete($parametros);
}

// Comentario de los datos del informe
elseif ($_POST['accion'] == 'hfodataComent') {

    $id_row     = $_POST['id_row'];
    $comentario = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['observaciones']));
    echo $hfo->hfodataComent($id_row,$comentario);
}
