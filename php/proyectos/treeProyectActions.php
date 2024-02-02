<?php

include_once("treeProyectClass.php");
$treeProyects = new treeProyects();

// Leer Arbol
if (!isset($_POST['accion'])) {

    echo $treeProyects->ReadProyects();
}

// Editar Elemento del Tree
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'updateTreeElement')) {

    $idElement = $_POST['idElement'];
    $nameElement = $cadenas->latin1($cadenas->codificarBD_utf8($_POST['nameElement']));

    echo $treeProyects->UpdateTreeElemento($idElement, $nameElement);
}

// Eliminar Elemento del Tree
if ((isset($_POST['accion'])) && ($_POST['accion'] == 'destroyTreeElement')) {

    //$params = json_decode(file_get_contents('php://input'));
    $params = $_POST['params'];
    echo $treeProyects->DestroyElement($params);
}

// Leer Zona
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'getZona')) {

    $objectId = $_POST['idObject'];
    echo $treeProyects->LoadZone($objectId);
}

// Nuevo Proyecto
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'NuevoProyecto')) {

    $polo = $_POST['polo'];
    $nombre = stripcslashes($cadenas->codificarBD_utf8($_POST['text']));
    $nombre_comercial = stripcslashes($cadenas->codificarBD_utf8($_POST['nombre_comercial']));
    $presupuesto = $_POST['presupuesto'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_terminacion = $_POST['fecha_terminacion'];
    $imagen = $_FILES['imagen'];
    $nombreImagen = stripcslashes($cadenas->codificarBD_utf8($imagen['name']));
    
    if ($_POST['activo'] == 'on'){ $activo = 1; }
    else{ $activo = 0; }

    echo $treeProyects->CreateProyect($nombre, $presupuesto, $imagen, $nombreImagen, $activo, $nombre_comercial, $polo, $fecha_inicio, $fecha_terminacion);
}

// Editar Proyecto
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'EditarProyecto')) {

    $id = $_POST['id'];
    $polo = $_POST['polo'];
    $nombre = stripcslashes($cadenas->codificarBD_utf8($_POST['text']));
    $nombre_comercial = stripcslashes($cadenas->codificarBD_utf8($_POST['nombre_comercial']));
    $presupuesto = $_POST['presupuesto'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_terminacion = $_POST['fecha_terminacion'];
    $imagen = $_FILES['imagen'];
    $nombreImagen = $cadenas->latin1($cadenas->codificarBD_utf8($imagen['name']));
    
    if ($_POST['activo'] == 'on'){ $activo = 1; }
    else{ $activo = 0; }

    echo $treeProyects->EditProyect($id, $nombre, $presupuesto, $imagen, $nombreImagen, $activo, $nombre_comercial, $polo, $fecha_inicio, $fecha_terminacion);
}

// Nueva Zona
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'NuevaZona')) {

    $id_parent = $_POST['id_parent'];
    $nombre = stripcslashes($cadenas->codificarBD_utf8($_POST['text']));
    $fecha_ini = $_POST['fecha_ini'];
    $fecha_fin = $_POST['fecha_fin'];

    echo $treeProyects->CreateZone($id_parent, $nombre, $fecha_ini, $fecha_fin);
}

// Editar Zona
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'EditarZona')) {

    $id = $_POST['id'];
    $nombre = stripcslashes($cadenas->codificarBD_utf8($_POST['text']));
    $fecha_ini = $_POST['fecha_ini'];
    $fecha_fin = $_POST['fecha_fin'];

    echo $treeProyects->EditZone($id, $nombre, $fecha_ini, $fecha_fin);
}

// Nuevo Objeto
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'NuevoObjeto')) {

    $zona = $_POST['idZona'];
    $nombre = stripcslashes($cadenas->codificarBD_utf8($_POST['nombreObjeto']));

    echo $treeProyects->NuevObjeto($zona, $nombre);
}

// Nueva Parte
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'NuevaParte')) {

    $objeto = $_POST['idObject'];
    $nombre = stripcslashes($cadenas->codificarBD_utf8($_POST['nombreParte']));

    echo $treeProyects->NuevaParte($objeto, $nombre);
}

// Insertar Nuevo Elemento desde el Grid
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'InsertarElemento')) {

    $idElement = $_POST['idElement'];
    $nombre = stripcslashes($cadenas->codificarBD_utf8($_POST['nombre']));

    echo $treeProyects->NuevoElemento($idElement, $nombre);
}

// Actualizar Elemento del Grid
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'updateGridElement')) {

    $idElement = $_POST['idElement'];
    $nameElement = stripcslashes($cadenas->codificarBD_utf8($_POST['nameElement']));
    echo $treeProyects->UpdateGridElement($idElement, $nameElement);
}

// Elimnar Elemento del Grid
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'destroyGridElement')) {

    $idElement = $_POST['idElement'];
    $nameElement = stripcslashes($cadenas->codificarBD_utf8($_POST['nameElement']));
    echo $treeProyects->DestroyGridElement($idElement, $nameElement);
}
?>