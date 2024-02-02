<?php

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

// Incluir los menajes del sistema
include_once '../sistema/message.php';

////////////////////////////////////////
//////      CLASE USUARIOS       ///////
////////////////////////////////////////

class Usuarios {

    /////////////////////////////////////////
    //////////////  Atributos  //////////////
    /////////////////////////////////////////
    
    
    /////////////////////////////////////////
    ///////////  Implementacion  ////////////
    /////////////////////////////////////////
    // Listar usuarios
    function ReadUsuarios($limit, $page, $polo) {

        // Recoger el total de registros del SP
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute('DECLARE @CTDAD INT; EXEC @CTDAD = Syst_usuarios_Ctdad 2; SELECT @CTDAD');
        $total = trim($query);  // Elimina los espacios en blanco a la isquierda y derecha de la cadena

        // Construir el JSON de SD
        $response = '{"success": true, "total": "' . $total . '", "usuarios": [';

        // Ejecutar el SP en la BD
        $sql = "Syst_usuarios_Paging $limit,$page,$polo";        
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        if ($query->RecordCount() > 0) {

            $count = 0;
            while (!$query->EOF) {
                
                $count++;
                if ($count > 1) { $response .= ','; }
                $response .= '{"id_usuario": "' . $query->fields[0] . '",
                               "nombre": "' . $GLOBALS['cadenas']->utf8($query->fields[1]) . '",
                               "apellidos": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '",
                               "cargo": "' . str_replace('"', "'", $GLOBALS['cadenas']->utf8($query->fields[3]))  . '",
                               "usuario": "' . $query->fields[4] . '",
                               "activo": "' . $query->fields[5] . '",
                               "email": "' . $query->fields[6] . '"
                              }';
                              
                $query->MoveNext();
            }
        }

        $response .= ']}';

