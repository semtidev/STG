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

// CLASE PROBLEMAS    

class Problemas {

    // Atributos  
    // Implementacion  
    // Listar Problemas
    function ReadProblemas() {

        // Construir el JSON de SD
        $response = '{"success": true, "gtiaproblemas": [';
        
        $sql = "SELECT id,descripcion FROM gtia_problemas ORDER BY descripcion ASC";

        // Ejecutar la consulta en la BD
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        if($query->RecordCount() > 0){
            
            $count = 0;
            while(!$query->EOF){
                $count++;
                if($count == 1){
                    $response .= '{"id": "'.$query->fields[0].'", "descripcion": "'.  utf8_encode($query->fields[1]).'"}';
                }
                else{
                    $response .= ',{"id": "'.$query->fields[0].'", "descripcion": "'.  utf8_encode($query->fields[1]).'"}';
                }
                $query->MoveNext();
            }
        }
        
        $response .= ']}';
        
        return $response;
    }

    ////////////////////////////////////////////
    // Insertar Dpto
    function NewProblema($descripcion) {

        // Validar el descripcion del Problema
        $qry_check = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_problemas WHERE descripcion = '$descripcion'");
        if ($qry_check->RecordCount() >= 1) {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][7]
            ));
        } else {

            // insertar el Tipo de Problema
            // consulta sql (Transacciones Inteligentes).
            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_problemas(descripcion) VALUES('$descripcion')");

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
    // Actualizar Problema
    function UpdProblema($id_problema, $descripcion) {

        // Validar descripcion del Tipo de Problema
        $qry_check = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_problemas WHERE descripcion = '$descripcion' AND id != $id_problema");
        if ($qry_check->RecordCount() >= 1) {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][8]
            ));
        } else {

            // consulta sql (Transacciones Inteligentes)
            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_problemas SET descripcion = '$descripcion' WHERE id = $id_problema");

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
    // Eliminar Problema
    function DelProblema($id_problema) {

        // consulta sql (Transacciones Inteligentes).
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_problemas WHERE id = $id_problema");

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