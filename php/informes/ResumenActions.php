<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

include_once 'ResumenClass.php';
$resumen = new Resumen();

// Leer las HFO
if ((!isset($_POST['accion'])) && (!isset($_GET['accion'])) && (!isset($_GET['loadZonas'])) && (!isset($_GET['loadResumenDataEstados'])) && (!isset($_GET['loadResumenDataSdPendientes'])) && (!isset($_GET['loadResumenDataHFO'])) && (!isset($_GET['loadResumenDataPIndicadores'])) && (!isset($_GET['loadResumenDataRepetitividad'])) && (!isset($_GET['loadResumenDataComportamHFO']))) {

    echo $resumen->ReadResumen();
}

// Leer zonas de un proyecto
if ($_GET['loadZonas'] != '') {

    $proyecto = $cadenas->utf8($cadenas->codificarBD_utf8($_GET['loadZonas']));
    echo $resumen->hfoLoadZonas($proyecto);
}

// Leer los datos del estado de las SD
if ($_GET['loadResumenDataEstados'] != '') {

    $informe = $_GET['loadResumenDataEstados'];
    echo $resumen->loadResumenDataEstados($informe);
}

// Leer los datos de las SD Pendientes
if ($_GET['loadResumenDataSdPendientes'] != '') {

    $informe = $_GET['loadResumenDataSdPendientes'];
    echo $resumen->loadResumenDataSDPendientes($informe);
}

// Leer los datos de los Indicadores Principales
if ($_GET['loadResumenDataPIndicadores'] != '') {

    $informe = $_GET['loadResumenDataPIndicadores'];
    echo $resumen->loadResumenDataPIndicadores($informe);
}

// Leer los datos de la Repetitividad de los Problemas
if ($_GET['loadResumenDataRepetitividad'] != '') {

    $informe = $_GET['loadResumenDataRepetitividad'];
    echo $resumen->loadResumenDataRepetitividad($informe);
}

// Leer los datos de las HFO
if ($_GET['loadResumenDataHFO'] != '') {

    $informe = $_GET['loadResumenDataHFO'];
    echo $resumen->loadResumenDataHFO($informe);
}

// Leer los datos del Comportamiento de HFO
if ($_GET['loadResumenDataComportamHFO'] != '') {

    $informe = $_GET['loadResumenDataComportamHFO'];
    echo $resumen->loadResumenDataComportamHFO($informe);
}

// Insertar nuevo informe Codir
if ($_POST['accion'] == 'ResumenInsert') {

    $titulo   = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['titulo']));
    $proyecto = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['proyecto']));
    $zona     = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['zonaValue']));
    $desde    = $_POST['desde'];
    $hasta    = $_POST['hasta'];
    
    echo $resumen->ResumenInsert($titulo, $proyecto, $zona, $desde, $hasta);
}

// Cargar campos Form 
if ($_POST['accion'] == 'LoadInforme') {

    $id = $_POST['id'];
    echo $resumen->ResumenFormLoad($id);
}

// Validar Seccion 
if ($_POST['accion'] == 'ResumenSectionValidate') {

    $id = $_POST['id'];
    echo $resumen->ResumenSectionValidate($id);
}

// Limpiar Secciones validadas 
if ($_POST['accion'] == 'ResumenSectionValidateClean') {

    echo $resumen->ResumenSectionValidateClean();
}

// Cargar los parametros de validacion de los informes
if ($_POST['accion'] == 'LoadResumenValidate') {

    echo $resumen->LoadResumenValidate();
}

// Modificar informe Codir
if ($_POST['accion'] == 'ResumenUdate') {

    $id       = $_POST['id'];
    $titulo   = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['titulo']));
    $proyecto = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['proyecto']));
    $zona     = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['zonaValue']));
    $desde    = $_POST['desde'];
    $hasta    = $_POST['hasta'];
    
    echo $resumen->ResumenUpdate($id, $titulo, $proyecto, $zona, $desde, $hasta);
}

// Eliminar Informe Codir
if ($_POST['accion'] == 'ResumenDelete') {

    $id = $_POST['id'];
    echo $resumen->ResumenDelete($id);
}

// Comentario de los datos del informe SD PENDIENTES
elseif ($_POST['accion'] == 'ResumendataSDPendientesGridComent') {

    $id_row     = $_POST['id_row'];
    $comentario = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['comentario']));
    echo $resumen->resumendataSDPendientesGridComent($id_row,$comentario);
}

// Comentario de los datos del informe Principales Indicadores
elseif ($_POST['accion'] == 'ResumendataPIndicadoresGridComent') {

    $id_row     = $_POST['id_row'];
    $comentario = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['comentario']));
    echo $resumen->ResumendataPIndicadoresGridComent($id_row,$comentario);
}

// Comentario de los datos del informe Probemas + Repetitivos
elseif ($_POST['accion'] == 'ResumendataRepetitividadGridComent') {

    $id_row     = $_POST['id_row'];
    $comentario = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['comentario']));
    echo $resumen->ResumendataRepetitividadGridComent($id_row,$comentario);
}

// Comentario de los datos del informe HFO
elseif ($_POST['accion'] == 'ResumendataHfoGridComent') {

    $id_row     = $_POST['id_row'];
    $comentario = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['comentario']));
    echo $resumen->ResumendataHfoGridComent($id_row,$comentario);
}

// Codir Comentario Inicial
if ($_POST['accion'] == 'ResumenComentInicial') {

    $id_resumen   = $_POST['id_resumen'];
    $comentario = preg_replace('/[\n|\r|\n\r]/i','\x0A',$cadenas->latin1($cadenas->codificarBD_utf8($_POST['comentario_inicial'])));  
    echo $resumen->resumendataComentInicial($id_resumen,$comentario);
}

// Codir Comentario Final
if ($_POST['accion'] == 'ResumenComentFinal') {

    $id_resumen   = $_POST['id_resumen'];
    $comentario = preg_replace('/[\n|\r|\n\r]/i','\x0A',$cadenas->utf8($_POST['comentario_final']));
    echo $resumen->resumendataComentFinal($id_resumen,$comentario);
}