        return $response;
    }

    ////////////////////////////////////////////
    // Crear Usuario
    function CreateUsuario($polo, $nombre, $apellidos, $cargo, $usuario, $password, $password2, $activo, $perfiles, $email, $expira, $avatar, $typeImage, $nameImage, $sizeImage, $permisos, $notificaciones, $portada) {

        $nameAvatar = 'nophoto.png';

        // Validar usuario
        $qry_valida = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id_usuario FROM syst_usuarios WHERE usuario = '$usuario'");
        
        if ($qry_valida->RecordCount() > 0) {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][14]  
            ));
            return $response;
        }
        else{
            
            // Validar Avatar
            if ($nameImage != '') {
            
                if (strstr($typeImage, 'image') == false) {
                    $response = json_encode(array(
                        "failure" => true,
                        "message" => $GLOBALS["message"][10]
                    ));
                    return $response;
                }
                elseif(strstr($typeImage, 'image') == true && $sizeImage > 250000){
                    $response = json_encode(array(
                        "failure" => true,
                        "message" => $GLOBALS["message"][11]
                    ));
                    return $response;
                }
                else{
                    
                    // Subir la imagen al servidor
                    $nameArray  = explode('.', $nameImage);
                    $nameCount  = count($nameArray);
                    $extencion  = $nameArray[$nameCount - 1];
                    $nameAvatar = $usuario.'.'.$extencion;
                    copy($avatar['tmp_name'], '../../resources/images/users/' . $nameImage);
                    rename('../../resources/images/users/'.$nameImage, '../../resources/images/users/'.$nameAvatar);

                }
            }

            // consulta sql (Transacciones Inteligentes).
            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            $sql_user = "INSERT INTO syst_usuarios
                            (
                                nombre,
                                apellidos,
                                cargo,
                                usuario,
                                contrasena,
                                activo,
                                email,
                                expira,
                                portada,
                                avatar,
                                notificaciones,
                                id_polo
                            )
                        VALUES
                            (
                                '$nombre',
                                '$apellidos',
                                '$cargo',
                                '$usuario',
                                '$password',
                                '$activo',
                                '$email',
                                '$expira',
                                '$portada',
                                '$nameAvatar',
                                '$notificaciones',
                                $polo
                            )";
            $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_user); 
            
            $qry_iduser = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id_usuario FROM syst_usuarios WHERE usuario = '$usuario'");
            $id_user    = $qry_iduser->fields[0];
            
            // Insertar Roles del usuario
            if (!is_array($perfiles)) {

                $GLOBALS["adoMSSQL_SEMTI"]->Execute('Syst_usuariosperfil_Insert '.$id_user.','.$perfiles);
            } else {

                for ($i = 0; $i < count($perfiles); $i++) {
                    $GLOBALS["adoMSSQL_SEMTI"]->Execute('Syst_usuariosperfil_Insert '.$id_user.','.$perfiles[$i]);
                }
            }

            // Insertar permisos a proyectos del usuario
            $records = json_decode(stripslashes($permisos));

            foreach ($records as $record) {

                $id_proyecto = substr($record->id, 1);

                if ($record->modificar == true)         $modificar         = 1; else $modificar         = 0;
                if ($record->lectura_exportar == true) $lectura_exportar = 1; else $lectura_exportar = 0;
                if ($record->lectura == true)          $lectura          = 1; else $lectura          = 0;
                if ($record->escritura == true)        $escritura        = 1; else $escritura        = 0;

                $sql = "INSERT INTO syst_usuarios_proyectos(id_usuario,id_proyecto,lectura,lectura_exportar,escritura,modificar) VALUES($id_user,$id_proyecto,$lectura,$lectura_exportar,$escritura,$modificar)";

                $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
            }

            if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {
                                
                $response = json_encode(array(
                    "success" => true
                ));
            } else {

                $response = json_encode(array(
                    "failure" => true,
                    "message" => $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()
                ));
            }

            $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
            $GLOBALS["adoMSSQL_SEMTI"]->Close();
            
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Modificar Usuario
    function UpdateUsuario($id, $nombre, $apellidos, $cargo, $usuario, $password, $activo, $perfiles, $email, $expira, $newavatar, $typeImage, $nameImage, $sizeImage, $permisos, $notificaciones, $avatar, $portada) {

        $sql_user = "UPDATE syst_usuarios SET nombre = '$nombre',apellidos = '$apellidos',cargo = '$cargo',activo = '$activo',email = '$email',expira = '$expira',portada = '$portada',notificaciones = '$notificaciones'";
         
        if($password != ''){

            $sql_user .= ",contrasena = '$password'";
        }

        // Validar Avatar
        if ($nameImage != '') {
        
            if (strstr($typeImage, 'image') == false) {
                $response = json_encode(array(
                    "failure" => true,
                    "message" => $GLOBALS["message"][10]
                ));
                return $response;
            }
            elseif(strstr($typeImage, 'image') == true && $sizeImage > 250000){
                $response = json_encode(array(
                    "failure" => true,
                    "message" => $GLOBALS["message"][11]
                ));
                return $response;
            }
            else{
                
                // Eliminar el viejo avatar
                if($avatar != 'nophoto.png' && file_exists('../../resources/images/users/'.$avatar)){
                    unlink('../../resources/images/users/'.$avatar);
                }

                // Subir la imagen al servidor
                $nameArray  = explode('.', $nameImage);
                $nameCount  = count($nameArray);
                $extencion  = $nameArray[$nameCount - 1];
                $nameAvatar = $usuario.'.'.$extencion;
                copy($newavatar['tmp_name'], '../../resources/images/users/' . $nameImage);
                rename('../../resources/images/users/'.$nameImage, '../../resources/images/users/'.$nameAvatar);

                $sql_user .= ",avatar = '$nameAvatar'";
            }
        }

        $sql_user .= " WHERE id_usuario = $id";

        // consulta sql (Transacciones Inteligentes).
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_user); 
               
        // Eliminar roles del usuario
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM syst_usuarios_perfil WHERE id_usuario = $id");

        // Insertar Roles del usuario
        if (!is_array($perfiles)) {

            $GLOBALS["adoMSSQL_SEMTI"]->Execute('Syst_usuariosperfil_Insert '.$id.','.$perfiles);
        } else {

            for ($i = 0; $i < count($perfiles); $i++) {

                $GLOBALS["adoMSSQL_SEMTI"]->Execute('Syst_usuariosperfil_Insert '.$id.','.$perfiles[$i]);
            }
        }

        // Eliminar permisos a proyectos del usuario
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM syst_usuarios_proyectos WHERE id_usuario = $id");

        // Insertar permisos a proyectos del usuario
        $records = json_decode(stripslashes($permisos));

        foreach ($records as $record) {

            $id_proyecto = substr($record->id, 1);

            if ($record->modificar == true)         $modificar         = 1; else $modificar         = 0;
            if ($record->lectura_exportar == true) $lectura_exportar = 1; else $lectura_exportar = 0;
            if ($record->lectura == true)          $lectura          = 1; else $lectura          = 0;
            if ($record->escritura == true)        $escritura        = 1; else $escritura        = 0;

            $sql = "INSERT INTO syst_usuarios_proyectos(id_usuario,id_proyecto,lectura,lectura_exportar,escritura,modificar) VALUES($id,$id_proyecto,$lectura,$lectura_exportar,$escritura,$modificar)";

            $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
        }

        if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {
                            
            $response = json_encode(array(
                "success" => true
            ));
        } else {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()
            ));
        }

        $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
        $GLOBALS["adoMSSQL_SEMTI"]->Close();
        
        return $response;
    }

    ////////////////////////////////////////////
    // Eliminar Usuario
    function DeleteUsuario($id) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute('Syst_usuarios_Delete '.$id);

        if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {

            $response = json_encode(array(
                "success" => true
            ));
        } else {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][2]
            ));
        }

        $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
        $GLOBALS["adoMSSQL_SEMTI"]->Close();

        return $response;
    }

    ////////////////////////////////////////////
    // Cargar Usuario
    function LoadUsuario($id) {

        // Recoger datos del usuario
        $sql = 'Syst_usuarios_Load '.$id;
        $usuario = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        // Recoger perfiles
        $perfiles = $GLOBALS["adoMSSQL_SEMTI"]->Execute('Syst_usuariosperfil_Select '. $usuario->fields[0]);

        $array_perfiles = '';

        while (!$perfiles->EOF) {

            $array_perfiles = $array_perfiles . "'" . $perfiles->fields[0] . "',";
            $perfiles->MoveNext();
        }

        $array_perfiles = substr($array_perfiles, 0, -1);

        // Asignar valores a los campos del form
        if ($usuario->fields[5] == 'Si'){ $activo = 1; }
        else{ $activo = 0; }
        if ($usuario->fields[10] == 'Si'){ $notificaciones = 1; }
        else{ $notificaciones = 0; }
        if ($usuario->fields[7] == '1900-01-01' || $usuario->fields[7] == null){ $expira = null; }
        else{ $expira = $usuario->fields[7]; }
        $response = '{"success": true,
                      "data": {
                                "id_usuario":"' . $usuario->fields[0] . '",
                                "nombre":"' . $GLOBALS["cadenas"]->utf8($usuario->fields[1]) . '",
                                "apellidos":"' . $GLOBALS["cadenas"]->utf8($usuario->fields[2]) . '",
                                "usuario":"' . $GLOBALS["cadenas"]->utf8($usuario->fields[4]) . '",
                                "cargo":"' . str_replace('"', "'", $GLOBALS["cadenas"]->utf8($usuario->fields[3])) . '",
                                "activo":"' . $activo . '",
                                "perfiles":[' . $array_perfiles . '],
                                "email":"' . $usuario->fields[6] . '",
                                "expira":"' . $expira . '",
                                "portada":"' . $usuario->fields[8] . '",
                                "avatar":"' . $usuario->fields[9] . '",
                                "polo":"' . $usuario->fields[11] . '",
                                "notificaciones":"' . $notificaciones . '"
                            }
                     }';

        return $response;
    }

    ////////////////////////////////////////////
    ////////////////////////////////////////////
    ///////////  Getters && Setters  ///////////
    ////////////////////////////////////////////
}

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
