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

// CLASE DEPARTAMENTOS    

class Departamentos {

    // Atributos  
    // Implementacion  
    // Listar Dptos
    function ReadDptos() {

        // Construir el JSON de SD
        $response = '{"success": true, "dptos": [';

        $sql = "SELECT id,nombre FROM gtia_dptos ORDER BY nombre ASC";
        // Ejecutar la consulta en la BD
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        if ($query->RecordCount() > 0) {

            $count = 0;
            while (!$query->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{"id": "' . $query->fields[0] . '", "nombre": "' . utf8_encode($query->fields[1]) . '"}';
                } else {
                    $response .= ',{"id": "' . $query->fields[0] . '", "nombre": "' . utf8_encode($query->fields[1]) . '"}';
                }
                $query->MoveNext();
            }
        }

        $response .= ']}';

        return $response;
    }

    ////////////////////////////////////////////
    // Insertar Dpto
    function NewDpto($nombre) {

        // Validar el nombre del Dpto
        $qry_check = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_dptos WHERE nombre = '$nombre'");
        if ($qry_check->RecordCount() >= 1) {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][5]
            ));
        } else {

            // insertar del Dpto
            // consulta sql (Transacciones Inteligentes).
            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_dptos(nombre) VALUES('$nombre')");

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
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Actualizar SD
    function UpdDpto($id_dpto, $nombre) {

        // Validar el nombre del Dpto
        $qry_check = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_dptos WHERE nombre = '$nombre' AND id != $id_dpto");
        if ($qry_check->RecordCount() >= 1) {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][6]
            ));
        } else {

            // consulta sql (Transacciones Inteligentes)
            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_dptos SET nombre = '$nombre' WHERE id = $id_dpto");

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
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Eliminar Dpto
    function DelDpto($id_dpto) {

        // consulta sql (Transacciones Inteligentes).
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_dptos WHERE id = $id_dpto");

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
    ////////////////////////////////////////////
    ///////////  Getters && Setters  ///////////
    ////////////////////////////////////////////
}

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
?>