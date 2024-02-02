<?php

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

include_once 'connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once 'cadenas.php';
$cadenas = new Cadenas();

// Incluir los menajes del sistema
include_once 'message.php';

//////////////////////////////////////
//////      CLASE SYSTEM       ///////
//////////////////////////////////////

class System {

    /////////////////////////////////////////
    //////////////  Atributos  //////////////
    /////////////////////////////////////////
    
    
    /////////////////////////////////////////
    ///////////  Implementacion  ////////////
    /////////////////////////////////////////
    
    // Cargar datos del usuario logeado
    function LoadCurrentUser() {

        $id_user = $_SESSION['idsession'];
        $sql_login = "SELECT
                        nombre,
                        apellidos,
                        cargo,
                        usuario,
                        portada,
                        avatar,
                        email,
                        id_polo
                      FROM
                        syst_usuarios
                      WHERE
                        id_usuario = '$id_user'";
        $query_login = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_login);

        // Construir el JSON de SD
        $response = '{"success": true, "currentuser": [
                        {
                            "id": "'.$_SESSION['idsession'].'",
                            "nombre": "' . $GLOBALS['cadenas']->utf8($query_login->fields[0]) . '",
                            "apellidos": "' . $GLOBALS['cadenas']->utf8($query_login->fields[1]) . '",
                            "cargo": "' . str_replace('"', "'", $GLOBALS['cadenas']->utf8($query_login->fields[2])) . '",
                            "usuario": "' . $query_login->fields[3] . '",
                            "portada": "' . $query_login->fields[4] . '",
                            "avatar": "' . $query_login->fields[5] . '",
                            "email": "' . $query_login->fields[6] . '",
                            "polo": "' . $query_login->fields[7] . '"
                        }
                     ]}';

        return $response;
    }

    // Cargar Perfil de Usuario
    function LoadUserPerfil($id_user){

        $usuario = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT notificaciones FROM syst_usuarios WHERE id_usuario = $id_user");

        if ($usuario->fields[0] == 'Si'){ $notificaciones = 1; }
        else{ $notificaciones = 0; }
        $response = '{"success": true,
                      "data": {"notificaciones":"' . $notificaciones . '"}
                     }';

        return $response;
    }

    // Actualizar Perfil de Usuario
    function UpdateCurrentUser($image,$typeImage,$nameImage,$sizeImage,$notificaciones){

        $id_user = $_SESSION['idsession'];

        // consulta sql (Transacciones Inteligentes).
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        if ($nameImage != '') {
            
            // ACTUALIZAR AVATAR

            if (strstr($typeImage, 'image') == false) {
                $response = json_encode(array(
                    "failure" => true,
                    "message" => '"'.$GLOBALS["message"][10].'"'
                ));
            }
            elseif(strstr($typeImage, 'image') == true && $sizeImage > 150000){
                $response = json_encode(array(
                    "failure" => true,
                    "message" => '"'.$GLOBALS["message"][11].'"'
                ));
            }
            else{
                
                // Subir la imagen al servidor
                $nameArray  = explode('.', $nameImage);
                $extencion  = $nameArray[1];
                $nameAvatar = $_SESSION['usuario'].'.'.$extencion;
                copy($image['tmp_name'], '../../resources/images/users/' . $nameImage);
                rename('../../resources/images/users/'.$nameImage, '../../resources/images/users/'.$nameAvatar);
                
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE syst_usuarios SET avatar = '$nameAvatar' WHERE id_usuario = $id_user");
                    
                if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {
                    $response = json_encode(array(
                        "success" => true,
                        "avatar" => '"'.$nameAvatar.'"'
                    ));
                }
                else{
                    $response = json_encode(array(
                        "failure" => true,
                        "message" => $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()
                    ));
                }
                
            }
        }
        else{

            // ACTUALIZAR NOTIFICACIONES

            $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE syst_usuarios SET notificaciones = '$notificaciones' WHERE id_usuario = $id_user");

            if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {
                    $response = json_encode(array(
                    "success" => true,
                    "avatar" => ""
                ));
            }
            else{
                $response = json_encode(array(
                    "failure" => true,
                    "message" => $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()
                ));
            }
        }

        $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
        $GLOBALS["adoMSSQL_SEMTI"]->Close();

        return $response;
    }
    
    // Cargar Portada del usuario
    function LoadPortada() {
        
        $id_user = $_SESSION['idusuario'];
        // Recoger datos del usuario
        $sql     = 'Syst_portada_Load '.$id_user;
        $portada = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
        if($portada->fields[0] == 'Controlpanel'){
            $presentacion = false;
            $controlpanel = true;
        }
        else{
            $presentacion = true;
            $controlpanel = false;
        }
        $response = '{"success": true,
                      "data": {"presentacion":"' . $presentacion . '",
                               "panelcontrol":"' . $controlpanel . '"}
                     }';

        return $response;
    }
    
    // Actualizar Portada del usuario
    function UpdatePortada($portada) {
        
        $id_user = $_SESSION['idusuario'];
        $sql     = 'Syst_portada_Update '.$id_user.','.$portada;
        $qry     = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
        
        if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {

            $page = 'index.html';
            if($portada == 'Controlpanel'){
                $page = 'controlpanel/index.php';
            }
            $_SESSION['portada'] = $page;
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
    
    // Cargar Configuraciones
    function LoadConfig() {

        // Recoger datos del usuario
        $sql   = 'Syst_parametros_Load';
        $param = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        $response = '{"success": true,
                      "data": {"ipserver":"' . $param->fields[1] . '"}
                     }';

        return $response;
    }    
    
    // Actualizar Configuraciones
    function UpdateConfig($ipserver) {
         
        // consulta sql (Transacciones Inteligentes).
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();
            
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE syst_configuraciones SET valor = '$ipserver' WHERE config = 'ipserver'");
        
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
    ////////////////////////////////////////////
    ///////////  Getters && Setters  ///////////
    ////////////////////////////////////////////
}

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
