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
//////      CLASE PERFILES       ///////
////////////////////////////////////////

class Perfiles {

    /////////////////////////////////////////
    //////////////  Atributos  //////////////
    /////////////////////////////////////////
    
    
    /////////////////////////////////////////
    ///////////  Implementacion  ////////////
    /////////////////////////////////////////
    // Listar perfiles
    function ReadPerfiles($limit, $page) {

        // Recoger el total de registros de la tabla
        $total_query = $GLOBALS["adoMSSQL_SEMTI"]->Execute('DECLARE @CTDAD INT; @CTDAD = EXEC Syst_perfiles_Ctdad; SELECT @CTDAD');
        $total = trim($total_query);
        
        // Construir el JSON de SD
        $response = '{"success": true, "total": "'.$total.'", "perfiles": [';
        
        $sql = "Syst_perfiles_Paging $limit,$page";

        // Ejecutar la consulta en la BD
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        if($query->RecordCount() > 0){
            
            $count = 0;
            while(!$query->EOF){
                $count++;
                if($count == 1){
                    $response .= '{"id": "'.$query->fields[0].'", "nombre": "'.$GLOBALS['cadenas']->utf8($query->fields[1]).'", "descripcion": "'.$GLOBALS['cadenas']->utf8($query->fields[2]).'"}';
                }
                else{
                    $response .= ',{"id": "'.$query->fields[0].'", "nombre": "'.$GLOBALS['cadenas']->utf8($query->fields[1]).'", "descripcion": "'.$GLOBALS['cadenas']->utf8($query->fields[2]).'"}';
                }
                $query->MoveNext();
            }
        }
        
        $response .= ']}';
        
        return $response;
        
    }

    ////////////////////////////////////////////
    // Crear Perfil
    function CreatePerfil($nombre, $descripcion, $permisos) {

        // Validar Perfil 
        $sql_validar = "SELECT id FROM syst_perfiles WHERE nombre = '$nombre'";
        $qry_validar = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_validar);
        
        if ($qry_validar->RecordCount() > 0) {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][15]
            ));
        } else {

            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            $records = json_decode(stripslashes($permisos));

            // Agregar el perfil
            $sql_perfil   = "INSERT INTO syst_perfiles(nombre,descripcion) VALUES('$nombre','$descripcion')";
            $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_perfil); 
            $qry_idperfil = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM syst_perfiles WHERE nombre = '$nombre'"); 
            $id_perfil    = $qry_idperfil->fields[0];
            
            // Agregar los permisos
            foreach ($records as $record) {

                if (substr($record->id, 0, 1) != 1) {

                    $id_pagina = substr($record->id, 1);
                    if ($record->modificar == true)
                        $modificar = 1;
                    else
                        $modificar = 0;
                    if ($record->lectura_exportar == true)
                        $lectura_exportar = 1;
                    else
                        $lectura_exportar = 0;
                    if ($record->lectura == true)
                        $lectura = 1;
                    else
                        $lectura = 0;
                    if ($record->escritura == true)
                        $escritura = 1;
                    else
                        $escritura = 0;

                    $sql = "Syst_permisos_Insert $id_pagina,$id_perfil,$modificar,$lectura_exportar,$lectura,$escritura";
                    $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
                }
            }

            if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {

                $response = json_encode(array(
                    "success" => true
                ));
            } else {

                $response = json_encode(array(
                    "failure" => true,
                    "message" => $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()  //$GLOBALS["message"][2]
                ));
            }

            $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
            $GLOBALS["adoMSSQL_SEMTI"]->Close();
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Modificar Perfil
    function UpdatePerfil($id_perfil, $nombre, $descripcion, $permisos) {

        $records = json_decode(stripslashes($permisos));
        
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();
        
        // Modificar el perfil
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE syst_perfiles SET nombre = '$nombre',descripcion = '$descripcion' WHERE id = $id_perfil");
        
        // Eliminar permisos
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM syst_permisos WHERE id_perfil = $id_perfil");

        // Agregar los nuevos permisos
        foreach ($records as $record) {

            if (substr($record->id, 0, 1) != 1) {

                $id_pagina = substr($record->id, 1);
                if ($record->modificar == true)
                    $modificar = 1;
                else
                    $modificar = 0;
                if ($record->lectura_exportar == true)
                    $lectura_exportar = 1;
                else
                    $lectura_exportar = 0;
                if ($record->lectura == true)
                    $lectura = 1;
                else
                    $lectura = 0;
                if ($record->escritura == true)
                    $escritura = 1;
                else
                    $escritura = 0;

                $sql = "Syst_permisos_Insert $id_pagina,$id_perfil,$modificar,$lectura_exportar,$lectura,$escritura";
                $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
            }
        }

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
    // Eliminar Perfil
    function DeletePerfil($id_perfil) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("Syst_perfiles_Delete $id_perfil");

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
