<?php

// Incluir la clase de conexion
include_once 'connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once 'cadenas.php';
$cadenas = new Cadenas();

// Incluir los mensajes del sistema
include_once 'message.php';

//////////////////////////////////////////////
//////////       CLASE LOGIN       ///////////
//////////////////////////////////////////////
class Login {

    /////////////////////////////////////////
    ////////        Atributos       /////////
    /////////////////////////////////////////
    
    private $login_id;
    private $login_ultimoacceso;
    private $id_user;

    /////////////////////////////////////////
    /////////////////////////////////////////
    
    //  Validar Sesion de Usuario
    function Login_Active($user, $password) {

        $message = "";

        $sql_login = "SELECT
                        syst_usuarios.id_usuario,
                        syst_usuarios.nombre,
                        syst_usuarios.apellidos,
                        syst_usuarios.cargo,
                        syst_usuarios.usuario,
                        syst_usuarios.activo,
                        syst_usuarios.portada,
                        syst_usuarios.contrasena,
                        syst_usuarios.id_polo,
                        syst_usuarios.email,
                        syst_polos.nombre AS nombre_polo,
                        syst_usuarios.expira
                      FROM
                        syst_usuarios
                      LEFT JOIN syst_polos 
                        ON syst_usuarios.id_polo = syst_polos.id
                      WHERE
                        usuario = '$user'";
        $query_login = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_login);
        if ($query_login->RecordCount() > 0) {
            
            $data_password = $query_login->fields[7];
            $expiradb = $query_login->fields[11];
            if ($expiradb != null && $expiradb != '' && $expiradb != '1900-01-01') {
                $array_expira = explode('-', $$expiradb);
                $login_expira = $array_expira[2] .'-'. $array_expira[1] .'-'. $array_expira[0];
                $expiratime = strtotime($login_expira);
            }
            else {
                $expiratime = strtotime('now');
            }            
            $fecha_hoy = strtotime('now');
            
            if (($data_password != md5($password)) || ($query_login->fields[5] == 'No')) {
                
                $message = $GLOBALS["message"][1];
                return $message;
            } 
            elseif ($expiratime < $fecha_hoy) {
                $message = $GLOBALS["message"][31];
                return $message;
            }
            else {

                // Cargar configuracion del sistema
                $sql_config = "SELECT config, valor FROM syst_configuraciones";
                $query_config = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_config);
                $ipserver = $query_config->fields[1];

                // Cargar perfil(es) del usuario
                $perfiles = array();
                $sql_perfiles = "SELECT id_perfil FROM syst_usuarios_perfil WHERE id_usuario = " . $query_login->fields[0];
                $query_perfiles = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_perfiles);
                while (!$query_perfiles->EOF) {
		            $perfiles[] = $query_perfiles->fields[0];                                  
                    $query_perfiles->MoveNext();
                }
                $usuario_perfiles = implode(',', $perfiles);
                
                // Paranoia: destruimos las variables user_login y pass_login usadas 
                unset($user);
                unset($password);

                // Almacenamos los datos del usuario logeado 
                $idusuario = $query_login->fields[0];
                $nombre    = $query_login->fields[1];
                $apellidos = $query_login->fields[2];
                $cargo     = $query_login->fields[3];
                $usuario   = $query_login->fields[4];
                $portada   = 'index.html';
                $email     = $query_login->fields[9];
                $polo      = $query_login->fields[8];
                $polo_name = $query_login->fields[10];
                if($query_login->fields[6] == 'Controlpanel'){
                    $portada   = 'controlpanel/index.php';
                }

                // le damos un nombre a la sesion.
                session_name('semtiGarantiaSession');

                // inicia sessiones
                session_start();

                // Paranoia: decimos al navegador que no "cachee" esta página.
                //session_cache_limiter('nocache,private');

                // declaramos las variables de sesion
                //$sesion_user = $EnDecryptText->Encrypt_Text($sesion->get_user());

                $_SESSION['idsession']   = $idusuario;
                $_SESSION['idusuario']   = $idusuario;
                $_SESSION['usuario']     = $usuario;
                $_SESSION['nombre']      = $nombre;
                $_SESSION['apellidos']   = $apellidos;
                $_SESSION['cargo']       = $cargo;
                $_SESSION['portada']     = $portada;
                $_SESSION['email']       = $email;
                $_SESSION['polo']        = $polo;
                $_SESSION['polo_name']   = $polo_name;
                $_SESSION['inforesumen'] = '';
                $_SESSION['ipserver']    = $ipserver;
                $_SESSION['perfiles']    = $usuario_perfiles;

                return $message;
            }
        } else {

            $message = $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg();   //$GLOBALS["message"][1];
            return $message;
        }
    }


    /////////////////////////////////////////////////////////
    ///////////  Getters && Setters  ///////////
    //---------------  ID USER  -----------------*/
    function get_login_id() {

        return $this->login_id;
    }

    function set_login_id($new_login_id) {

        $this->login_id = $new_login_id;
    }

    //---------------  ULTIMO ACCESO  -----------------*/
    function get_login_ultimoacceso() {

        return $this->login_ultimoacceso;
    }

    function set_login_ultimoacceso($new_login_ultimoacceso) {

        $this->login_ultimoacceso = $new_login_ultimoacceso;
    }

    //---------------  NOMBRE DEL USUARIO  -----------------*/
    function get_login_nameuser() {

        return $this->login_nameuser;
    }

    function set_login_nameuser($new_login_nameuser) {

        $this->login_nameuser = $new_login_nameuser;
    }

}


////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////


if ((isset($_POST['action'])) && ($_POST['action'] == 'login')) {

    $sesion = new Login();

    $user = trim($_POST['username']);
    $password = trim($_POST['password']);

    $iniciarsesion = $sesion->Login_Active($user, $password);

    echo json_encode(array(
        "success" => true,
        "message" => $iniciarsesion
    ));
}

