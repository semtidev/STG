<?php

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir la clase de conexion
include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

// Incluir los mensajes del sistema
include_once '../sistema/message.php';

if ((isset($_POST['accion'])) && ($_POST['accion'] == 'Actualizar')) {

    $usuario = $_POST['usuario'];
    $oldpassword = $_POST['oldpassword'];
    $newpassword1 = $_POST['newpassword1'];
    $newpassword2 = $_POST['newpassword2'];

    // Validar contraseñas nuevas
    if ($newpassword1 != $newpassword2) {

        echo json_encode(array(
            "failure" => true,
            "message" => $message[16]
        ));
    } else {

        // Actualizar contraseña
        $newpassword = md5($newpassword1);

        $adoMSSQL_SEMTI->StartTrans();

        $sql = 'Syst_usuarios_Password "'.$newpassword.'",'.$usuario;
        $adoMSSQL_SEMTI->Execute($sql);

        if (!$adoMSSQL_SEMTI->HasFailedTrans()) {

            echo json_encode(array(
                "success" => true
            ));
        } else {

            echo json_encode(array(
                "failure" => true,
                "message" => $message[2]
            ));
        }

        $adoMSSQL_SEMTI->CompleteTrans();
        $adoMSSQL_SEMTI->Close();
    }
} else {

    echo json_encode(array(
        "failure" => true,
        "message" => $message[2]
    ));
}
?>