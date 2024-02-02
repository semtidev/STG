<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir script de conexion a BD
include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

// Incluir los menajes del sistema
include_once '../sistema/message.php';


// CLASE SOLICITUD DEFECTACION    

class Hfo {

    // Atributos  
    // Implementacion  
    
    // Listar SD
    function ReadHfo() {

        $id_user = $_SESSION['idsession'];
        
        // Construir el JSON de SD
        $response = '{"success": true, "hfo": [';

        // Ejecutar el SP en la BD
        $sql = "Info_hfo_List $id_user";        
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        if ($query->RecordCount() > 0) {

            $count = 0;
            while (!$query->EOF) {
                $count++;
                if($query->fields[6] == '1900-01-01'){ $desde = null; }else{ $desde = $query->fields[6]; }
                if($query->fields[7] == '1900-01-01'){ $hasta = null; }else{ $hasta = $query->fields[7]; }
                if ($count == 1) {
                    $response .= '{"id": "' . $query->fields[0] . '",
                                   "id_user": "' . $query->fields[1] . '",
                                   "titulo": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '",
                                   "proyecto": "' . $GLOBALS['cadenas']->utf8($query->fields[3]) . '",
                                   "zona": "' . $GLOBALS['cadenas']->utf8($query->fields[4]) . '",
                                   "objeto": "' . $query->fields[5] . '",
                                   "desde": "' . $desde . '",
                                   "hasta": "' . $hasta . '",
                                   "fechamod": "' . $query->fields[8] . '"}';
                } else {
                    $response .= ',{"id": "' . $query->fields[0] . '",
                                    "id_user": "' . $query->fields[1] . '",
                                    "titulo": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '",
                                    "proyecto": "' . $GLOBALS['cadenas']->utf8($query->fields[3]) . '",
                                    "zona": "' . $GLOBALS['cadenas']->utf8($query->fields[4]) . '",
                                    "objeto": "' . $query->fields[5] . '",
                                    "desde": "' . $desde . '",
                                    "hasta": "' . $hasta . '",
                                    "fechamod": "' . $query->fields[8] . '"}';
                }
                $query->MoveNext();
            }
        }

        $response .= ']}';

        return $response;
    }

    ////////////////////////////////////////////
    // Obtener los datos de un Informe
    function loadInfoData($informe){
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");
        
        // Construir el JSON de HFOdata
        $response = '{"success": true, "hfodata": [';

        // Seleccionar el informe
        $sql_info = "SELECT  
                        info_hfo.proyecto,
                        info_hfo.zona,
                        info_hfo.objeto,
                        info_hfo.desde,
                        info_hfo.hasta
                     FROM 
                        info_hfo
                     WHERE
                        info_hfo.id = $informe";
        
        $query_info = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_info);
        
        // Guardar la configuracion del informe
        $proyecto = $query_info->fields[0];
        $zona     = $query_info->fields[1];
        $objeto   = $query_info->fields[2];
        $desde    = $query_info->fields[3];
        $hasta    = $query_info->fields[4];

        //if (strstr($objeto, 'Todos') == false) { return 'No es todo'; }else{ return $proyecto.', '.$zona.', '.$objeto.', '.$desde.', '.$hasta; }
        // Recorrer los problemas y obtener los datos del informe
        $sql_problema = "SELECT id,descripcion FROM gtia_problemas";
        $qry_problema = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_problema);
        
        while(!$qry_problema->EOF){
            
            $habit_cont  = 0;
            $count_sd    = 0;
            $pendientes  = 0;
            $sd_list     = '';
            $habit_list  = '';
            $sd_exist    = false;
            $id_problema = $qry_problema->fields[0];
            
            // Obtener las SD por problema
            $sql_data = "SELECT
                            gtia_sd.id,
                            gtia_sd.numero,
                            gtia_sd.objeto_local
                         FROM
                            gtia_sd
                         WHERE
                            gtia_sd.proyecto = '$proyecto' AND
                            gtia_sd.id_problema = $id_problema";
                            
            if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
                $sql_data .= " AND (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta')";
            }
            
            // Seleccionar las zonas
            if (strstr($zona, 'Todas') == false) {
                
                $zonas_array = explode(', ', $zona);
                $sql_data   .= " AND (";
                $count       = 0;
                
                for($i = 0; $i < count($zonas_array); $i++){
                    $count++;
                    $zona_item = $zonas_array[$i];
                    if($count == 1){                    
                        $sql_data .= "gtia_sd.zona = '$zona_item'";
                    }
                    else{
                        $sql_data .= " OR gtia_sd.zona = '$zona_item'";
                    }
                }
                $sql_data   .= ")";                
            }
            
            // Seleccionar las habitaciones
            if (strstr($objeto, 'Todos') == false) {
                
                $objetos_array = explode(', ', $objeto);
                $sql_data     .= " AND (";
                $count         = 0;
                
                for($i = 0; $i < count($objetos_array); $i++){
                    $count++;
                    $objeto_item = $objetos_array[$i];
                    if($count == 1){                    
                        $sql_data .= "gtia_sd.objeto_local LIKE '%$objeto_item%'";
                    }
                    else{
                        $sql_data .= " OR gtia_sd.objeto_local LIKE '%$objeto_item%'";
                    }
                }
                $sql_data .= ")";
            }
            else{
                $sql_data .= " AND gtia_sd.objeto_local LIKE '%BW % (%'";
            }           
            
            // Recorrer las SD y seleccionar los datos
            $qry_data = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_data);            
                        
            if($qry_data->RecordCount() > 0){
                
                $sd_exist = true;
                while(!$qry_data->EOF){
                    
                    $count_sd++;
                    $id_sd = $qry_data->fields[0];
                                        
                    // Seleccionar las SD
                    if($count_sd == 1){
                        $sd_list .= $qry_data->fields[1];
                    }
                    else{
                        $sd_list .= ', '.$qry_data->fields[1];
                    }
                    
                    // Seleccionar las habitaciones por cada SD
                    $sql_habit  = "SELECT
                                      gtia_partes.nombre,
                                      gtia_sd_partes.estado
                                   FROM
                                      gtia_partes,
                                      gtia_sd_partes
                                   WHERE
                                      gtia_sd_partes.id_sd = $id_sd AND
                                      gtia_sd_partes.id_parte = gtia_partes.id";
                    $qry_habit  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_habit);
                                    
                    while(!$qry_habit->EOF){
                        
                        $habit_cont++;
                        
                        // Habitaciones
                        if($habit_cont == 1){
                            $habit_list .= $qry_habit->fields[0];
                        }
                        else{
                            $habit_list .= ', '.$qry_habit->fields[0];
                        }
                        
                        // Habitaciones Pendientes                    
                        if($qry_habit->fields[1] == 'PR' || $qry_habit->fields[1] == 'Por Resolver'){
                            $pendientes++;
                        }
                        
                        $qry_habit->MoveNext();
                    }
                                        
                    $qry_data->MoveNext();
                }
            }
            
            if($sd_exist == true){
                
                // Actualizar/Insertar el renglon en los datos del informe
                $sql_renglon = "SELECT id FROM info_hfodata WHERE id_infohfo = $informe AND id_problema = $id_problema";
                $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
                if($qry_renglon->RecordCount() > 0){
                    // Actualizar Renglon
                    $sql_renglonUpd = "UPDATE
                                          info_hfodata
                                       SET
                                          sd = '$sd_list',
                                          habitaciones = '$habit_list',
                                          ctdad_habit = $habit_cont,
                                          pendientes = $pendientes,
                                          fechamod = '$fecha_update'
                                       WHERE
                                          id_infohfo = $informe AND
                                          id_problema = $id_problema";
                    $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
                }
                else{
                    // Insertar Renglon
                    $sql_renglonAdd = "INSERT INTO
                                          info_hfodata(
                                              id_infohfo,
                                              sd,
                                              habitaciones,
                                              ctdad_habit,
                                              pendientes,
                                              id_problema,
                                              fechamod
                                          )
                                       VALUES(
                                          $informe,
                                          '$sd_list',
                                          '$habit_list',
                                          $habit_cont,
                                          $pendientes,
                                          $id_problema,
                                          '$fecha_update'
                                       )";
                    $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
                }
            }
            
            //////////////////////////////////////////////
                        
            $qry_problema->MoveNext();
        }
        
        ///////////////////////////////////////////////
        
        // Eliminar registro de datos del informe
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM info_hfodata WHERE id_infohfo = $informe AND fechamod != '$fecha_update'");
            
        // Cargar los datos del informe
        $sql_infodata = "SELECT  
                            info_hfodata.id,
                            info_hfodata.sd,
                            info_hfodata.habitaciones,
                            info_hfodata.ctdad_habit,
                            info_hfodata.pendientes,
                            gtia_problemas.descripcion,
                            info_hfodata.observaciones,
                            info_hfodata.fechamod
                         FROM 
                            info_hfodata, gtia_problemas
                         WHERE
                            info_hfodata.id_infohfo = $informe AND
                            info_hfodata.id_problema = gtia_problemas.id";
        
        $query_infodata = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_infodata);
        
        if ($query_infodata->RecordCount() > 0) {

            $count = 0;
            while (!$query_infodata->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_infohfo": "' . $informe . '",
                                    "sd": "' . $query_infodata->fields[1] . '",
                                    "habitaciones": "' . $query_infodata->fields[2] . '",
                                    "ctdad_habit": "' . $query_infodata->fields[3] . '",
                                    "pendientes": "' . $query_infodata->fields[4] . '",
                                    "problema": "' . utf8_encode($query_infodata->fields[5]) . '",
                                    "observaciones": "' . utf8_encode($query_infodata->fields[6]) . '",
                                    "fechamod": "' . $query_infodata->fields[7] . '"
                                  }';
                } else {
                    $response .= ',{
                                     "id": "' . $query_infodata->fields[0] . '",
                                     "id_infohfo": "' . $informe . '",
                                     "sd": "' . $query_infodata->fields[1] . '",
                                     "habitaciones": "' . $query_infodata->fields[2] . '",
                                     "ctdad_habit": "' . $query_infodata->fields[3] . '",
                                     "pendientes": "' . $query_infodata->fields[4] . '",
                                     "problema": "' . utf8_encode($query_infodata->fields[5]) . '",
                                     "observaciones": "' . utf8_encode($query_infodata->fields[6]) . '",
                                     "fechamod": "' . $query_infodata->fields[7] . '"
                                   }';
                }
                
                $query_infodata->MoveNext();
            }
        }

        $response .= ']}';

        return $response;
    }
    
    ////////////////////////////////////////////
    // Obtener las zonas de un proyecto    
    function hfoLoadZonas($proyecto) {

        // Construir el JSON de HFO
        $response = '{"success": true, "zonas": [';

        // Ejecutar el SP en la BD
        $sql = "SELECT  
                    gtia_zonas.id
                    ,gtia_zonas.id_proyecto
                    ,gtia_zonas.nombre
                    ,gtia_zonas.fecha_ini
                    ,gtia_zonas.fecha_fin
                FROM 
                    gtia_zonas, gtia_proyectos
                WHERE
                    gtia_zonas.id_proyecto = gtia_proyectos.id AND
                    gtia_proyectos.nombre = '$proyecto'
                ORDER BY 
                    gtia_zonas.nombre ASC";
        
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        if ($query->RecordCount() > 0) {

            $count = 0;
            while (!$query->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{"id": "' . $query->fields[0] . '", "id_proyecto": "' . $query->fields[1] . '", "nombre": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '", "fecha_ini": "' . $query->fields[3] . '", "fecha_fin": "' . $query->fields[4] . '"}';
                } else {
                    $response .= ',{"id": "' . $query->fields[0] . '", "id_proyecto": "' . $query->fields[1] . '", "nombre": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '", "fecha_ini": "' . $query->fields[3] . '", "fecha_fin": "' . $query->fields[4] . '"}';
                }
                $query->MoveNext();
            }
        }

        $response .= ']}';

        return $response;
    }

    ////////////////////////////////////////////
    // Obtener los objetos de una zona
    function hfoLoadObjetos($proyecto,$zona) {

        // Construir el JSON de SD
        $response = '{"success": true, "objetos": [';

        // Ejecutar el SP en la BD
        if($zona == 'Todas'){
            $sql = "SELECT  
                    gtia_objetos.id
                        ,gtia_objetos.id_zona
                        ,gtia_objetos.nombre
                    FROM 
                        gtia_objetos, gtia_proyectos, gtia_zonas
                    WHERE
                        gtia_objetos.nombre LIKE 'BW%' AND
                        gtia_objetos.id_zona = gtia_zonas.id AND
                        gtia_zonas.id_proyecto = gtia_proyectos.id AND
                        gtia_proyectos.nombre = '$proyecto'
                    ORDER BY 
                        gtia_objetos.nombre ASC";
        }
        else{
            $zonarray = explode(',', $zona);
            
            $sql = "SELECT  
                        gtia_objetos.id
                        ,gtia_objetos.id_zona
                        ,gtia_objetos.nombre
                    FROM 
                        gtia_objetos, gtia_proyectos, gtia_zonas
                    WHERE
                        gtia_objetos.nombre LIKE 'BW%' AND
                        gtia_objetos.id_zona = gtia_zonas.id AND (";
                        
                    for($i = 0; $i < count($zonarray); $i++){
                        $zona = trim($zonarray[$i]);
                        if($i == 0){
                            $sql .= "gtia_zonas.nombre = '$zona'";
                        }
                        else{
                            $sql .= " OR gtia_zonas.nombre = '$zona'";
                        }
                    }
            
            $sql .= ") AND gtia_zonas.id_proyecto = gtia_proyectos.id AND
                        gtia_proyectos.nombre = '$proyecto'
                    ORDER BY 
                        gtia_objetos.nombre ASC";
        }
        
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        if ($query->RecordCount() > 0) {

            $count = 0;
            while (!$query->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{"id": "' . $query->fields[0] . '", "id_zona": "' . $query->fields[1] . '", "nombre": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '"}';
                } else {
                    $response .= ',{"id": "' . $query->fields[0] . '", "id_zona": "' . $query->fields[1] . '", "nombre": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '"}';
                }
                $query->MoveNext();
            }
        }

        $response .= ']}';

        return $response;
    }
        
    ////////////////////////////////////////////
    // Insertar nuevo informe HFO
    function HfoInsert($titulo, $proyecto, $zona, $objeto, $desde, $hasta) {

        $id_user = $_SESSION['idsession'];
        
        if($zona == ''){ $zona = 'Todas'; }else{ $zona = str_replace(',', ', ', $zona); }
        if($objeto == '') { $objeto = 'Todos'; }else{ $objeto = str_replace(',', ', ', $objeto); }
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO info_hfo(id_user,titulo,proyecto,zona,objeto,desde,hasta,fechamod) VALUES($id_user,'$titulo','$proyecto','$zona','$objeto','$desde','$hasta','$fecha_update')");

        if (!$GLOBALS["adoMYSQL_SEMTI"]->HasFailedTrans()) {

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
    // Cargar campos del Form de SD
    function HfoFormLoad($id) {

        $qry_hfo = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT 
                        info_hfo.id,
                        info_hfo.titulo,				
                        info_hfo.proyecto,
                        info_hfo.zona,
                        info_hfo.objeto,
                        info_hfo.desde,
                        info_hfo.hasta
                        FROM 
                            info_hfo
                        WHERE
                            info_hfo.id = $id");
            
        if ($qry_hfo->RecordCount() > 0) {

            if ($qry_hfo->fields[3] == 'Todas'){
                $zona = '';
            }else{
                $zona = $qry_hfo->fields[3];
            }
            if ($qry_hfo->fields[4] == 'Todos'){
                $objeto = '';
            }else{
                $objeto = $qry_hfo->fields[4];
            }

            $response = json_encode(array(
                "success" => true,
                "data" => array(
                    "id" => $qry_hfo->fields[0],
                    "titulo" => $qry_hfo->fields[1],
                    "proyecto" => $GLOBALS['cadenas']->utf8($qry_hfo->fields[2]),
                    "zona" => $GLOBALS['cadenas']->utf8($zona),
                    "objeto" => $GLOBALS['cadenas']->utf8($objeto),
                    "desde" => $qry_hfo->fields[5],
                    "hasta" => $qry_hfo->fields[6]
                )
            ));
        }
        else {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()
            ));
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Modificar informe HFO
    function HfoUpdate($id, $titulo, $proyecto, $zona, $objeto, $desde, $hasta) {

        $id_user = $_SESSION['idsession'];
        
        if($zona == ''){ $zona = 'Todas'; }else{ $zona = str_replace(',', ', ', $zona); }
        if($objeto == '') { $objeto = 'Todos'; }else{ $objeto = str_replace(',', ', ', $objeto); }
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_hfo SET titulo = '$titulo',proyecto = '$proyecto',zona = '$zona',objeto = '$objeto',desde = '$desde',hasta = '$hasta',fechamod = '$fecha_update' WHERE id = $id");

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
    
    // Eliminar Informe Hfo
    function HfoDelete($id) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("Info_hfo_Delete $id");

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
    //  Eliminar Informes Check
    function HfoCheckDelete($parametros) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $records = json_decode(stripslashes($parametros));
        foreach ($records as $record) {

            $id = $record->id;
            $GLOBALS["adoMSSQL_SEMTI"]->Execute("Info_hfo_Delete $id");
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
    //  Comentario de los datos del informe
    function hfodataComent($id_row,$comentario) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_hfodata SET observaciones = '$comentario' WHERE id = $id_row");
        
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
