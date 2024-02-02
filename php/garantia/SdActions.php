<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

include_once 'SdClass.php';
$sd = new SD();

// Leer Todas las SD
if ((!isset($_POST['accion'])) && (!isset($_GET['accion'])) && (!isset($_GET['listar'])) && (!isset($_GET['filtrar']))) {

    // Recibir parametros del Store para paginacion
    $page  = $_REQUEST['page'];
    $limit = $_REQUEST['limit'];
    $listar = 'Todos.Todos';

    echo $sd->ReadSD($limit, $page, $listar, '');
}

// Leer las SD filtradas
elseif ((isset($_GET['listar'])) && (!isset($_GET['filtrar']))) {

    // Recibir parametros del Store para paginacion
    $page   = $_REQUEST['page'];
    $limit  = $_REQUEST['limit'];
    $listar = $_GET['listar'];

    echo $sd->ReadSD($limit, $page, $listar, '');
}

// Leer las SD buscadas
elseif ((isset($_GET['filtrar'])) && (!isset($_GET['listar']))) {

    // Recibir parametros del Store para paginacion
    $page    = $_REQUEST['page'];
    $limit   = $_REQUEST['limit'];
    $filtrar = $_GET['filtrar'];

    echo $sd->ReadSD($limit, $page, 'Todos.Todos', $filtrar);
}

// Leer las SD buscadas y listadas
elseif ((isset($_GET['filtrar'])) && (isset($_GET['listar']))) {

    // Recibir parametros del Store para paginacion
    $page    = $_REQUEST['page'];
    $limit   = $_REQUEST['limit'];
    $filtrar = $_GET['filtrar'];
    $listar  = $_GET['listar'];

    echo $sd->ReadSD($limit, $page, $listar, $filtrar);
}

// Obtener el texto del campo de objeto en el Form de SD
elseif ($_POST['accion'] == 'GetTextTreepicker') {

    $element    = $_POST['element'];
    $id_element = $_POST['id_element'];

    echo $sd->GetTextTreepicker($element, $id_element);
}

// Agregar objeto/parte a la nueva SD
elseif ($_POST['accion'] == 'sdFormAddGrid') {

    $ruta      = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['ruta']));
    $ubicacion = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['ubicacion']));
    $estado    = $_POST['estado'];

    echo $sd->sdFormAddGrid($ruta, $ubicacion, $estado);
}

// Modificar objeto/parte a la nueva SD
elseif ($_POST['accion'] == 'updateSDFormGridElement') {

    $id_row    = $_POST['id_row'];
    $ubicacion = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['ubicacion']));
    $estado    = $_POST['estado'];

    echo $sd->sdFormUpdGrid($id_row, $ubicacion, $estado);
}

// Eliminar objeto/parte de la nueva SD
elseif ($_POST['accion'] == 'sdFormDelGrid') {

    $id_row = $_POST['id_row'];

    echo $sd->sdFormDelGrid($id_row);
}

// Insertar nueva SD
elseif ($_POST['accion'] == 'SdInsert') {

    $numero         = $_POST['numero'];
    $problema       = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['problema']));
    $proyecto       = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['proyecto']));
    $descripcion    = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['descripcion']));
    $objectArray    = $cadenas->codificarBD_utf8($_POST['objectArray']);
    $dpto           = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['dptoValue']));
    $fecha_reporte  = $_POST['fecha_reporte'];
    $estado         = $_POST['estado'];
    $compra         = $_POST['compra'];
    $fecha_solucion = $_POST['fecha_solucion'];
    $comentario     = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['comentario']));
    $document       = $_FILES['documento'];
    $nombreDocument = $document['name']; //$cadenas->utf8($cadenas->codificarBD_utf8($document['name']));
    $causa          = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['causa']));
    $fecha_almacen  = '';
    $costo          = $_POST['costo'];


    if (isset($_POST['constructiva']) && $_POST['constructiva'] == 'on'){ $constructiva = 'Si'; }
    else{ $constructiva = 'No'; }
    
    if (isset($_POST['suministro']) && $_POST['suministro'] == 'on') { 
        $suministro = 'Si';  
        $compra = $_POST['compra']; 
        $fecha_almacen = $_POST['fecha_almacen'];
    }
    else{ $suministro = 'No'; }
    
    if (isset($_POST['afecta_explotacion']) && $_POST['afecta_explotacion'] == 'on'){  $afecta_explotacion = 'Si'; }
    else{ $afecta_explotacion = 'No'; }
    
    echo $sd->SdInsert($numero, $problema, $proyecto, $descripcion, $objectArray, $dpto, $fecha_reporte, $fecha_solucion, $estado, $constructiva, $suministro, $compra, $afecta_explotacion, $comentario, $document, $nombreDocument, $causa, $fecha_almacen, $costo);
}

