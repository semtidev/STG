<?php

include_once 'UsuariosClass.php';
$usuarios = new Usuarios();

// Listar Usuarios
if (!isset($_POST['accion'])) {

    // Recibir parametros del Store para paginacion
    $page  = $_REQUEST['page'];
    $limit = $_REQUEST['limit'];
    $polo  = 0;
    if (isset($_GET['polo'])) {
        $polo = $_GET['polo'];
    }

    $list_users = $usuarios->ReadUsuarios($limit, $page, $polo);

    echo $list_users;
}

// Insertar Usuario
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'Insertar')) {
    
    $polo      = intval($_POST['polo']);
    $nombre    = $cadenas->codificarBD_utf8($_POST['nombre']);
    $apellidos = $cadenas->codificarBD_utf8($_POST['apellidos']);
    $cargo     = $cadenas->codificarBD_utf8($_POST['cargo']);
    $usuario   = $_POST['usuario'];
    if($_POST['password'] != '') { 
        $password  = md5($_POST['password']);
        $password2 = md5($_POST['password2']);
    } else { 
        $password = '';
        $password2 = '';
    }
    $email     = $_POST['email'];
    $expira    = $_POST['expira'];
    $portada   = $_POST['portada'];
    $avatar    = $_FILES['newavatar'];
    $typeImage = $avatar['type'];
    $sizeImage = $avatar['size'];
    $nameImage = $avatar['name'];
    $permisos  = $_POST['permisos'];
    
    if ($_POST['activo'] == 'on'){ $activo = 'Si'; }
    else{ $activo = 'No'; }
    
    if(strstr($_POST['perfiles'],',')){ $perfiles  = explode(',',$_POST['perfiles']); }
    else{ $perfiles  = $_POST['perfiles']; }

    if ($_POST['notificaciones'] == 'on'){ $notificaciones = 'Si'; }
    else{ $notificaciones = 'No'; }

    $nuevo_usuario = $usuarios->CreateUsuario($polo, $nombre, $apellidos, $cargo, $usuario, $password, $password2, $activo, $perfiles, $email, $expira, $avatar, $typeImage, $nameImage, $sizeImage, $permisos, $notificaciones, $portada);

    echo $nuevo_usuario;
}

// Modificar Usuario
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'Actualizar')) {

    $id        = $_POST['id_usuario'];
    $nombre    = $cadenas->codificarBD_utf8($_POST['nombre']);
    $apellidos = $cadenas->codificarBD_utf8($_POST['apellidos']);
    $cargo     = $cadenas->codificarBD_utf8($_POST['cargo']);
    $usuario   = $_POST['usuario'];
    if($_POST['password'] != '') { $password  = md5($_POST['password']); }else{ $password = ''; }
    $email     = $_POST['email'];
    $expira    = $_POST['expira'];
    $portada   = $_POST['portada'];
    $avatar    = $_POST['avatar'];
    $newavatar = $_FILES['newavatar'];
    $typeImage = $newavatar['type'];
    $sizeImage = $newavatar['size'];
    $nameImage = $newavatar['name'];
    $permisos  = $_POST['permisos'];
    
    if ($_POST['activo'] == 'on'){ $activo = 'Si'; }
    else{ $activo = 'No'; }
    
    if(strstr($_POST['perfiles'],',')){ $perfiles  = explode(',',$_POST['perfiles']); }
    else{ $perfiles  = $_POST['perfiles']; }

    if ($_POST['notificaciones'] == 'on'){ $notificaciones = 'Si'; }
    else{ $notificaciones = 'No'; }

    $update_usuario = $usuarios->UpdateUsuario($id, $nombre, $apellidos, $cargo, $usuario, $password, $activo, $perfiles, $email, $expira, $newavatar, $typeImage, $nameImage, $sizeImage, $permisos, $notificaciones, $avatar, $portada);

    echo $update_usuario;
}

// Eliminar Usuario
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'Eliminar')) {

    $id = $_POST['id_usuario'];

    $delete_usuario = $usuarios->DeleteUsuario($id);

    echo $delete_usuario;
}

// Cargar Usuario
elseif ((isset($_POST['accion'])) && ($_POST['accion'] == 'LoadUsuario')) {

    $id = $_POST['id'];

    $load_usuario = $usuarios->LoadUsuario($id);

    echo $load_usuario;
}
