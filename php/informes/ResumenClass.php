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

class Resumen {

    // Atributos  
    // Implementacion  
    
    // Listar SD
    function ReadResumen() {
       
        $id_user     = $_SESSION['idsession'];
        $fecha_desde = '';
        $fecha_hasta = '';
        
        // Construir el JSON de SD
        $response = '{"success": true, "resumen": [';

        // Ejecutar el SP en la BD
        $sql = "Info_resumen_List $id_user";        
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
        
        if ($query->RecordCount() > 0) {

            $count = 0;
            while (!$query->EOF) {
                $count++;
                if($query->fields[5] == '1900-01-01'){ $fecha_desde = ''; }else{ $fecha_desde =  $query->fields[5]; }
                if($query->fields[6] == '1900-01-01'){ $fecha_hasta = ''; }else{ $fecha_hasta =  $query->fields[6]; }
                                                
                if ($count == 1) {
                    $response .= '{"id": "' . $query->fields[0] . '",
                                   "id_user": "' . $query->fields[1] . '",
                                   "titulo": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '",
                                   "proyecto": "' . $GLOBALS['cadenas']->utf8($query->fields[3]) . '",
                                   "zona": "' . $GLOBALS['cadenas']->utf8($query->fields[4]) . '",
                                   "desde": "' . $fecha_desde . '",
                                   "hasta": "' . $fecha_hasta . '",
                                   "comentario_inicial": "' . $GLOBALS['cadenas']->utf8($query->fields[7]) . '",
                                   "comentario_final": "' . $GLOBALS['cadenas']->utf8($query->fields[8]) . '",
                                   "fechamod": "' . $query->fields[9] . '"}';
                } else {
                    $response .= ',{"id": "' . $query->fields[0] . '",
                                    "id_user": "' . $query->fields[1] . '",
                                    "titulo": "' . $GLOBALS['cadenas']->utf8($query->fields[2]) . '",
                                    "proyecto": "' . $GLOBALS['cadenas']->utf8($query->fields[3]) . '",
                                    "zona": "' . $GLOBALS['cadenas']->utf8($query->fields[4]) . '",
                                    "desde": "' . $fecha_desde . '",
                                    "hasta": "' . $fecha_hasta . '",
                                    "comentario_inicial": "' . $GLOBALS['cadenas']->utf8($query->fields[7]) . '",
                                    "comentario_final": "' . $GLOBALS['cadenas']->utf8($query->fields[8]) . '",
                                    "fechamod": "' . $query->fields[9] . '"}';
                }
                $query->MoveNext();
            }
        }

        $response .= ']}';
        return $response;
    }

    /////////////////////////////////////////////////////
    // Obtener los datos de la Pestaña Estado de las SD
    function loadResumenDataEstados($informe){
        
        // Inicializar variables de estado
        $sd_firmadas   = 0;
        $sd_noproceden = 0;
        $sd_poresolver = 0;
        $sd_reclamadas = 0;
        $sd_total      = 0;
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");
        
        // Construir el JSON de Resumenestado
        $response = '{"success": true, "estados": [';

        // Seleccionar el informe
        $sql_info = "SELECT  
                        info_resumen.proyecto,
                        info_resumen.zona,
                        info_resumen.desde,
                        info_resumen.hasta
                     FROM 
                        info_resumen
                     WHERE
                        info_resumen.id = $informe";
        
        $query_info = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_info);
        
        // Guardar la configuracion del informe
        $proyecto = $query_info->fields[0];
        $zona     = $query_info->fields[1];
        $desde    = $query_info->fields[2];
        $hasta    = $query_info->fields[3];
        
        // Obtener las SD en proceso
        $sql_enproceso = "SELECT
                            COUNT(estado) AS enproceso
                         FROM
                            gtia_sd
                         WHERE
                            estado = 'En Proceso' AND
                            proyecto = '$proyecto'";
        
        if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
            $sql_enproceso .= " AND (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
        }
        
        if ($zona != 'Todas') {
            
            $zonas_array   = explode(', ', $zona);
            $sql_enproceso .= " AND (";
            $zonas_count   = 0;
            
            for($i = 0; $i < count($zonas_array); $i++){
                $zonas_count++;
                $zona_item = $zonas_array[$i];
                if($zonas_count == 1){                    
                    $sql_enproceso .= "zona = '$zona_item'";
                }
                else{
                    $sql_enproceso .= " OR zona = '$zona_item'";
                }
            }
            $sql_enproceso .= ")";                
        }
        
        $qry_enproceso = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_enproceso);   
        $sd_enproceso  = $qry_enproceso->fields[0];

        // Obtener las SD firmadas
        $sql_firmadas = "SELECT
                            COUNT(estado) AS firmadas
                         FROM
                            gtia_sd
                         WHERE
                            estado = 'Firmada' AND
                            proyecto = '$proyecto'";
        
        if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
            $sql_firmadas .= " AND (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
        }
        
        if ($zona != 'Todas') {
            
            $zonas_array   = explode(', ', $zona);
            $sql_firmadas .= " AND (";
            $zonas_count   = 0;
            
            for($i = 0; $i < count($zonas_array); $i++){
                $zonas_count++;
                $zona_item = $zonas_array[$i];
                if($zonas_count == 1){                    
                    $sql_firmadas .= "zona = '$zona_item'";
                }
                else{
                    $sql_firmadas .= " OR zona = '$zona_item'";
                }
            }
            $sql_firmadas .= ")";                
        }
        
        $qry_firmadas = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_firmadas);   
        $sd_firmadas  = $qry_firmadas->fields[0];
        
        // Obtener las SD que no proceden
        $sql_noproceden = "SELECT
                            COUNT(estado) AS noproceden
                         FROM
                            gtia_sd
                         WHERE
                            estado = 'No Procede' AND
                            proyecto = '$proyecto'";
        
        if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
            $sql_noproceden .= " AND (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
        }
        
        if ($zona != 'Todas') {
            
            $zonas_array     = explode(', ', $zona);
            $sql_noproceden .= " AND (";
            $zonas_count     = 0;
            
            for($i = 0; $i < count($zonas_array); $i++){
                $zonas_count++;
                $zona_item = $zonas_array[$i];
                if($zonas_count == 1){                    
                    $sql_noproceden .= "zona = '$zona_item'";
                }
                else{
                    $sql_noproceden .= " OR zona = '$zona_item'";
                }
            }
            $sql_noproceden .= ")";                
        }
        
        $qry_noproceden = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_noproceden);   
        $sd_noproceden  = $qry_noproceden->fields[0];
        
        // Obtener las SD que estan por resolver
        $sql_poresolver = "SELECT
                            COUNT(estado) AS poresolver
                         FROM
                            gtia_sd
                         WHERE
                            estado = 'Por Resolver' AND
                            proyecto = '$proyecto'";
        
        if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
            $sql_poresolver .= " AND (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
        }
        
        if ($zona != 'Todas') {
            
            $zonas_array     = explode(', ', $zona);
            $sql_poresolver .= " AND (";
            $zonas_count     = 0;
            
            for($i = 0; $i < count($zonas_array); $i++){
                $zonas_count++;
                $zona_item = $zonas_array[$i];
                if($zonas_count == 1){                    
                    $sql_poresolver .= "zona = '$zona_item'";
                }
                else{
                    $sql_poresolver .= " OR zona = '$zona_item'";
                }
            }
            $sql_poresolver .= ")";                
        }
        
        $qry_poresolver = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_poresolver);   
        $sd_poresolver  = $qry_poresolver->fields[0];
        
        // Obtener las SD reclamadas
        $sql_reclamadas = "SELECT
                            COUNT(estado) AS reclamadas
                         FROM
                            gtia_sd
                         WHERE
                            estado = 'Reclamada' AND
                            proyecto = '$proyecto'";
        
        if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
            $sql_reclamadas .= " AND (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
        }
        
        if ($zona != 'Todas') {
            
            $zonas_array     = explode(', ', $zona);
            $sql_reclamadas .= " AND (";
            $zonas_count     = 0;
            
            for($i = 0; $i < count($zonas_array); $i++){
                $zonas_count++;
                $zona_item = $zonas_array[$i];
                if($zonas_count == 1){                    
                    $sql_reclamadas .= "zona = '$zona_item'";
                }
                else{
                    $sql_reclamadas .= " OR zona = '$zona_item'";
                }
            }
            $sql_reclamadas .= ")";                
        }
        
        $qry_reclamadas = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_reclamadas);   
        $sd_reclamadas  = $qry_reclamadas->fields[0];
        
        // Calcular el total de SD
        $sd_total = $sd_firmadas + $sd_noproceden + $sd_poresolver + $sd_reclamadas + $sd_enproceso;
        
        
        // Actualizar la tabla de estado del informe
        
        $sd_exist = false;
        $sql_exist_estado = "SELECT COUNT(id) AS existe FROM info_resumendata_estados WHERE id_resumen = $informe";
        $qry_exist_estado = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_exist_estado);   
        
        if($qry_exist_estado->fields[0] > 0){  $sd_exist = true;  }
            
        if($sd_exist == true){
                
            // Actualizar Renglon
            $sql_renglonUpd = "UPDATE
                                  info_resumendata_estados
                               SET
                                  firmadas = $sd_firmadas,
                                  noproceden = $sd_noproceden,
                                  poresolver = $sd_poresolver,
                                  reclamadas = $sd_reclamadas,
                                  enproceso = $sd_enproceso,
                                  total = $sd_total,
                                  fechamod = '$fecha_update'
                               WHERE
                                  id_resumen = $informe";
            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
        }
        else{
            // Insertar Renglon
            $sql_renglonAdd = "INSERT INTO
                                  info_resumendata_estados(
                                      id_resumen,
                                      firmadas,
                                      noproceden,
                                      poresolver,
                                      reclamadas,
                                      total,
                                      fechamod,
                                      enproceso
                                  )
                               VALUES(
                                  $informe,
                                  $sd_firmadas,
                                  $sd_noproceden,
                                  $sd_poresolver,
                                  $sd_reclamadas,
                                  $sd_total,
                                  '$fecha_update',
                                  $sd_enproceso
                               )";
            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
        }
                
        
        // Cargar los datos del informe
        
        $sql_estadosdata = "SELECT  
                                id,
                                firmadas,
                                noproceden,
                                poresolver,
                                reclamadas,
                                total,
                                fechamod,
                                enproceso
                            FROM 
                                info_resumendata_estados
                            WHERE
                                id_resumen = $informe";
        
        $query_estadosdata = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_estadosdata);
        
        if ($query_estadosdata->RecordCount() > 0) {

            $response .= '{ "id": "' . $query_estadosdata->fields[0] . '",
                            "id_resumen": "' . $informe . '",
                            "indicador": "Solicitudes de Defectaci\xF3n en tiempo de Garant\xEDa",
                            "firmadas": "' . $query_estadosdata->fields[1] . '",
                            "noproceden": "' . $query_estadosdata->fields[2] . '",
                            "poresolver": "' . $query_estadosdata->fields[3] . '",
                            "reclamadas": "' . $query_estadosdata->fields[4] . '",
                            "total": "' . $query_estadosdata->fields[5] . '",
                            "fechamod": "' . $query_estadosdata->fields[6] . '",
                            "enproceso": "'. $query_estadosdata->fields[7] .'"
                           }';             
        }

        $response .= ']}';

        return $response;
    }
    
   
    // Obtener los datos de la Pestaña SD Pendientes
    function loadResumenDataSDPendientes($informe){
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");
        
        // Construir el JSON de SD Pendientes
        $response = '{"success": true, "sdpendientes": [';
        
        // Seleccionar el informe
        $sql_info = "SELECT  
                        info_resumen.proyecto,
                        info_resumen.zona,
                        info_resumen.desde,
                        info_resumen.hasta
                     FROM 
                        info_resumen
                     WHERE
                        info_resumen.id = $informe";
        
        $query_info = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_info);
        
        // Guardar la configuracion del informe
        $proyecto = $query_info->fields[0];
        $zona     = $query_info->fields[1];
        $desde    = $query_info->fields[2];
        $hasta    = $query_info->fields[3];

        // Recorrer los problemas y obtener los datos del informe
        $sql_problema = "SELECT id,descripcion FROM gtia_problemas";
        $qry_problema = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_problema);
        
        while(!$qry_problema->EOF){
            
            $id_problema = $qry_problema->fields[0];
            $problema    = $qry_problema->fields[1];
            
            // Obtener el total de SD por problema
            $sql_totalsd = "SELECT
                                COUNT(id) AS ctdad_sd
                            FROM
                                gtia_sd
                            WHERE
                                gtia_sd.estado = 'Por Resolver' AND
                                gtia_sd.proyecto = '$proyecto' AND
                                gtia_sd.id_problema = $id_problema";
            
            if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
                $sql_totalsd .= " AND (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta')";
            }
            
            if (strstr($zona, 'Todas') == false) {
                
                $zonas_array  = explode(', ', $zona);
                $sql_totalsd .= " AND (";
                $count        = 0;
                
                for($i = 0; $i < count($zonas_array); $i++){
                    $count++;
                    $zona_item = $zonas_array[$i];
                    if($count == 1){                    
                        $sql_totalsd .= "gtia_sd.zona = '$zona_item'";
                    }
                    else{
                        $sql_totalsd .= " OR gtia_sd.zona = '$zona_item'";
                    }
                }
                $sql_totalsd   .= ")";                
            }
                        
            $qry_totalsd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_totalsd);
            
            if($qry_totalsd->fields[0] > 0){
                
                $total_sd = $qry_totalsd->fields[0];
            
                // Seleccionar las SD agrupadas
                $sql_sdgroup = "SELECT
                                DISTINCT gtia_sd.descripcion
                             FROM
                                gtia_sd
                             WHERE
                                gtia_sd.estado = 'Por Resolver' AND 
                                gtia_sd.proyecto = '$proyecto' AND
                                gtia_sd.id_problema = $id_problema";
                                
                if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
                    $sql_sdgroup .= " AND (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta')";
                }
                
                if (strstr($zona, 'Todas') == false) {
                    
                    $zonas_array  = explode(', ', $zona);
                    $sql_sdgroup .= " AND (";
                    $count        = 0;
                    
                    for($i = 0; $i < count($zonas_array); $i++){
                        $count++;
                        $zona_item = $zonas_array[$i];
                        if($count == 1){                    
                            $sql_sdgroup .= "gtia_sd.zona = '$zona_item'";
                        }
                        else{
                            $sql_sdgroup .= " OR gtia_sd.zona = '$zona_item'";
                        }
                    }
                    $sql_sdgroup   .= ")";                
                }
                
                $qry_sdgroup = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_sdgroup);            
                $sd = '';
                $count_sd = 0;
                
                if($qry_sdgroup->RecordCount() > 0){
                    
                    while(!$qry_sdgroup->EOF){
                        
                        $count_sd++;
                        $sd_descripcion = $qry_sdgroup->fields[0];
                        
                        // Seleccionar las SD y sus datos
                        $sql_sd  =  "SELECT
                                        gtia_sd.zona,
                                        gtia_sd.objeto_local,
                                        gtia_sd.id
                                    FROM
                                        gtia_sd
                                    WHERE
                                        gtia_sd.proyecto = '$proyecto' AND
                                        gtia_sd.estado = 'Por Resolver' AND
                                        gtia_sd.descripcion = '$sd_descripcion' AND
                                        gtia_sd.id_problema = $id_problema";
                        
                        if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
                            $sql_sd .= " AND (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta')";
                        }
                        
                        if (strstr($zona, 'Todas') == false) {
                            
                            $zonas_array = explode(', ', $zona);
                            $sql_sd     .= " AND (";
                            $count       = 0;
                            
                            for($i = 0; $i < count($zonas_array); $i++){
                                $count++;
                                $zona_item = $zonas_array[$i];
                                if($count == 1){                    
                                    $sql_sd .= "gtia_sd.zona = '$zona_item'";
                                }
                                else{
                                    $sql_sd .= " OR gtia_sd.zona = '$zona_item'";
                                }
                            }
                            $sql_sd   .= ")";                
                        }
                        
                        $qry_sd   = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_sd);
                        $ctdad_sd = $qry_sd->RecordCount();
                        $sd .= $count_sd.' '.$sd_descripcion.'('.$ctdad_sd.') * ';
                        $sd_count   = 0;
                        $sd_zonas   = '';
                        $sd_objetos = '';
                        $sd_locales = '';
                        $dptos      = '';
                        
                        while(!$qry_sd->EOF){
                                    
                            // Definir las zonas
                            if (strstr($sd_zonas, $qry_sd->fields[0]) == false) {
                                $sd_zonas .= ', '.$qry_sd->fields[0];
                            }
                            
                            // Definir los objetos y partes
                            $field_objetos = explode(',',$qry_sd->fields[1]);
                            for($i = 0; $i < count($field_objetos); $i++){
                                $field_objetoparte = explode('(',$field_objetos[$i]);
                                $field_objetoname  = trim($field_objetoparte[0]);
                                // Objetos
                                if (strstr($field_objetos[$i],'BW') == true) {
                                    if (strstr($sd_objetos,'BWs') == false) {
                                        $sd_objetos .= ', BWs';
                                    }
                                    if (strstr($sd_locales,'Habitaciones') == false) {
                                        $sd_locales .= ', Habitaciones';
                                    }
                                }
                                else{                                    
                                    if (strstr($sd_objetos,$field_objetoname) == false) {
                                        $sd_objetos .= ', '.$field_objetoname;
                                    }                                    
                                }
                                // Locales
                                if(count($field_objetoparte) > 1){
                                    $debug_objetoparte = substr($field_objetoparte[1],0,strpos($field_objetoparte[1],')'));
                                    $field_locales = explode(',',$debug_objetoparte);
                                    for($j = 0; $j < count($field_locales); $j++){
                                        if (is_numeric(trim($field_locales[$j]))) {
                                            if (strstr($sd_locales,'Habitaciones') == false) {
                                                $sd_locales .= ', Habitaciones';
                                            }
                                        }
                                        else{                                    
                                            if (strstr($sd_locales,trim($field_locales[$j])) == false) {
                                                $sd_locales .= ', '.trim($field_locales[$j]);
                                            }                                    
                                        }    
                                    }
                                }
                            }
                                                        
                            // Definir los dptos
                            $id_sd = $qry_sd->fields[2];
                            $sql_dptos = "SELECT
                                            gtia_dptos.nombre
                                          FROM
                                            gtia_sd_dpto,
                                            gtia_dptos
                                          WHERE
                                            gtia_sd_dpto.id_sd = $id_sd AND
                                            gtia_sd_dpto.id_dpto = gtia_dptos.id";
                            $qry_dptos = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_dptos);
                            
                            while(!$qry_dptos->EOF) {
                                if (strstr($dptos, $qry_dptos->fields[0]) == false) {
                                    $dptos .= ', '.$qry_dptos->fields[0];
                                }
                                $qry_dptos->MoveNext();
                            }
                            
                                                        
                            $qry_sd->MoveNext();
                        }
                        $qry_sd->Close();                        
                        
                        $data_problema    = $problema.' ('.$total_sd.' SD)';
                        $data_descripcion = $sd_descripcion.' ('.$ctdad_sd.' SD)';
                        $data_zonas       = substr($sd_zonas,2);
                        $data_objetos     = substr($sd_objetos,2);
                        $data_locales     = substr($sd_locales,2);
                        $data_dpto        = substr($dptos,2);  
                        
                        $sql_renglon =  "SELECT id FROM info_resumendata_sdpendientes WHERE id_resumen = $informe AND id_problema = $id_problema AND descrip_sd = '$sd_descripcion'";
                                        
                        $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
                        
                        if($qry_renglon->RecordCount() > 0){
                            // Actualizar Renglon
                            $sql_renglonUpd  =  "UPDATE
                                                    info_resumendata_sdpendientes
                                                SET
                                                    problema_sd = '$data_problema',
                                                    descripcion = '$data_descripcion',
                                                    zonas = '$data_zonas',
                                                    objetos = '$data_objetos',
                                                    locales = '$data_locales',
                                                    dpto = '$data_dpto',
                                                    fechamod = '$fecha_update'
                                                WHERE
                                                    id_resumen = $informe AND
                                                    id_problema = $id_problema AND
                                                    descrip_sd = '$sd_descripcion'";
                                                    
                            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
                        }
                        else{
                            // Insertar Renglon
                            $sql_renglonAdd  =  "INSERT INTO
                                                    info_resumendata_sdpendientes(
                                                        id_resumen,
                                                        problema_sd,
                                                        descripcion,
                                                        objetos,
                                                        locales,
                                                        dpto,
                                                        zonas,
                                                        fechamod,
                                                        id_problema,
                                                        descrip_sd
                                                    )
                                                VALUES(
                                                    $informe,
                                                    '$data_problema',
                                                    '$data_descripcion',
                                                    '$data_objetos',
                                                    '$data_locales',
                                                    '$data_dpto',
                                                    '$data_zonas',
                                                    '$fecha_update',
                                                    $id_problema,
                                                    '$sd_descripcion'
                                                )";
                                               
                            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
                        }
                        
                        $qry_sdgroup->MoveNext();
                    }
                    $qry_sdgroup->Close();
                }
            }                       
            $qry_problema->MoveNext();
        }
        $qry_problema->Close();
                
        // Eliminar registro de datos del informe
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM info_resumendata_sdpendientes WHERE id_resumen = $informe AND fechamod != '$fecha_update'");
            
        // Cargar los datos del informe
        $sql_infodata = "SELECT  
                            info_resumendata_sdpendientes.id,
                            info_resumendata_sdpendientes.problema_sd,
                            info_resumendata_sdpendientes.descripcion,
                            info_resumendata_sdpendientes.zonas,
                            info_resumendata_sdpendientes.objetos,
                            info_resumendata_sdpendientes.locales,
                            info_resumendata_sdpendientes.dpto,
                            info_resumendata_sdpendientes.comentario,
                            info_resumendata_sdpendientes.fechamod
                         FROM 
                            info_resumendata_sdpendientes
                         WHERE
                            id_resumen = $informe";

        $query_infodata = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_infodata);
        
        if ($query_infodata->RecordCount() > 0) {

            $count = 0;
            while (!$query_infodata->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_resumen": "' . $informe . '",
                                    "problema_sd": "' . utf8_encode($query_infodata->fields[1]) . '",
                                    "descripcion": "' . utf8_encode($query_infodata->fields[2]) . '",
                                    "zonas": "' . $query_infodata->fields[3] . '",
                                    "objetos": "' . utf8_encode($query_infodata->fields[4]) . '",
                                    "locales": "' . utf8_encode($query_infodata->fields[5]) . '",
                                    "dpto": "' . utf8_encode($query_infodata->fields[6]) . '",
                                    "comentario": "' . utf8_encode($query_infodata->fields[7]) . '",
                                    "fechamod": "' . $query_infodata->fields[8] . '"
                                  }';
                } else {
                    $response .= ',{
                                     "id": "' . $query_infodata->fields[0] . '",
                                     "id_resumen": "' . $informe . '",
                                     "problema_sd": "' . utf8_encode($query_infodata->fields[1]) . '",
                                     "descripcion": "' . utf8_encode($query_infodata->fields[2]) . '",
                                     "zonas": "' . $query_infodata->fields[3] . '",
                                     "objetos": "' . utf8_encode($query_infodata->fields[4]) . '",
                                     "locales": "' . utf8_encode($query_infodata->fields[5]) . '",
                                     "dpto": "' . utf8_encode($query_infodata->fields[6]) . '",
                                     "comentario": "' . utf8_encode($query_infodata->fields[7]) . '",
                                     "fechamod": "' . $query_infodata->fields[8] . '"
                                   }';
                }
                
                $query_infodata->MoveNext();
            }
            $query_infodata->Close();
        }

        $response .= ']}';

        return $response;
    }
    
       
    // Obtener los datos de la Pestaña Repetitividad
    function loadResumenDataRepetitividad($informe){
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");
        
        // Construir el JSON de SD Pendientes
        $response = '{"success": true, "repetitividad": [';
        
        // Seleccionar el informe
        $sql_info = "SELECT  
                        info_resumen.proyecto,
                        info_resumen.zona,
                        info_resumen.desde,
                        info_resumen.hasta
                     FROM 
                        info_resumen
                     WHERE
                        info_resumen.id = $informe";
        
        $query_info = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_info);
        
        // Guardar la configuracion del informe
        $proyecto = $query_info->fields[0];
        $zona     = $query_info->fields[1];
        $desde    = $query_info->fields[2];
        $hasta    = $query_info->fields[3];

        // Recorrer los problemas y obtener los datos del informe
        $sql_problema = "SELECT id,descripcion FROM gtia_problemas";
        $qry_problema = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_problema);
        
        $repetitividad = array();
        $descripcion   = array();
        
        while(!$qry_problema->EOF){
            
            $id_problema = $qry_problema->fields[0];
            $problema_desc = $qry_problema->fields[1];
            
            // Obtener el total de SD por problema
            $sql_totalsd = "SELECT
                                COUNT(id) AS ctdad_sd
                            FROM
                                gtia_sd
                            WHERE
                                gtia_sd.estado != 'No Procede' AND
                                gtia_sd.proyecto = '$proyecto' AND
                                gtia_sd.id_problema = $id_problema";
            
            if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
                $sql_totalsd .= " AND (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta')";
            }
            
            if (strstr($zona, 'Todas') == false) {
                
                $zonas_array  = explode(', ', $zona);
                $sql_totalsd .= " AND (";
                $count        = 0;
                
                for($i = 0; $i < count($zonas_array); $i++){
                    $count++;
                    $zona_item = $zonas_array[$i];
                    if($count == 1){                    
                        $sql_totalsd .= "gtia_sd.zona = '$zona_item'";
                    }
                    else{
                        $sql_totalsd .= " OR gtia_sd.zona = '$zona_item'";
                    }
                }
                $sql_totalsd   .= ")";                
            }
                        
            $qry_totalsd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_totalsd);
            
            // Agregar la repetitividad y descripcion del problema a sus arreglos
            $repetitividad[$id_problema] = $qry_totalsd->fields[0];
            $descripcion[$id_problema]   = $problema_desc;
                             
            $qry_problema->MoveNext();
        }
        $qry_problema->Close();
        
        // Ordenar Descendentemente el arreglo de repetitividad
        arsort($repetitividad);
        
        $count_problema = 0;
        
        // Seleccionnar los cinco primeros y recorrerlos
        foreach($repetitividad as $problema=>$ctdad)
        {
            $count_problema++;
            if($count_problema <= 5){
                
                if($ctdad > 0){
                                
                    // Descripcion del problema
                    $problema_descripcion = $descripcion[$problema].' ('.$ctdad.')';
                         
                    // Seleccionar las SD agrupadas
                    $sql_sdgroup = "SELECT
                                        DISTINCT gtia_sd.descripcion
                                    FROM
                                        gtia_sd
                                    WHERE
                                        gtia_sd.estado != 'No Procede' AND 
                                        gtia_sd.proyecto = '$proyecto' AND
                                        gtia_sd.id_problema = $problema";
                                        
                    if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
                        $sql_sdgroup .= " AND (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta')";
                    }
                    
                    if (strstr($zona, 'Todas') == false) {
                        
                        $zonas_array  = explode(', ', $zona);
                        $sql_sdgroup .= " AND (";
                        $count        = 0;
                        
                        for($i = 0; $i < count($zonas_array); $i++){
                            $count++;
                            $zona_item = $zonas_array[$i];
                            if($count == 1){                    
                                $sql_sdgroup .= "gtia_sd.zona = '$zona_item'";
                            }
                            else{
                                $sql_sdgroup .= " OR gtia_sd.zona = '$zona_item'";
                            }
                        }
                        $sql_sdgroup   .= ")";                
                    }
                    
                    $qry_sdgroup = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_sdgroup);            
                    $count_sd = 0;
                    
                    if($qry_sdgroup->RecordCount() > 0){
                        
                        while(!$qry_sdgroup->EOF){
                            
                            $count_sd++;
                            $sd_descripcion = $qry_sdgroup->fields[0];
                                                                          
                            // Seleccionar las SD y sus datos
                            $sql_sd  =  "SELECT
                                            gtia_sd.id
                                        FROM
                                            gtia_sd
                                        WHERE
                                            gtia_sd.proyecto = '$proyecto' AND
                                            gtia_sd.estado != 'No Procede' AND
                                            gtia_sd.descripcion = '$sd_descripcion' AND
                                            gtia_sd.id_problema = $problema";
                            
                            if($desde != '1900-01-01' AND $hasta != '1900-01-01'){
                                $sql_sd .= " AND (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta')";
                            }
                            
                            if (strstr($zona, 'Todas') == false) {
                                
                                $zonas_array = explode(', ', $zona);
                                $sql_sd     .= " AND (";
                                $count       = 0;
                                
                                for($i = 0; $i < count($zonas_array); $i++){
                                    $count++;
                                    $zona_item = $zonas_array[$i];
                                    if($count == 1){                    
                                        $sql_sd .= "gtia_sd.zona = '$zona_item'";
                                    }
                                    else{
                                        $sql_sd .= " OR gtia_sd.zona = '$zona_item'";
                                    }
                                }
                                $sql_sd   .= ")";                
                            }
                            
                            $qry_sd   = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_sd);
                            $ctdad_sd = $qry_sd->RecordCount();
                            
                            //  Actualizar/Insertar el renglon
                            $sql_renglon =  "SELECT id FROM info_resumendata_repetitividad WHERE id_resumen = $informe AND id_problema = $problema AND sd_descripcion = '$sd_descripcion'";
                            $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
                            
                            if($qry_renglon->RecordCount() > 0){
                                // Actualizar Renglon
                                $sql_renglonUpd  =  "UPDATE
                                                        info_resumendata_repetitividad
                                                    SET
                                                        problema_descripcion = '$problema_descripcion',
                                                        sd_ctdad = $ctdad_sd,
                                                        fechamod = '$fecha_update'
                                                    WHERE
                                                        id_resumen = $informe AND
                                                        id_problema = $problema AND
                                                        sd_descripcion = '$sd_descripcion'";
                                                        
                                $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
                            }
                            else{
                                // Insertar Renglon
                                $sql_renglonAdd  =  "INSERT INTO
                                                        info_resumendata_repetitividad(
                                                            id_resumen,
                                                            id_problema,
                                                            problema_descripcion,
                                                            sd_descripcion,
                                                            sd_ctdad,
                                                            fechamod
                                                        )
                                                    VALUES(
                                                        $informe,
                                                        $problema,
                                                        '$problema_descripcion',
                                                        '$sd_descripcion',
                                                        $ctdad_sd,
                                                        '$fecha_update'
                                                    )";
                                                   
                                $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
                            }  
                            
                            $qry_sdgroup->MoveNext();
                        }
                        $qry_sdgroup->Close();
                    }
                    
                    
                    
                    
                    
                }    
            }
        }
        
                  
        // Eliminar registro de datos del informe
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM info_resumendata_repetitividad WHERE id_resumen = $informe AND fechamod != '$fecha_update'");
            
        // Cargar los datos del informe
        $sql_infodata = "SELECT  
                            info_resumendata_repetitividad.id,
                            info_resumendata_repetitividad.problema_descripcion,
                            info_resumendata_repetitividad.sd_descripcion,
                            info_resumendata_repetitividad.sd_ctdad,
                            info_resumendata_repetitividad.comentario,
                            info_resumendata_repetitividad.fechamod
                         FROM 
                            info_resumendata_repetitividad
                         WHERE
                            id_resumen = $informe";

        $query_infodata = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_infodata);
        
        if ($query_infodata->RecordCount() > 0) {

            $count = 0;
            while (!$query_infodata->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_resumen": "' . $informe . '",
                                    "problema_descripcion": "' . utf8_encode($query_infodata->fields[1]) . '",
                                    "sd_descripcion": "' . utf8_encode($query_infodata->fields[2]) . '",
                                    "sd_ctdad": "' . $query_infodata->fields[3] . '",
                                    "comentario": "' . utf8_encode($query_infodata->fields[4]) . '",
                                    "fechamod": "' . $query_infodata->fields[5] . '"
                                  }';
                } else {
                    $response .= ',{
                                     "id": "' . $query_infodata->fields[0] . '",
                                     "id_resumen": "' . $informe . '",
                                     "problema_descripcion": "' . utf8_encode($query_infodata->fields[1]) . '",
                                     "sd_descripcion": "' . utf8_encode($query_infodata->fields[2]) . '",
                                     "sd_ctdad": "' . $query_infodata->fields[3] . '",
                                     "comentario": "' . utf8_encode($query_infodata->fields[4]) . '",
                                     "fechamod": "' . $query_infodata->fields[5] . '"
                                   }';
                }
                
                $query_infodata->MoveNext();
            }
            $query_infodata->Close();
        }

        $response .= ']}';

        return $response;
    }
    
      
    // Obtener los datos de la pestaña Indicadores Principales
    function loadResumenDataPIndicadores($informe){
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");
        
        // Construir el JSON de HFOdata
        $response = '{"success": true, "pindicadores": [';

        // Seleccionar el informe
        $sql_info = "SELECT  
                        info_resumen.proyecto,
                        info_resumen.zona,
                        info_resumen.desde,
                        info_resumen.hasta
                     FROM 
                        info_resumen
                     WHERE
                        info_resumen.id = $informe";
        
        $query_info = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_info);
        
        // Guardar la configuracion del informe
        $proyecto = $query_info->fields[0];
        $zona     = $query_info->fields[1];
        $desde    = $query_info->fields[2];
        $hasta    = $query_info->fields[3];
        
        // Determniar los rangos de fecha del informe
        
        // Determinar las fechas mayor y menor
        $fecha_mayor = '';
        $fecha_menor = '';
        
        if ($desde == '1900-01-01' || $desde == '' || $desde == null) {
            $qry_desde = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT fecha_inicio FROM gtia_proyectos WHERE nombre = '$proyecto'");
            $desde  = $qry_desde->fields[0];
        }
        if ($hasta == '1900-01-01' || $hasta == '' || $hasta == null) {
            $hasta  = date('Y-m-d');
        }

        if(str_replace('-','',$desde) < str_replace('-','',$hasta)){ $fecha_mayor = $hasta; $fecha_menor = $desde; }
        elseif(str_replace('-','',$desde) > str_replace('-','',$hasta)){ $fecha_mayor = $desde; $fecha_menor = $hasta; }
        else{ $fecha_mayor = $hasta; $fecha_menor = $desde; }
        
        // Días que comprende el rango
        $dias = $GLOBALS["cadenas"]->dias_entre_fechas($fecha_menor,$fecha_mayor);
        
        // Si es un dia
        if($dias == 0){
            $periodo_ant_desde = date('Y-m-d',strtotime($desde." -1 days"));
            $periodo_ant_hasta = '';
            $periodo_act_desde = $desde;
            $periodo_act_hasta = '';
            $acumulado_fecha   = $desde;
        }
        // Si es una semana
        elseif($dias == 7){
            $periodo_ant_desde = date('Y-m-d',strtotime($fecha_menor." -8 days"));
            $periodo_ant_hasta = date('Y-m-d',strtotime($fecha_mayor." -1 days"));
            $periodo_act_desde = $fecha_menor;
            $periodo_act_hasta = $fecha_mayor;
            $acumulado_fecha   = $fecha_mayor;
        }
        // Si es un mes
        elseif($dias >= 28 && $dias <= 31){
            $primerdia_mes = date('Y-m-d',strtotime('first day of this month '.$fecha_menor));
            $ultimodia_mes = date('Y-m-d',strtotime('last day of this month '.$fecha_mayor));
            if($primerdia_mes == $fecha_menor && $ultimodia_mes == $fecha_mayor){
                $periodo_ant_desde = date('Y-m-d',strtotime($primerdia_mes." -1 month"));
                $periodo_ant_hasta = date('Y-m-d',strtotime('last day of this month '.$periodo_ant_desde));
                $periodo_act_desde = $primerdia_mes;
                $periodo_act_hasta = $ultimodia_mes;
                $acumulado_fecha   = $ultimodia_mes;
            }
        }
        // Si es un año
        elseif($dias >= 365 && $dias <= 366){
            $primerdia_anio = date('Y-m-d',strtotime('first day of January '.date('Y',strtotime($fecha_menor))));
            $ultimodia_anio = date('Y-m-d',strtotime('last day of December '.date('Y',strtotime($fecha_mayor))));
            if($primerdia_anio == $fecha_menor && $ultimodia_anio == $fecha_mayor){
                $periodo_ant_desde = date('Y-m-d',strtotime($primerdia_anio." -1 year"));
                $periodo_ant_hasta = date('Y-m-d',strtotime('last day of +11 month '.$periodo_ant_desde));
                $periodo_act_desde = $primerdia_anio;
                $periodo_act_hasta = $ultimodia_anio;
                $acumulado_fecha   = $ultimodia_anio;
            }
        }
        // Si es un rango de fechas
        else{
            $mes_desde     = date('n',strtotime('first day of this month '.$fecha_menor));
            $mes_hasta     = date('n',strtotime('last day of this month '.$fecha_mayor));
            $primerdia_mes = date('Y-m-d',strtotime('first day of this month '.$fecha_menor));
            $ultimodia_mes = date('Y-m-d',strtotime('last day of this month '.$fecha_mayor));
            $meses         = $mes_hasta - $mes_desde;
            if($primerdia_mes == $fecha_menor && $ultimodia_mes == $fecha_mayor && $meses > 0){
                $meses++;
                $periodo_ant_desde = date('Y-m-d',strtotime($fecha_menor." -".$meses." months"));
                $periodo_ant_hasta = date('Y-m-d',strtotime($fecha_mayor." -".$meses." months"));
                $periodo_act_desde = $fecha_menor;
                $periodo_act_hasta = $fecha_mayor;
                $acumulado_fecha   = $fecha_mayor;
            }
            else{
                $periodo_ant_desde = date('Y-m-d',strtotime($fecha_menor." -".$dias." days"));
                $periodo_ant_hasta = date('Y-m-d',strtotime($fecha_mayor." -".$dias." days"));
                $periodo_act_desde = $fecha_menor;
                $periodo_act_hasta = $fecha_mayor;
                $acumulado_fecha   = $fecha_mayor;
            }
        }
                
        // Obtener los datos de cada Indicador
        
        /********************************************************
         ***  Demora Promedio en SD Const, No AEH y No Sumin  ***
         ********************************************************/
        
        // Periodo Anterior
        $sql_periodo_ant = "SELECT
                                fecha_reporte, fecha_solucion, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                constructiva = 'Si' AND
                                suministro = 'No' AND 
                                afecta_explotacion = 'No' AND
                                (fecha_reporte >= '$periodo_ant_desde' AND fecha_reporte <= '$periodo_ant_hasta')";
    
        $qry_periodo_ant = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_ant);

        $sum_periodo_ant  = 0;
        $cont_periodo_ant = 0;
    
        if($qry_periodo_ant){
            while (!$qry_periodo_ant->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_periodo_ant->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_ant->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_ant->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_ant->fields[2];
                $fecha_almacen = $qry_periodo_ant->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_ant->fields[1] != '' && $qry_periodo_ant->fields[1] != null && $qry_periodo_ant->fields[1] == '1900-01-01') ? $qry_periodo_ant->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_ant->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_ant->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_ant += $calculo_demora;
                $cont_periodo_ant++;
                
                $qry_periodo_ant->MoveNext();
            }
            $qry_periodo_ant->Close();
        }
    
        if ($sum_periodo_ant != 0 && $cont_periodo_ant != 0) {
            $demora_periodo_ant = $sum_periodo_ant / $cont_periodo_ant;
        } else {
            $demora_periodo_ant = 0;
        }
        $demora_periodo_ant = number_format($demora_periodo_ant,0). ' Dias';
        
        // Periodo Actual
        $sql_periodo_act = "SELECT
                                fecha_reporte, fecha_solucion, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                constructiva = 'Si' AND
                                suministro = 'No' AND 
                                afecta_explotacion = 'No' AND
                                (fecha_reporte >= '$periodo_act_desde' AND fecha_reporte <= '$periodo_act_hasta')";
    
        $qry_periodo_act = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_act);

        $sum_periodo_act  = 0;
        $cont_periodo_act = 0;
    
        if($qry_periodo_act){
            while (!$qry_periodo_act->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_periodo_act->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_act->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_act->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_act += $calculo_demora;
                $cont_periodo_act++;
                
                $qry_periodo_act->MoveNext();
            }
            $qry_periodo_act->Close();
        }
    
        if ($sum_periodo_act != 0 && $cont_periodo_act != 0) {
            $demora_periodo_act = $sum_periodo_act / $cont_periodo_act;
        } else {
            $demora_periodo_act = 0;
        }
        $demora_periodo_act = number_format($demora_periodo_act,0). ' Dias';
        $demora_periodo_act_number = number_format($demora_periodo_act,0);
        
        // Acumulado
        $sql_acum = "SELECT
                        fecha_reporte, fecha_solucion, suministro, fecha_almacen
                    FROM
                        gtia_sd
                    WHERE
                        proyecto = '$proyecto' AND
                        constructiva = 'Si' AND
                        suministro = 'No' AND 
                        afecta_explotacion = 'No' AND
                        fecha_reporte <= '$acumulado_fecha'";
    
        $qry_acum = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_acum);

        $sum_acum  = 0;
        $cont_acum = 0;
    
        if($qry_acum){
            while (!$qry_acum->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_acum->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_acum->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_acum->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_acum += $calculo_demora;
                $cont_acum++;
                
                $qry_acum->MoveNext();
            }
            $qry_acum->Close();
        }
    
        if ($sum_acum != 0 && $cont_acum != 0) {
            $demora_acum = $sum_acum / $cont_acum;
        } else {
            $demora_acum = 0;
        }
        $demora_acum = number_format($demora_acum,0). ' Dias';
        
        // Meta
        $sql_meta = "SELECT
                        id,meta
                    FROM
                        gtia_indicadores
                    WHERE
                        nombre = 'Demora Promedio en SD Const, No AEH y No Sumin'";
    
        $qry_meta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_meta);
        $indicador = $qry_meta->fields[0]; 
        $textmeta  = $qry_meta->fields[1];
        $metarray  = explode(' ',$textmeta);
        $meta      = $metarray[1]; 
        
        // Estado
        if($demora_periodo_act_number <= intval($meta)){ $estado = 'Bien'; }else{ $estado = 'Mal'; }
        
        // Tendencia
        $dif_acum_meta = $demora_acum - $meta;
        $dif_pant_meta = $demora_periodo_ant - $meta;
        if($dif_acum_meta < $dif_pant_meta){ $tendencia = 'asc'; }
        elseif($dif_acum_meta == $dif_pant_meta){ $tendencia = 'const'; }
        elseif($dif_acum_meta > $dif_pant_meta){ $tendencia = 'desc'; }
        
        // Actualizar/Insertar el renglon en los datos del informe
        $sql_renglon = "SELECT id FROM info_resumendata_pindicadores WHERE id_resumen = $informe AND id_indicador = $indicador";
        $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
        if($qry_renglon->RecordCount() > 0){
            // Actualizar Renglon
            $sql_renglonUpd = "UPDATE
                                  info_resumendata_pindicadores
                               SET
                                  periodo_ant = '$demora_periodo_ant',
                                  periodo_act = '$demora_periodo_act',
                                  acumulado = '$demora_acum',
                                  estado = '$estado',
                                  tendencia = '$tendencia',
                                  fechamod = '$fecha_update'
                               WHERE
                                  id_resumen = $informe AND
                                  id_indicador = $indicador";
            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
        }
        else{
            // Insertar Renglon
            $sql_renglonAdd = "INSERT INTO
                                  info_resumendata_pindicadores(
                                      id_resumen,
                                      id_indicador,
                                      periodo_ant,
                                      periodo_act,
                                      acumulado,
                                      estado,
                                      tendencia,
                                      fechamod
                                  )
                               VALUES(
                                  $informe,
                                  $indicador,
                                  '$demora_periodo_ant',
                                  '$demora_periodo_act',
                                  '$demora_acum',
                                  '$estado',
                                  '$tendencia',
                                  '$fecha_update'
                               )";
            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
        }

        
        /*****************************************************
         ***  Demora Promedio en SD Const, AEH y No Sumin  ***
         *****************************************************/
        
        // Periodo Anterior
        $sql_periodo_ant = "SELECT
                                fecha_reporte, fecha_solucion, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                constructiva = 'Si' AND
                                suministro = 'No' AND 
                                afecta_explotacion = 'Si' AND
                                (fecha_reporte >= '$periodo_ant_desde' AND fecha_reporte <= '$periodo_ant_hasta')";
    
        $qry_periodo_ant = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_ant);

        $sum_periodo_ant  = 0;
        $cont_periodo_ant = 0;
    
        if($qry_periodo_ant){
            while (!$qry_periodo_ant->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_periodo_ant->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_ant->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_ant->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_ant += $calculo_demora;
                $cont_periodo_ant++;
                
                $qry_periodo_ant->MoveNext();
            }
            $qry_periodo_ant->Close();
        }
    
        if ($sum_periodo_ant != 0 && $cont_periodo_ant != 0) {
            $demora_periodo_ant = $sum_periodo_ant / $cont_periodo_ant;
        } else {
            $demora_periodo_ant = 0;
        }
        $demora_periodo_ant = number_format($demora_periodo_ant,0). ' Dias';
        
        // Periodo Actual
        $sql_periodo_act = "SELECT
                                fecha_reporte, fecha_solucion, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                constructiva = 'Si' AND
                                suministro = 'No' AND 
                                afecta_explotacion = 'Si' AND
                                (fecha_reporte >= '$periodo_act_desde' AND fecha_reporte <= '$periodo_act_hasta')";
    
        $qry_periodo_act = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_act);

        $sum_periodo_act  = 0;
        $cont_periodo_act = 0;
    
        if($qry_periodo_act){
            while (!$qry_periodo_act->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_periodo_act->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_act->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_act->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_act += $calculo_demora;
                $cont_periodo_act++;
                
                $qry_periodo_act->MoveNext();
            }
            $qry_periodo_act->Close();
        }
    
        if ($sum_periodo_act != 0 && $cont_periodo_act != 0) {
            $demora_periodo_act = $sum_periodo_act / $cont_periodo_act;
        } else {
            $demora_periodo_act = 0;
        }
        $demora_periodo_act = number_format($demora_periodo_act,0). ' Dias';
        $demora_periodo_act_number = number_format($demora_periodo_act,0);
        
        // Acumulado
        $sql_acum = "SELECT
                        fecha_reporte, fecha_solucion, suministro, fecha_almacen
                    FROM
                        gtia_sd
                    WHERE
                        proyecto = '$proyecto' AND
                        constructiva = 'Si' AND
                        suministro = 'No' AND 
                        afecta_explotacion = 'Si' AND
                        fecha_reporte <= '$acumulado_fecha'";
    
        $qry_acum = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_acum);

        $sum_acum  = 0;
        $cont_acum = 0;
    
        if($qry_acum){
            while (!$qry_acum->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_acum->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_acum->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_acum->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_acum += $calculo_demora;
                $cont_acum++;
                
                $qry_acum->MoveNext();
            }
            $qry_acum->Close();
        }
    
        if ($sum_acum != 0 && $cont_acum != 0) {
            $demora_acum = $sum_acum / $cont_acum;
        } else {
            $demora_acum = 0;
        }
        $demora_acum = number_format($demora_acum,0). ' Dias';
        
        // Meta
        $sql_meta = "SELECT
                        id,meta
                    FROM
                        gtia_indicadores
                    WHERE
                        nombre = 'Demora Promedio en SD Const, AEH y No Sumin'";
    
        $qry_meta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_meta);
        $indicador = $qry_meta->fields[0]; 
        $textmeta  = $qry_meta->fields[1];
        $metarray  = explode(' ',$textmeta);
        $meta      = $metarray[1];
        
        // Estado
        if($demora_periodo_act_number <= intval($meta)){ $estado = 'Bien'; }else{ $estado = 'Mal'; }
        
        // Tendencia
        $dif_acum_meta = $demora_acum - $meta;
        $dif_pant_meta = $demora_periodo_ant - $meta;
        if($dif_acum_meta < $dif_pant_meta){ $tendencia = 'asc'; }
        elseif($dif_acum_meta == $dif_pant_meta){ $tendencia = 'const'; }
        elseif($dif_acum_meta > $dif_pant_meta){ $tendencia = 'desc'; }
        
        // Actualizar/Insertar el renglon en los datos del informe
        $sql_renglon = "SELECT id FROM info_resumendata_pindicadores WHERE id_resumen = $informe AND id_indicador = $indicador";
        $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
        if($qry_renglon->RecordCount() > 0){
            // Actualizar Renglon
            $sql_renglonUpd = "UPDATE
                                  info_resumendata_pindicadores
                               SET
                                  periodo_ant = '$demora_periodo_ant',
                                  periodo_act = '$demora_periodo_act',
                                  acumulado = '$demora_acum',
                                  estado = '$estado',
                                  tendencia = '$tendencia',
                                  fechamod = '$fecha_update'
                               WHERE
                                  id_resumen = $informe AND
                                  id_indicador = $indicador";
            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
        }
        else{
            // Insertar Renglon
            $sql_renglonAdd = "INSERT INTO
                                  info_resumendata_pindicadores(
                                      id_resumen,
                                      id_indicador,
                                      periodo_ant,
                                      periodo_act,
                                      acumulado,
                                      estado,
                                      tendencia,
                                      fechamod
                                  )
                               VALUES(
                                  $informe,
                                  $indicador,
                                  '$demora_periodo_ant',
                                  '$demora_periodo_act',
                                  '$demora_acum',
                                  '$estado',
                                  '$tendencia',
                                  '$fecha_update'
                               )";
            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
        }
        
        
        /***********************************
         ***  Demora Promedio en SD AEH  ***
         ***********************************/
        
        // Periodo Anterior
        $sql_periodo_ant = "SELECT
                                fecha_reporte, fecha_solucion, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                afecta_explotacion = 'Si' AND
                                (fecha_reporte >= '$periodo_ant_desde' AND fecha_reporte <= '$periodo_ant_hasta')";
    
        $qry_periodo_ant = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_ant);

        $sum_periodo_ant  = 0;
        $cont_periodo_ant = 0;
    
        if($qry_periodo_ant){
            while (!$qry_periodo_ant->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_periodo_ant->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_ant->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_ant->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_ant += $calculo_demora;
                $cont_periodo_ant++;
                
                $qry_periodo_ant->MoveNext();
            }
            $qry_periodo_ant->Close();
        }
    
        if ($sum_periodo_ant != 0 && $cont_periodo_ant != 0) {
            $demora_periodo_ant = $sum_periodo_ant / $cont_periodo_ant;
        } else {
            $demora_periodo_ant = 0;
        }
        $demora_periodo_ant = number_format($demora_periodo_ant,0). ' Dias';
        
        // Periodo Actual
        $sql_periodo_act = "SELECT
                                fecha_reporte, fecha_solucion, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                afecta_explotacion = 'Si' AND
                                (fecha_reporte >= '$periodo_act_desde' AND fecha_reporte <= '$periodo_act_hasta')";
    
        $qry_periodo_act = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_act);

        $sum_periodo_act  = 0;
        $cont_periodo_act = 0;
    
        if($qry_periodo_act){
            while (!$qry_periodo_act->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_periodo_act->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_act->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_act->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/
                
                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_act += $calculo_demora;
                $cont_periodo_act++;
                
                $qry_periodo_act->MoveNext();
            }
            $qry_periodo_act->Close();
        }
    
        if ($sum_periodo_act != 0 && $cont_periodo_act != 0) {
            $demora_periodo_act = $sum_periodo_act / $cont_periodo_act;
        } else {
            $demora_periodo_act = 0;
        }
        $demora_periodo_act = number_format($demora_periodo_act,0). ' Dias';
        $demora_periodo_act_number = number_format($demora_periodo_act,0);
        
        // Acumulado
        $sql_acum = "SELECT
                        fecha_reporte, fecha_solucion, suministro, fecha_almacen
                    FROM
                        gtia_sd
                    WHERE
                        proyecto = '$proyecto' AND
                        afecta_explotacion = 'Si' AND
                        fecha_reporte <= '$acumulado_fecha'";
    
        $qry_acum = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_acum);

        $sum_acum  = 0;
        $cont_acum = 0;
    
        if($qry_acum){
            while (!$qry_acum->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_acum->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_acum->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_acum->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[2];
                $fecha_almacen = $qry_periodo_act->fields[3];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_acum += $calculo_demora;
                $cont_acum++;
                
                $qry_acum->MoveNext();
            }
            $qry_acum->Close();
        }
    
        if ($sum_acum != 0 && $cont_acum != 0) {
            $demora_acum = $sum_acum / $cont_acum;
        } else {
            $demora_acum = 0;
        }
        $demora_acum = number_format($demora_acum,0). ' Dias';
        
        // Meta
        $sql_meta = "SELECT
                        id,meta
                    FROM
                        gtia_indicadores
                    WHERE
                        nombre = 'Demora Promedio en SD AEH'";
    
        $qry_meta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_meta);
        $indicador = $qry_meta->fields[0]; 
        $textmeta  = $qry_meta->fields[1];
        $metarray  = explode(' ',$textmeta);
        $meta      = $metarray[1];
        
        // Estado
        if($demora_periodo_act_number <= intval($meta)){ $estado = 'Bien'; }else{ $estado = 'Mal'; }
        
        // Tendencia
        $dif_acum_meta = $demora_acum - $meta;
        $dif_pant_meta = $demora_periodo_ant - $meta;
        if($dif_acum_meta < $dif_pant_meta){ $tendencia = 'asc'; }
        elseif($dif_acum_meta == $dif_pant_meta){ $tendencia = 'const'; }
        elseif($dif_acum_meta > $dif_pant_meta){ $tendencia = 'desc'; }
        
        // Actualizar/Insertar el renglon en los datos del informe
        $sql_renglon = "SELECT id FROM info_resumendata_pindicadores WHERE id_resumen = $informe AND id_indicador = $indicador";
        $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
        if($qry_renglon->RecordCount() > 0){
            // Actualizar Renglon
            $sql_renglonUpd = "UPDATE
                                  info_resumendata_pindicadores
                               SET
                                  periodo_ant = '$demora_periodo_ant',
                                  periodo_act = '$demora_periodo_act',
                                  acumulado = '$demora_acum',
                                  estado = '$estado',
                                  tendencia = '$tendencia',
                                  fechamod = '$fecha_update'
                               WHERE
                                  id_resumen = $informe AND
                                  id_indicador = $indicador";
            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
        }
        else{
            // Insertar Renglon
            $sql_renglonAdd = "INSERT INTO
                                  info_resumendata_pindicadores(
                                      id_resumen,
                                      id_indicador,
                                      periodo_ant,
                                      periodo_act,
                                      acumulado,
                                      estado,
                                      tendencia,
                                      fechamod
                                  )
                               VALUES(
                                  $informe,
                                  $indicador,
                                  '$demora_periodo_ant',
                                  '$demora_periodo_act',
                                  '$demora_acum',
                                  '$estado',
                                  '$tendencia',
                                  '$fecha_update'
                               )";
            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
        }
        
        
        /*******************************************************************************
         ***  Habitaciones y Locales no conformes por problemas imputables a la AEI  ***
         *******************************************************************************/
        
        // Periodo Anterior
        $sql_periodo_ant = "SELECT
                                COUNT(id) AS ctdad
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                estado != 'No Procede' AND
                                (fecha_reporte >= '$periodo_ant_desde' AND fecha_reporte <= '$periodo_ant_hasta')";
    
        $qry_periodo_ant = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_ant);
        $periodo_ant     = $qry_periodo_ant->fields[0];
        
        // Periodo Actual
        $sql_periodo_act = "SELECT
                                COUNT(id) AS ctdad
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                estado != 'No Procede' AND
                                (fecha_reporte >= '$periodo_act_desde' AND fecha_reporte <= '$periodo_act_hasta')";
    
        $qry_periodo_act = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_act);
        $periodo_act     = $qry_periodo_act->fields[0];
        
        // Acumulado
        $sql_acum = "SELECT
                        COUNT(id) AS ctdad
                    FROM
                        gtia_sd
                    WHERE
                        proyecto = '$proyecto' AND
                        estado != 'No Procede' AND
                        fecha_reporte <= '$acumulado_fecha'";
    
        $qry_acum = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_acum);
        $acum     = $qry_acum->fields[0];
        
        // Meta
        $sql_meta = "SELECT
                        id,meta
                    FROM
                        gtia_indicadores
                    WHERE
                        nombre = 'Habitaciones y Locales no conformes por problemas imputables a la AEI'";
    
        $qry_meta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_meta);
        $indicador = $qry_meta->fields[0]; 
        $meta      = $qry_meta->fields[1];
        
        // Estado
        $estado = null;
        
        // Tendencia
        $tendencia = null;
        
        // Actualizar/Insertar el renglon en los datos del informe
        $sql_renglon = "SELECT id FROM info_resumendata_pindicadores WHERE id_resumen = $informe AND id_indicador = $indicador";
        $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
        if($qry_renglon->RecordCount() > 0){
            // Actualizar Renglon
            $sql_renglonUpd = "UPDATE
                                  info_resumendata_pindicadores
                               SET
                                  periodo_ant = '$periodo_ant',
                                  periodo_act = '$periodo_act',
                                  acumulado = '$acum',
                                  fechamod = '$fecha_update'
                               WHERE
                                  id_resumen = $informe AND
                                  id_indicador = $indicador";
            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
        }
        else{
            // Insertar Renglon
            $sql_renglonAdd = "INSERT INTO
                                  info_resumendata_pindicadores(
                                      id_resumen,
                                      id_indicador,
                                      periodo_ant,
                                      periodo_act,
                                      acumulado,
                                      fechamod
                                  )
                               VALUES(
                                  $informe,
                                  $indicador,
                                  '$periodo_ant',
                                  '$periodo_act',
                                  '$acum',
                                  '$fecha_update'
                               )";
            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
        }
        
        //////////////////////////////////////////////////////////////////////////        
        
        // Eliminar registro de datos del informe
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM info_resumendata_pindicadores WHERE id_resumen = $informe AND fechamod != '$fecha_update'");
            
        // Cargar los datos del informe
        $sql_infodata = "SELECT  
                            info_resumendata_pindicadores.id,
                            gtia_indicadores.nombre,
                            info_resumendata_pindicadores.periodo_ant,
                            info_resumendata_pindicadores.periodo_act,
                            info_resumendata_pindicadores.acumulado,
                            gtia_indicadores.meta,
                            info_resumendata_pindicadores.estado,
                            info_resumendata_pindicadores.tendencia,
                            info_resumendata_pindicadores.acciones,
                            info_resumendata_pindicadores.fechamod
                         FROM 
                            info_resumendata_pindicadores, gtia_indicadores
                         WHERE
                            info_resumendata_pindicadores.id_resumen = $informe AND
                            info_resumendata_pindicadores.id_indicador = gtia_indicadores.id";
        
        $query_infodata = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_infodata);
        
        if ($query_infodata->RecordCount() > 0) {

            $count = 0;
            while (!$query_infodata->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_resumen": "' . $informe . '",
                                    "indicador": "' . utf8_encode($query_infodata->fields[1]) . '",
                                    "periodo_ant": "'.$query_infodata->fields[2].'",
                                    "periodo_act": "'.$query_infodata->fields[3].'",
                                    "acumulado": "'.$query_infodata->fields[4].'",
                                    "meta": "'.$query_infodata->fields[5].'",
                                    "estado": "'.$query_infodata->fields[6].'",
                                    "tendencia": "'.$query_infodata->fields[7].'",
                                    "acciones": "' . utf8_encode($query_infodata->fields[8]) . '",
                                    "fechamod": "'.$query_infodata->fields[9].'"
                                  }';
                } else {
                    $response .= ',{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_resumen": "' . $informe . '",
                                    "indicador": "' . utf8_encode($query_infodata->fields[1]) . '",
                                    "periodo_ant": "'.$query_infodata->fields[2].'",
                                    "periodo_act": "'.$query_infodata->fields[3].'",
                                    "acumulado": "'.$query_infodata->fields[4].'",
                                    "meta": "'.$query_infodata->fields[5].'",
                                    "estado": "'.$query_infodata->fields[6].'",
                                    "tendencia": "'.$query_infodata->fields[7].'",
                                    "acciones": "' . utf8_encode($query_infodata->fields[8]) . '",
                                    "fechamod": "'.$query_infodata->fields[9].'"
                                   }';
                }
                
                $query_infodata->MoveNext();
            }
        }        
        
        $response .= ']}';

        return $response;
    }
    
    
    // Obtener los datos de la pestaña Comportamiento de las HFO
    function loadResumenDataComportamHFO($informe){
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");
        
        // Construir el JSON de HFOdata
        $response = '{"success": true, "comportamhfo": [';

        // Seleccionar el informe
        $sql_info = "SELECT  
                        info_resumen.proyecto,
                        info_resumen.zona,
                        info_resumen.desde,
                        info_resumen.hasta
                     FROM 
                        info_resumen
                     WHERE
                        info_resumen.id = $informe";
        
        $query_info = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_info);
        
        // Guardar la configuracion del informe
        $proyecto = $query_info->fields[0];
        $zona     = $query_info->fields[1];
        $desde    = $query_info->fields[2];
        $hasta    = $query_info->fields[3];
        
        // Determniar los rangos de fecha del informe
        
        // Determinar las fechas mayor y menor
        $fecha_mayor = '';
        $fecha_menor = '';

        if ($desde == '1900-01-01' || $desde == '' || $desde == null) {
            $qry_desde = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT fecha_inicio FROM gtia_proyectos WHERE nombre = '$proyecto'");
            $desde  = $qry_desde->fields[0];
        }
        if ($hasta == '1900-01-01' || $hasta == '' || $hasta == null) {
            $hasta  = date('Y-m-d');
        }

        if(str_replace('-','',$desde) < str_replace('-','',$hasta)){ $fecha_mayor = $hasta; $fecha_menor = $desde; }
        elseif(str_replace('-','',$desde) > str_replace('-','',$hasta)){ $fecha_mayor = $desde; $fecha_menor = $hasta; }
        else{ $fecha_mayor = $hasta; $fecha_menor = $desde; }
        
        // Días que comprende el rango
        $dias = $GLOBALS["cadenas"]->dias_entre_fechas($fecha_menor,$fecha_mayor);
        
        // Si es un dia
        if($dias == 0){
            $periodo_ant_desde = date('Y-m-d',strtotime($desde." -1 days"));
            $periodo_ant_hasta = '';
            $periodo_act_desde = $desde;
            $periodo_act_hasta = '';
            $acumulado_fecha   = $desde;
        }
        // Si es una semana
        elseif($dias == 7){
            $periodo_ant_desde = date('Y-m-d',strtotime($fecha_menor." -8 days"));
            $periodo_ant_hasta = date('Y-m-d',strtotime($fecha_mayor." -1 days"));
            $periodo_act_desde = $fecha_menor;
            $periodo_act_hasta = $fecha_mayor;
            $acumulado_fecha   = $fecha_mayor;
        }
        // Si es un mes
        elseif($dias >= 28 && $dias <= 31){
            $primerdia_mes = date('Y-m-d',strtotime('first day of this month '.$fecha_menor));
            $ultimodia_mes = date('Y-m-d',strtotime('last day of this month '.$fecha_mayor));
            if($primerdia_mes == $fecha_menor && $ultimodia_mes == $fecha_mayor){
                $periodo_ant_desde = date('Y-m-d',strtotime($primerdia_mes." -1 month"));
                $periodo_ant_hasta = date('Y-m-d',strtotime('last day of this month '.$periodo_ant_desde));
                $periodo_act_desde = $primerdia_mes;
                $periodo_act_hasta = $ultimodia_mes;
                $acumulado_fecha   = $ultimodia_mes;
            }
        }
        // Si es un año
        elseif($dias >= 365 && $dias <= 366){
            $primerdia_anio = date('Y-m-d',strtotime('first day of January '.date('Y',strtotime($fecha_menor))));
            $ultimodia_anio = date('Y-m-d',strtotime('last day of December '.date('Y',strtotime($fecha_mayor))));
            if($primerdia_anio == $fecha_menor && $ultimodia_anio == $fecha_mayor){
                $periodo_ant_desde = date('Y-m-d',strtotime($primerdia_anio." -1 year"));
                $periodo_ant_hasta = date('Y-m-d',strtotime('last day of +11 month '.$periodo_ant_desde));
                $periodo_act_desde = $primerdia_anio;
                $periodo_act_hasta = $ultimodia_anio;
                $acumulado_fecha   = $ultimodia_anio;
            }
        }
        // Si es un rango de fechas
        else{
            $mes_desde     = date('n',strtotime('first day of this month '.$fecha_menor));
            $mes_hasta     = date('n',strtotime('last day of this month '.$fecha_mayor));
            $primerdia_mes = date('Y-m-d',strtotime('first day of this month '.$fecha_menor));
            $ultimodia_mes = date('Y-m-d',strtotime('last day of this month '.$fecha_mayor));
            $meses         = $mes_hasta - $mes_desde;
            if($primerdia_mes == $fecha_menor && $ultimodia_mes == $fecha_mayor && $meses > 0){
                $meses++;
                $periodo_ant_desde = date('Y-m-d',strtotime($fecha_menor." -".$meses." months"));
                $periodo_ant_hasta = date('Y-m-d',strtotime($fecha_mayor." -".$meses." months"));
                $periodo_act_desde = $fecha_menor;
                $periodo_act_hasta = $fecha_mayor;
                $acumulado_fecha   = $fecha_mayor;
            }
            else{
                $periodo_ant_desde = date('Y-m-d',strtotime($fecha_menor." -".$dias." days"));
                $periodo_ant_hasta = date('Y-m-d',strtotime($fecha_mayor." -".$dias." days"));
                $periodo_act_desde = $fecha_menor;
                $periodo_act_hasta = $fecha_mayor;
                $acumulado_fecha   = $fecha_mayor;
            }
        }
               
                
        // Obtener los datos de cada Indicador
        
        
        /*******************************************
         ***  COMPORTAMIENTO DEL PERIODO ACTUAL  ***
         *******************************************/
        
        // Periodo Anterior
        $sql_periodo_ant = "SELECT
                                fecha_reporte, fecha_solucion, id, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                (fecha_reporte >= '$periodo_ant_desde' AND fecha_reporte <= '$periodo_ant_hasta') AND
                                objeto_local LIKE 'BW%(%'";
    
        $qry_periodo_ant = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_ant);

        $sum_periodo_ant   = 0;
        $ctdad_hfo_per_ant = 0;
    
        if($qry_periodo_ant){
            while (!$qry_periodo_ant->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_periodo_ant->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_ant->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_ant->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_ant->fields[3];
                $fecha_almacen = $qry_periodo_ant->fields[4];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_ant->fields[1] != '' && $qry_periodo_ant->fields[1] != null && $qry_periodo_ant->fields[1] == '1900-01-01') ? $qry_periodo_ant->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_ant->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_ant->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_ant += $calculo_demora;
                
                // Contar las habitaciones por cada SD
                $id_sd      = $qry_periodo_ant->fields[2];
                $sql_habit  = "SELECT
                                  COUNT(id_parte) AS ctdad
                               FROM
                                  gtia_sd_partes
                               WHERE
                                  gtia_sd_partes.id_sd = $id_sd";
                $qry_habit  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_habit);
                $count_hfo  = $qry_habit->RecordCount();                
                $ctdad_hfo_per_ant += $count_hfo;
                
                $qry_periodo_ant->MoveNext();
            }
            $qry_periodo_ant->Close();
        }
            
        if ($sum_periodo_ant != 0 && $ctdad_hfo_per_ant != 0) {
            $demora_periodo_ant = $sum_periodo_ant / $ctdad_hfo_per_ant;
        } else {
            $demora_periodo_ant = 0;
        }
        $demora_periodo_ant = number_format($demora_periodo_ant,0);
            
        // Periodo Actual
        $sql_periodo_act = "SELECT
                                fecha_reporte, fecha_solucion, id, suministro, fecha_almacen
                            FROM
                                gtia_sd
                            WHERE
                                proyecto = '$proyecto' AND
                                (fecha_reporte >= '$periodo_act_desde' AND fecha_reporte <= '$periodo_act_hasta') AND
                                objeto_local LIKE 'BW%(%'";
    
        $qry_periodo_act = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_periodo_act);

        $sum_periodo_act   = 0;
        $ctdad_hfo_per_act = 0;
    
        if($qry_periodo_act){
            while (!$qry_periodo_act->EOF) {
    
                /*$arr_fecha_reporte  = explode('-', $qry_periodo_act->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_periodo_act->fields[1]);
                $timestamp_Reporte  = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_periodo_act->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora    = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora     = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_periodo_act->fields[3];
                $fecha_almacen = $qry_periodo_act->fields[4];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_periodo_act->fields[1] != '' && $qry_periodo_act->fields[1] != null && $qry_periodo_act->fields[1] == '1900-01-01') ? $qry_periodo_act->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_periodo_act->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_periodo_act->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_act += $calculo_demora;
                
                // Contar las habitaciones por cada SD
                $id_sd      = $qry_periodo_act->fields[2];
                $sql_habit  = "SELECT
                                  COUNT(id_parte) AS ctdad
                               FROM
                                  gtia_sd_partes
                               WHERE
                                  gtia_sd_partes.id_sd = $id_sd";
                $qry_habit  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_habit);
                $count_hfo  = $qry_habit->RecordCount();                
                $ctdad_hfo_per_act += $count_hfo;
                
                $qry_periodo_act->MoveNext();
            }
            $qry_periodo_act->Close();
        }
    
        if ($sum_periodo_act != 0 && $ctdad_hfo_per_act != 0) {
            $demora_periodo_act = $sum_periodo_act / $ctdad_hfo_per_act;
        } else {
            $demora_periodo_act = 0;
        }
        $demora_periodo_act = number_format($demora_periodo_act,0);
        
        // Meta
        $sql_meta = "SELECT
                        id,meta
                    FROM
                        gtia_indicadores
                    WHERE
                        nombre = 'Comportamiento de las HFO'";
    
        $qry_meta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_meta);
        $indicador = $qry_meta->fields[0]; 
        $textmeta  = $qry_meta->fields[1];
        $metarray  = explode(' ',$textmeta);
        $meta      = $metarray[1]; 
        
        // Estado
        if($demora_periodo_act <= intval($meta)){ $estado = 'Bien'; }else{ $estado = 'Mal'; }
        
        // Tendencia
        $dif_pact_meta = $demora_periodo_act - $meta;
        $dif_pant_meta = $demora_periodo_ant - $meta;
        if($dif_pant_meta < $dif_pact_meta){ $tendencia = 'asc'; }
        elseif($dif_pant_meta == $dif_pact_meta){ $tendencia = 'const'; }
        elseif($dif_pant_meta > $dif_pact_meta){ $tendencia = 'desc'; }
        
        // Actualizar/Insertar el renglon en los datos del informe
        $sql_renglon = "SELECT id FROM info_resumendata_coportamhfo WHERE id_resumen = $informe AND id_indicador = $indicador";
        $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
        if($qry_renglon->RecordCount() > 0){
            // Actualizar Renglon
            $sql_renglonUpd = "UPDATE
                                  info_resumendata_coportamhfo
                               SET
                                  demora = '$demora_periodo_act Dias',
                                  ctdad_hfo = $ctdad_hfo_per_act,
                                  estado = '$estado',
                                  tendencia = '$tendencia',
                                  fechamod = '$fecha_update'
                               WHERE
                                  id_resumen = $informe AND
                                  id_indicador = $indicador";
            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
        }
        else{
            // Insertar Renglon
            $sql_renglonAdd = "INSERT INTO
                                  info_resumendata_coportamhfo(
                                      id_resumen,
                                      id_indicador,
                                      demora,
                                      ctdad_hfo,
                                      estado,
                                      tendencia,
                                      fechamod
                                  )
                               VALUES(
                                  $informe,
                                  $indicador,
                                  '$demora_periodo_act Dias',
                                  $ctdad_hfo_per_act,
                                  '$estado',
                                  '$tendencia',
                                  '$fecha_update'
                               )";
            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
        }

        
        /**************************************
         ***  COMPORTAMIENTO DEL ACUMULADO  ***
         **************************************/
                
        // Acumulado
        $sql_acum = "SELECT
                        fecha_reporte, fecha_solucion, id, suministro, fecha_almacen
                     FROM
                        gtia_sd
                     WHERE
                        proyecto = '$proyecto' AND
                        fecha_reporte <= '$acumulado_fecha' AND
                        objeto_local LIKE 'BW%(%'";
    
        $qry_acum = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_acum);

        $sum_periodo_acum   = 0;
        $ctdad_hfo_per_acum = 0;
    
        if($qry_acum){
            while (!$qry_acum->EOF) {
    
                /*$arr_fecha_reporte = explode('-', $qry_acum->fields[0]);
                $arr_fecha_solucion = explode('-', $qry_acum->fields[1]);
                $timestamp_Reporte = mktime(0, 0, 0, $arr_fecha_reporte[1], $arr_fecha_reporte[2], $arr_fecha_reporte[0]);
                if (count($arr_fecha_solucion) != 3 || $qry_acum->fields[1] == '1900-01-01') {
                    $arr_fecha_solucion = explode('-', date('Y-m-d'));
                }
                $timestamp_Solucion = mktime(0, 0, 0, $arr_fecha_solucion[1], $arr_fecha_solucion[2], $arr_fecha_solucion[0]);
                $segundos_demora = $timestamp_Solucion - $timestamp_Reporte;
                $calculo_demora = $segundos_demora / (60 * 60 * 24);*/

                $suministro = $qry_acum->fields[3];
                $fecha_almacen = $qry_acum->fields[4];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($qry_acum->fields[1] != '' && $qry_acum->fields[1] != null && $qry_acum->fields[1] == '1900-01-01') ? $qry_acum->fields[1] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $qry_acum->fields[0];
                    }
                    else {
                        if ($fecha_almacen != '' && $fecha_almacen != null && $fecha_almacen != '1900-01-01') {
                            $inicio = $fecha_almacen;
                            $almacen_number = str_replace('-', '', $fecha_almacen);
                            $fin_number = str_replace('-', '', $fin);
                            if ($almacen_number > $fin_number) {
                                $fin = date('Y-m-d');
                            }
                        }
                        else {
                        $inicio = $qry_acum->fields[0];
                        }
                    }
                    
                    // Calculo de la demora
                    $start = new DateTime($inicio);
                    $end = new DateTime($fin);

                    //de lo contrario, se excluye la fecha de finalización (¿error?)
                    $end->modify('+1 day');
            
                    $interval = $end->diff($start);
            
                    // total dias
                    $days = $interval->days;
            
                    // crea un período de fecha iterable (P1D equivale a 1 día)
                    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
                    // almacenado como matriz, por lo que puede agregar más de una fecha feriada
                    $holidays = array('2012-09-07');
            
                    foreach($period as $dt) {
                        $curr = $dt->format('D');

                        // obtiene si es Domingo
                        if($curr == 'Sun') {
                            $days--;
                        }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                            $days--;
                        }
                    }
                    
                    $calculo_demora = $days;
                }
        
                $sum_periodo_acum += $calculo_demora;
                
                // Contar las habitaciones por cada SD
                $id_sd      = $qry_acum->fields[2];
                $sql_habit  = "SELECT
                                  COUNT(id_parte) AS ctdad
                               FROM
                                  gtia_sd_partes
                               WHERE
                                  gtia_sd_partes.id_sd = $id_sd";
                $qry_habit  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_habit);
                $count_hfo  = $qry_habit->RecordCount();                
                $ctdad_hfo_per_acum += $count_hfo;
                
                $qry_acum->MoveNext();
            }
            $qry_acum->Close();
        }
    
        if ($sum_periodo_acum != 0 && $ctdad_hfo_per_acum != 0) {
            $demora_acum = $sum_periodo_acum / $ctdad_hfo_per_acum;
        } else {
            $demora_acum = 0;
        }
        $demora_acum = number_format($demora_acum,0). ' Dias';
        $demora_acum_number = number_format($demora_acum,0);
        
        // Meta
        $sql_meta = "SELECT
                        id,meta
                    FROM
                        gtia_indicadores
                    WHERE
                        nombre = 'Comportamiento de las HFO Acumulado'";
    
        $qry_meta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_meta);
        $indicador = $qry_meta->fields[0]; 
        $textmeta  = $qry_meta->fields[1];
        $metarray  = explode(' ',$textmeta);
        $meta      = $metarray[1];
        
        // Estado
        if($demora_acum_number <= intval($meta)){ $estado = 'Bien'; }else{ $estado = 'Mal'; }
        
        // Tendencia
        $dif_acum_meta = $demora_acum - $meta;
        $dif_pant_meta = $demora_periodo_ant - $meta;
        if($dif_acum_meta < $dif_pant_meta){ $tendencia = 'asc'; }
        elseif($dif_acum_meta == $dif_pant_meta){ $tendencia = 'const'; }
        elseif($dif_acum_meta > $dif_pant_meta){ $tendencia = 'desc'; }
        
        // Actualizar/Insertar el renglon en los datos del informe
        $sql_renglon = "SELECT id FROM info_resumendata_pindicadores WHERE id_resumen = $informe AND id_indicador = $indicador";
        $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
        if($qry_renglon->RecordCount() > 0){
            // Actualizar Renglon
            $sql_renglonUpd = "UPDATE
                                  info_resumendata_coportamhfo
                               SET
                                  demora = '$demora_periodo_act Dias',
                                  ctdad_hfo = $ctdad_hfo_per_acum,
                                  estado = '$estado',
                                  tendencia = '$tendencia',
                                  fechamod = '$fecha_update'
                               WHERE
                                  id_resumen = $informe AND
                                  id_indicador = $indicador";
            $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
        }
        else{
            // Insertar Renglon
            $sql_renglonAdd = "INSERT INTO
                                  info_resumendata_coportamhfo(
                                      id_resumen,
                                      id_indicador,
                                      demora,
                                      ctdad_hfo,
                                      estado,
                                      tendencia,
                                      fechamod
                                  )
                               VALUES(
                                  $informe,
                                  $indicador,
                                  '$demora_periodo_act Dias',
                                  $ctdad_hfo_per_acum,
                                  '$estado',
                                  '$tendencia',
                                  '$fecha_update'
                               )";
            $qry_renglonAdd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonAdd);
        }
               
        //////////////////////////////////////////////////////////////////////////        
        
        // Eliminar registro de datos del informe
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM info_resumendata_coportamhfo WHERE id_resumen = $informe AND fechamod != '$fecha_update'");
            
        // Cargar los datos del informe
        $sql_infodata = "SELECT  
                            info_resumendata_coportamhfo.id,
                            gtia_indicadores.nombre,
                            info_resumendata_coportamhfo.demora,
                            info_resumendata_coportamhfo.ctdad_hfo,
                            gtia_indicadores.meta,
                            info_resumendata_coportamhfo.estado,
                            info_resumendata_coportamhfo.tendencia,
                            info_resumendata_coportamhfo.fechamod
                         FROM 
                            info_resumendata_coportamhfo, gtia_indicadores
                         WHERE
                            info_resumendata_coportamhfo.id_resumen = $informe AND
                            info_resumendata_coportamhfo.id_indicador = gtia_indicadores.id";
        
        $query_infodata = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_infodata);
        
        if ($query_infodata->RecordCount() > 0) {

            $count = 0;
            while (!$query_infodata->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_resumen": "' . $informe . '",
                                    "indicador": "' . utf8_encode($query_infodata->fields[1]) . '",
                                    "demora": "'.$query_infodata->fields[2].'",
                                    "ctdad": "'.$query_infodata->fields[3].'",
                                    "meta": "'.$query_infodata->fields[4].'",
                                    "estado": "'.$query_infodata->fields[5].'",
                                    "tendencia": "'.$query_infodata->fields[6].'",
                                    "fechamod": "'.$query_infodata->fields[7].'"
                                  }';
                } else {
                    $response .= ',{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_resumen": "' . $informe . '",
                                    "indicador": "' . utf8_encode($query_infodata->fields[1]) . '",
                                    "demora": "'.$query_infodata->fields[2].'",
                                    "ctdad": "'.$query_infodata->fields[3].'",
                                    "meta": "'.$query_infodata->fields[4].'",
                                    "estado": "'.$query_infodata->fields[5].'",
                                    "tendencia": "'.$query_infodata->fields[6].'",
                                    "fechamod": "'.$query_infodata->fields[7].'"
                                   }';
                }
                
                $query_infodata->MoveNext();
            }
        }        
        
        $response .= ']}';

        return $response;
    }
    
                  
    // Obtener los datos de la pestaña HFO
    function loadResumenDataHFO($informe){
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");
        
        // Construir el JSON de HFOdata
        $response = '{"success": true, "hfo": [';

        // Seleccionar el informe
        $sql_info = "SELECT  
                        info_resumen.proyecto,
                        info_resumen.zona,
                        info_resumen.desde,
                        info_resumen.hasta
                     FROM 
                        info_resumen
                     WHERE
                        info_resumen.id = $informe";
        
        $query_info = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_info);
        
        // Guardar la configuracion del informe
        $proyecto = $query_info->fields[0];
        $zona     = $query_info->fields[1];
        $desde    = $query_info->fields[2];
        $hasta    = $query_info->fields[3];

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
            $sql_data .= " AND gtia_sd.objeto_local LIKE 'BW%(%'";
                       
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
                        
                        if(is_numeric($qry_habit->fields[0])){
                            
                            $habit_cont++;
                            
                            // Habitaciones
                            if($habit_cont == 1){
                                $habit_list .= $qry_habit->fields[0];
                            }
                            else{
                                $habit_list .= ', '.$qry_habit->fields[0];
                            }
                            
                            // Habitaciones Pendientes                    
                            if($qry_habit->fields[1] == 'Por Resolver'){
                                $pendientes++;
                            }
                        }
                        
                        $qry_habit->MoveNext();
                    }
                                        
                    $qry_data->MoveNext();
                }
            }
            
            if($sd_exist == true){
                
                // Actualizar/Insertar el renglon en los datos del informe
                $sql_renglon = "SELECT id FROM info_resumendata_hfo WHERE id_resumen = $informe AND id_problema = $id_problema";
                $qry_renglon = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglon);
                if($qry_renglon->RecordCount() > 0){
                    // Actualizar Renglon
                    $sql_renglonUpd = "UPDATE
                                          info_resumendata_hfo
                                       SET
                                          sd = '$sd_list',
                                          habitaciones = '$habit_list',
                                          ctdad_habit = $habit_cont,
                                          pendientes = $pendientes,
                                          fechamod = '$fecha_update'
                                       WHERE
                                          id_resumen = $informe AND
                                          id_problema = $id_problema";
                    $qry_renglonUpd = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_renglonUpd);
                }
                else{
                    // Insertar Renglon
                    $sql_renglonAdd = "INSERT INTO
                                          info_resumendata_hfo(
                                              id_resumen,
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
        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM info_resumendata_hfo WHERE id_resumen = $informe AND fechamod != '$fecha_update'");
            
        // Cargar los datos del informe
        $sql_infodata = "SELECT  
                            info_resumendata_hfo.id,
                            info_resumendata_hfo.sd,
                            info_resumendata_hfo.habitaciones,
                            info_resumendata_hfo.ctdad_habit,
                            info_resumendata_hfo.pendientes,
                            gtia_problemas.descripcion,
                            info_resumendata_hfo.observaciones,
                            info_resumendata_hfo.fechamod
                         FROM 
                            info_resumendata_hfo, gtia_problemas
                         WHERE
                            info_resumendata_hfo.id_resumen = $informe AND
                            info_resumendata_hfo.id_problema = gtia_problemas.id";
        
        $query_infodata = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_infodata);
        
        if ($query_infodata->RecordCount() > 0) {

            $count = 0;
            while (!$query_infodata->EOF) {
                $count++;
                if ($count == 1) {
                    $response .= '{
                                    "id": "' . $query_infodata->fields[0] . '",
                                    "id_resumen": "' . $informe . '",
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
                                     "id_resumen": "' . $informe . '",
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

    
    // Insertar nuevo informe Resumen
    function ResumenInsert($titulo, $proyecto, $zona, $desde, $hasta) {

        $id_user = $_SESSION['idsession'];
        
        if($zona == ''){ $zona = 'Todas'; }else{ $zona = str_replace(',', ', ', $zona); }
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO info_resumen(id_user,titulo,proyecto,zona,desde,hasta,fechamod) VALUES($id_user,'$titulo','$proyecto','$zona','$desde','$hasta','$fecha_update')");

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
    
    
    // Cargar campos del Form de Informes
    function ResumenFormLoad($id) {

        $rango = 1;
        $qry   = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id,titulo,proyecto,zona,desde,hasta FROM info_resumen WHERE id = $id");
            
        if ($qry->RecordCount() > 0) {

            if ($qry->fields[3] == 'Todas'){
                $zona = '';
            }else{
                $zona = $qry->fields[3];
            }
            if ($qry->fields[4] == '1900-01-01'){
                $desde= '';
            }else{
                $desde = $qry->fields[4];
            }
            if ($qry->fields[5] == '1900-01-01'){
                $hasta= '';
            }else{
                $hasta = $qry->fields[5];
            }
            if($qry->fields[4] != '1900-01-01' && $qry->fields[5] != '1900-01-01'){
                $rango = 0;
            }

            $response = json_encode(array(
                "success" => true,
                "data" => array(
                    "id" => $qry->fields[0],
                    "titulo" => $GLOBALS['cadenas']->utf8($qry->fields[1]),
                    "proyecto" => $GLOBALS['cadenas']->utf8($qry->fields[2]),
                    "zona" => $GLOBALS['cadenas']->utf8($zona),
                    "desde" => $desde,
                    "hasta" => $hasta,
                    "rango" => $rango
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

    
    // Modificar informe Resumen
    function ResumenUpdate($id, $titulo, $proyecto, $zona, $desde, $hasta) {

        $id_user = $_SESSION['idsession'];
        
        if($zona == ''){ $zona = 'Todas'; }else{ $zona = str_replace(',', ', ', $zona); }
        
        // Fecha de actualizacion de los datos
        $ahora        = time();
        $hora         = date("h") - 1;
        $fecha_update = date("Y-m-d"). " " .$hora. ":" .date("i:s.000");

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_resumen SET titulo = '$titulo',proyecto = '$proyecto',zona = '$zona',desde = '$desde',hasta = '$hasta',fechamod = '$fecha_update' WHERE id = $id");

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
    
    
    // Eliminar Informe Resumen
    function ResumenDelete($id) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("Info_resumen_Delete $id");

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
    
            
    //  Comentario de los datos del informe SD PENDIENTES
    function resumendataSDPendientesGridComent($id_row,$comentario) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_resumendata_sdpendientes SET comentario = '$comentario' WHERE id = $id_row");
        
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
        
    //  Comentario de los datos del informe Principales Indicadores
    function ResumendataPIndicadoresGridComent($id_row,$comentario) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_resumendata_pindicadores SET acciones = '$comentario' WHERE id = $id_row");
        
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
        
    //  Comentario de los datos del informe Problemas + Repetitivos
    function ResumendataRepetitividadGridComent($id_row,$comentario) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_resumendata_repetitividad SET comentario = '$comentario' WHERE id = $id_row");
        
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
    
    
    //  Comentario de los datos del informe HFO
    function ResumendataHfoGridComent($id_row,$comentario) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_resumendata_hfo SET observaciones = '$comentario' WHERE id = $id_row");
        
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
    
    
    // Comentario Inicial
    function resumendataComentInicial($id_resumen,$comentario){
                
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_resumen SET comentario_inicial = '$comentario' WHERE id = $id_resumen");
        
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
    
    
    // Comentario Final
    function resumendataComentFinal($id_resumen,$comentario){
        
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE info_resumen SET comentario_final = '$comentario' WHERE id = $id_resumen");
        
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
    
    
    function nl2p($string, $line_breaks = true, $xml = true)
    {
        // Remove existing HTML formatting to avoid double-wrapping things
        $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);
     
        // It is conceivable that people might still want single line-breaks
        // without breaking into a new paragraph.
        if ($line_breaks == true)
            return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br'.($xml == true ? ' /' : '').'>'), trim($string)).'</p>';
        else 
            return '<p>'.preg_replace("/([\n]{1,})/i", "</p>\n<p>", trim($string)).'</p>';
    }
    
    
    function ResumenSectionValidate($id){
        
        $response = json_encode(array(
            "failure" => true,
            "message" => "Error en la operacion."
        ));        
        if($_SESSION['inforesumen'] == ''){
            if($_SESSION['inforesumen'] .= $id){
                $response = json_encode(array(
                    "success" => true,
                    "message" => $_SESSION['inforesumen']
                ));
            }
        }
        else{
            if($_SESSION['inforesumen'] .= ',' . $id){
                $response = json_encode(array(
                    "success" => true,
                    "message" => $_SESSION['inforesumen']
                ));
            }
        }        
        return $response;        
    }
    
    
    function ResumenSectionValidateClean(){
        if($_SESSION['inforesumen'] = ''){
            $response = json_encode(array(
                "success" => true
            ));
        }        
        return $response; 
    }
    
    
    function LoadResumenValidate(){
        
        $comentini         = 1;
        $estados           = 0;
        $sdpendientes      = 0;
        $indicadores       = 0;
        $problemasrep      = 0;
        $hfo               = 0;
        $comportamientohfo = 0;
        $deficonstruct     = 0;
        $comentfin         = 0;
        
        if($_SESSION['inforesumen'] != ''){
            
            $section_array = explode(',',$_SESSION['inforesumen']);
            $section_count = count($section_array);
            for($i = 0; $i < $section_count; $i++){
                if($section_array[$i] == 'ResumendataEstadosPanel'){ $estados = 1; }
                elseif($section_array[$i] == 'ResumendataSDPendientesGrid'){ $sdpendientes = 1; }
                elseif($section_array[$i] == 'ResumendataPIndicadoresGrid'){ $indicadores = 1; }
                elseif($section_array[$i] == 'ResumendataRepetitividadGrid'){ $problemasrep = 1; }
                elseif($section_array[$i] == 'ResumendataHfoGrid'){ $hfo = 1; }
                elseif($section_array[$i] == 'ResumendataComportamHfoGrid'){ $comportamientohfo = 1; }
                elseif($section_array[$i] == 'ResumenDeficienciasConstructivas'){ $deficonstruct = 1; }
                elseif($section_array[$i] == 'ResumendataFormComentFinal'){ $comentfin = 1; }
            }
        }
        
        $response = json_encode(array(
                "success" => true,
                "data" => array(
                    "comentini" => $comentini,
                    "estados" => $estados,
                    "sdpendientes" => $sdpendientes,
                    "indicadores" => $indicadores,
                    "problemasrep" => $problemasrep,
                    "hfo" => $hfo,
                    "comportamientohfo" => $comportamientohfo,
                    "deficonstruct" => $deficonstruct,
                    "comentfin" => $comentfin
                )
            ));
        
        return $response;
    }
    
    ////////////////////////////////////////////
    ////////////////////////////////////////////
    ///////////  Getters && Setters  ///////////
    ////////////////////////////////////////////
}