// Actualizar SD
elseif ($_POST['accion'] == 'SdUdate') {

    $id             = $_POST['id'];
    $numero         = $_POST['numero'];
    $problema       = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['problema']));
    $proyecto       = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['proyecto']));
    $descripcion    = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['descripcion']));
    $objectArray    = $cadenas->codificarBD_utf8($_POST['objectArray']);
    $dpto           = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['dptoValue']));
    $fecha_reporte  = $_POST['fecha_reporte'];
    $estado         = $_POST['estado'];
    $compra         = $_POST['compra'];
    $fecha_solucion = $_POST['fecha_solucion'];
    $comentario     = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['comentario']));
    $document       = $_FILES['documento'];
    $nombreDocument = $document['name']; //$cadenas->utf8($cadenas->codificarBD_utf8($document['name']));
    $causa          = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['causa']));
    $fecha_almacen  = '';
    $costo          = $_POST['costo'];

    if (isset($_POST['constructiva']) && $_POST['constructiva'] == 'on'){ $constructiva = 'Si'; }
    else{ $constructiva = 'No'; }
    
    if (isset($_POST['suministro']) && $_POST['suministro'] == 'on') { 
        $suministro = 'Si';  
        $compra = $_POST['compra'];
        $fecha_almacen = $_POST['fecha_almacen'];
    }
    else{ $suministro = 'No'; }
    
    if (isset($_POST['afecta_explotacion']) && $_POST['afecta_explotacion'] == 'on'){  $afecta_explotacion = 'Si'; }
    else{ $afecta_explotacion = 'No'; }

    echo $sd->SdUdate($id, $numero, $problema, $proyecto, $descripcion, $objectArray, $dpto, $fecha_reporte, $fecha_solucion, $estado, $constructiva, $suministro, $compra, $afecta_explotacion, $comentario, $document, $nombreDocument, $causa, $fecha_almacen, $costo);
}

// Eliminar SD
elseif ($_POST['accion'] == 'SdDelete') {

    $idSD = $_POST['idSD'];
    echo $sd->SdDelete($idSD);
}

// Eliminar SD Check
elseif ($_POST['accion'] == 'SdCheckDelete') {

    $parametros = $_POST['parametros'];
    echo $sd->SdCheckDelete($parametros);
}

// Cargar campos Form SD
elseif ($_POST['accion'] == 'SdFormLoad') {

    $id_sd = $_POST['id_sd'];
    echo $sd->SdFormLoad($id_sd);
}

// Cargar campos Filtros SD
elseif ($_POST['accion'] == 'SdFiltrosLoad') {

    echo $sd->SdFiltrosLoad();
}

// Cargar grid Objetos/Partes del Form SD
elseif ($_POST['accion'] == 'SdFormObjectLoad') {

    $id_sd = $_POST['id_sd'];
    echo $sd->SdFormObjectLoad($id_sd);
}

// Limpiar la tabla temporal de objetos/partes
elseif ($_POST['accion'] == 'SdCleanGridTemp') {

    echo $sd->SdCleanGridTemp();
}

// Cargar campo Objeto_Parte de Filtros SD
elseif ($_POST['accion'] == 'SdFiltrosObjectLoad') {

    echo $sd->SdFiltrosObjectLoad();
}

// Preguntar existencia de Filtros SD
elseif ($_POST['accion'] == 'SdExistFilters') {

    echo $sd->SdExistFilters();
}

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////

// GRAFICAS

// Actualizar Meta SDDemora
elseif($_POST['accion'] == 'ActualizarMetaSDDemora'){
    
    $meta = $_POST['meta'];     
    echo $sd->ActualizarMetaSDDemora($meta);
}