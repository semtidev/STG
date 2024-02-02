<?php

include_once 'PerfilesClass.php';
$perfiles = new Perfiles();

// Leer Todos los Perfiles
if (!isset($_POST['accion'])) {

    // Recibir parametros del Store para paginacion
    $page  = $_REQUEST['page'];
    $limit = $_REQUEST['limit'];

    $list_perfil = $perfiles->ReadPerfiles($limit, $page);

    echo $list_perfil;
}

// Insertar Perfil
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'Insertar')) {

    $nombre = $cadenas->codificarBD_utf8($_POST['nombre']);
    $descripcion = $cadenas->codificarBD_utf8($_POST['descripcion']);
    $permisos = $_POST['permisos'];

    $nuevo_perfil = $perfiles->CreatePerfil($nombre, $descripcion, $permisos);

    echo $nuevo_perfil;
}

// Modificar Perfil
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'Actualizar')) {

    $id_perfil = $_POST['id'];
    $nombre = $cadenas->codificarBD_utf8($_POST['nombre']);
    $descripcion = $cadenas->codificarBD_utf8($_POST['descripcion']);
    $permisos = $_POST['permisos'];

    $update_perfil = $perfiles->UpdatePerfil($id_perfil, $nombre, $descripcion, $permisos);

    echo $update_perfil;
}

// Eliminar Perfil
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'Eliminar')) {

    $id_perfil = $_POST['id_perfil'];

    $delete_perfil = $perfiles->DeletePerfil($id_perfil);

    echo $delete_perfil;
}
