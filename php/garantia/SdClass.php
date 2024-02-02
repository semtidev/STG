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

// Validar Polo del usuario
$polo = -1;
if (intval($_SESSION['polo']) != 9) {
    $polo = intval($_SESSION['polo']);
}


// CLASE SOLICITUD DEFECTACION    

class SD {

    // Atributos  
    // Implementacion  
    
    // Listar SD
    function ReadSD($limit, $page, $listar, $filtrar) {

        $listar_array = explode('.', $listar);
        $proyecto     = $listar_array[0];
        $estado       = $listar_array[1];
        $tiposd       = $listar_array[2];
        $total_reg    = 0;
        $id_usuario   = $_SESSION['idsession'];
        
        $offset = ($limit * ($page - 1));

        // Declarar la consulta a la BD
        // Si se realiza una busqueda avanzada
        if ($filtrar != '') {

            $array_filtro = explode('*', $filtrar);

            // Asignar valores a los criterios de busqueda
            if ($array_filtro[0] != '') {
                $descripcion = $GLOBALS['cadenas']->latin1($GLOBALS['cadenas']->codificarBD_utf8($array_filtro[0]));
            } else {
                $descripcion = '';
            }
            if ($array_filtro[1] != '') {
                $numero = $array_filtro[1];
            } else {
                $numero = '';
            }
            if ($array_filtro[2] != '') {
                $dpto = $array_filtro[2];
                $dpto = str_replace(',', ', ', $dpto);
            } else {
                $dpto = '';
            }
            if ($array_filtro[3] != '') {
                $problema = $array_filtro[3];
            } else {
                $problema = '';
            }
            if ($array_filtro[4] != '') {
                $objeto_parte = $array_filtro[4];
            } else {
                $objeto_parte = '';
            }
            if ($array_filtro[5] != '') {
                $constructiva = $array_filtro[5];
            } else {
                $constructiva = '';
            }
            if ($array_filtro[6] != '') {
                $aeh = $array_filtro[6];
            } else {
                $aeh = '';
            }
            if ($array_filtro[7] != '') {
                $suministro = $array_filtro[7];
            } else {
                $suministro = '';
            }
            if ($array_filtro[8] != '') {
                $reportes_desde = $array_filtro[8];
            } else {
                $reportes_desde = '';
            }
            if ($array_filtro[9] != '') {
                $reportes_hasta = $array_filtro[9];
            } else {
                $reportes_hasta = '';
            }
            if ($array_filtro[10] != '') {
                $solucion_desde = $array_filtro[10];
            } else {
                $solucion_desde = '';
            }
            if ($array_filtro[11] != '') {
                $solucion_hasta = $array_filtro[11];
            } else {
                $solucion_hasta = '';
            }
            if ($array_filtro[12] != '') {
                $demora = $array_filtro[12];
            } else {
                $demora = '';
            }
            if ($array_filtro[13] != '') {
                $criteriodemora = $array_filtro[13];
            } else {
                $criteriodemora = '';
            }
            if ($array_filtro[14] != '') {
                $diasdemora = $array_filtro[14];
            } else {
                $diasdemora = '';
            }
            if ($array_filtro[15] != '') {
                $compra_imp = $array_filtro[15];
            } else {
                $compra_imp = '';
            }
            if ($array_filtro[16] != '') {
                $compra_nac = $array_filtro[16];
            } else {
                $compra_nac = '';
            }

            if ($array_filtro[17] != '') {
                $hascosto = $array_filtro[17];
            } else {
                $hascosto = '';
            }
            if ($array_filtro[18] != '') {
                $criteriocosto = $array_filtro[18];
            } else {
                $criteriocosto = '';
            }
            if ($array_filtro[19] != '') {
                $filtercosto = floatval($array_filtro[19]);
            } else {
                $filtercosto = '';
            }
            
            // Construir la consulta a BD
            if ($GLOBALS["polo"] == -1) {
                $sql_query = "SELECT
                                distinct gtia_sd.id,
                                gtia_sd.numero,
                                gtia_problemas.descripcion,
                                gtia_sd.descripcion,
                                gtia_sd.proyecto,
                                gtia_sd.zona,
                                gtia_sd.objeto_local,
                                gtia_sd.fecha_reporte,
                                gtia_sd.fecha_solucion,
                                gtia_sd.estado,
                                gtia_sd.constructiva,
                                gtia_sd.suministro,
                                gtia_sd.afecta_explotacion,
                                gtia_sd.comentario,
                                gtia_sd.fecha_mod,
                                gtia_sd.tipo_compra,
                                gtia_sd.documento,
                                gtia_proyectos.imagen,
                                gtia_sd.causa,
                                gtia_sd.fecha_almacen,
                                gtia_sd.costo
                            FROM
                                gtia_sd,
                                gtia_dptos,
                                gtia_sd_dpto,
                                gtia_problemas,
                                gtia_proyectos
                            WHERE
                                gtia_sd.id_problema = gtia_problemas.id AND
                                gtia_sd.proyecto = gtia_proyectos.nombre";
            }
            else {
                $sql_query = "SELECT
                                distinct gtia_sd.id,
                                gtia_sd.numero,
                                gtia_problemas.descripcion,
                                gtia_sd.descripcion,
                                gtia_sd.proyecto,
                                gtia_sd.zona,
                                gtia_sd.objeto_local,
                                gtia_sd.fecha_reporte,
                                gtia_sd.fecha_solucion,
                                gtia_sd.estado,
                                gtia_sd.constructiva,
                                gtia_sd.suministro,
                                gtia_sd.afecta_explotacion,
                                gtia_sd.comentario,
                                gtia_sd.fecha_mod,
                                gtia_sd.tipo_compra,
                                gtia_sd.documento,
                                gtia_proyectos.imagen,
                                gtia_sd.causa,
                                gtia_sd.fecha_almacen,
                                gtia_sd.costo
                            FROM
                                gtia_sd,
                                gtia_dptos,
                                gtia_sd_dpto,
                                gtia_problemas,
                                gtia_proyectos
                            WHERE
                                gtia_sd.id_problema = gtia_problemas.id AND
                                gtia_sd.proyecto = gtia_proyectos.nombre AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $GLOBALS["polo"];
            }

            // DESCRIPCION
            if ($descripcion != '') {
                
                $array_criterios = explode(',', $descripcion);
                $textsearch = " AND (";
                $search_counter = 0;
                
                // VARIOS CRITERIOS DE BUSQUEDA
                if(count($array_criterios) > 1){                    
                    
                    // RECORRER CADA CRITERIO DE BUSQUEDA
                    for($i = 0; $i < count($array_criterios); $i++){                        
                        
                        $search_counter++;
                        $search = trim($array_criterios[$i]);
                        
                        if($search_counter > 1){
                            $textsearch .= " OR (";
                        }else{
                            $textsearch .= "(";
                        }
                        
                        $array_filtros = explode(' ', $search);

                        // CUANDO AL CRITERIO SE LE APLICAN FILTROS
                        if(count($array_filtros) > 1){
                             
                            $filter_count = 0;

                            for($j = 0; $j < count($array_filtros); $j++){

                                $filter_count++;
                                $filter = trim($array_filtros[$j]);
                                $firstcarapter_filter = substr($filter,0,1);

                                if($filter_count > 1){             

                                   $textsearch .= " AND "; 
                                }

                                if($firstcarapter_filter == '-'){
                                    $filter = substr($filter,1);
                                    $textsearch .= "gtia_sd.descripcion NOT LIKE '%$filter%'";
                                }
                                elseif($firstcarapter_filter == '+'){
                                    $filter = substr($filter,1);
                                    $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                                }
                                else {
                                    $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                                }                       
                            }
                        }
                        // CUANDO EL CRITERIO NO TIENE FILTROS
                        else{

                            $filter = trim($array_filtros[0]);
                            $firstcarapter_filter = substr($filter,0,1);
                            if($firstcarapter_filter == '-'){
                                $filter = substr($filter,1);
                                $textsearch .= "gtia_sd.descripcion NOT LIKE '%$filter%'";
                            }
                            elseif($firstcarapter_filter == '+'){
                                $filter = substr($filter,1);
                                $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                            }
                            else {
                                $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                            }
                        }
                        
                        // Establecer idioma español e ignorar capitalizacion y acentos en la consulta
                        $textsearch .= " COLLATE Modern_Spanish_CI_AI)";                        
                    } 
                }
                // UN SOLO CRITERIO DE BUSQUEDA
                else{                    
                    
                    $search = trim($array_criterios[0]);
                    $array_filtros = explode(' ', $search);
                    
                    // CUANDO AL CRITERIO SE LE APLICAN FILTROS
                    if(count($array_filtros) > 1){
                                               
                        $filter_count = 0;
                        
                        for($j = 0; $j < count($array_filtros); $j++){
                            
                            $filter_count++;
                            $filter = trim($array_filtros[$j]);
                            $firstcarapter_filter = substr($filter,0,1);
                            
                            if($filter_count > 1){             
                                
                               $textsearch .= " AND "; 
                            }
                            
                            if($firstcarapter_filter == '-'){
                                $filter = substr($filter,1);
                                $textsearch .= "gtia_sd.descripcion NOT LIKE '%$filter%'";
                            }
                            elseif($firstcarapter_filter == '+'){
                                $filter = substr($filter,1);
                                $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                            }
                            else {
                                $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                            }                       
                        }                  
                    }
                    // CUANDO EL CRITERIO NO TIENE FILTROS
                    else{
                        
                        $filter = trim($array_filtros[0]);
                        $firstcarapter_filter = substr($filter,0,1);
                        if($firstcarapter_filter == '-'){
                            $filter = substr($filter,1);
                            $textsearch .= "gtia_sd.descripcion NOT LIKE '%$filter%'";
                        }
                        elseif($firstcarapter_filter == '+'){
                            $filter = substr($filter,1);
                            $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                        }
                        else {
                            $textsearch .= "gtia_sd.descripcion LIKE '%$filter%'";
                        }
                    } 
                    
                    // Establecer idioma español e ignorar capitalizacion y acentos en la consulta
                    $textsearch .= " COLLATE Modern_Spanish_CI_AI";  
                }
                
                $textsearch .= ")";
                $sql_query  .= $textsearch;
            }

            // NUMERO
            if ($numero != '') {
                $sql_query .= " AND gtia_sd.numero LIKE '%$numero%'";
            }

            // PROBLEMA
            if ($problema != '') {
                $sql_query .= " AND gtia_problemas.descripcion = '$problema'";
            }

            // DPTO
            if ($dpto != '') {
                $sql_query .= " AND (";
                $dpto_array = explode(', ',$dpto);
                $dpto_ctdad = count($dpto_array);
                for($d = 0; $d < $dpto_ctdad; $d++){
                    if($d == 0){
                        $sql_query .= "(gtia_sd.id = gtia_sd_dpto.id_sd AND gtia_sd_dpto.id_dpto = gtia_dptos.id AND gtia_dptos.nombre = '$dpto_array[$d]')";
                    }
                    else{
                        $sql_query .= " OR (gtia_sd.id = gtia_sd_dpto.id_sd AND gtia_sd_dpto.id_dpto = gtia_dptos.id AND gtia_dptos.nombre = '$dpto_array[$d]')";
                    }
                }
                $sql_query .= ")";
            }

            // ESTADO
            if ($estado != 'Todos') {
                $sql_query .= " AND gtia_sd.estado = '$estado'";
            }
            
            // TIPO SD
            if ($tiposd == 'SD Comunes') {
                $sql_query .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
            }
            elseif ($tiposd == 'SD Habitaciones') {
                $sql_query .= " AND gtia_sd.objeto_local LIKE '%BW%'";
            }

            // CONSTRUCTIVAS
            if ($constructiva != '' && is_numeric($constructiva)) {
                $sql_query .= " AND gtia_sd.constructiva = $constructiva";
            }

            // SUMINISTRO
            if ($suministro != '' && is_numeric($suministro)) {

                $sql_query .= " AND gtia_sd.suministro = $suministro";
                if ($suministro == 1) {

                    // COMPRAS
                    if ($compra_imp == 'true' && $compra_nac == 'true') {
                        $sql_query .= " AND (gtia_sd.tipo_compra = 'Imp' OR gtia_sd.tipo_compra = 'Nac')";
                    } elseif ($compra_imp == 'true' && $compra_nac == 'false') {
                        $sql_query .= " AND gtia_sd.tipo_compra = 'Imp'";
                    } elseif ($compra_imp == 'false' && $compra_nac == 'true') {
                        $sql_query .= " AND gtia_sd.tipo_compra = 'Nac'";
                    }
                }
            }

            // AFECTA LA EXPLOTACION DEL HOTEL
            if ($aeh != '' && is_numeric($aeh)) {
                $sql_query .= " AND gtia_sd.afecta_explotacion = $aeh";
            }

            // FECHA REPORTE DESDE
            if ($reportes_desde != '') {
                $sql_query .= " AND gtia_sd.fecha_reporte >= '$reportes_desde'";
            }

            // FECHA REPORTE HASTA
            if ($reportes_hasta != '') {
                $sql_query .= " AND gtia_sd.fecha_reporte <= '$reportes_hasta'";
            }

            // FECHA SOLUCION DESDE
            if ($solucion_desde != '') {
                $sql_query .= " AND gtia_sd.fecha_solucion >= '$solucion_desde'";
            }

            // FECHA SOLUCION HASTA
            if ($solucion_hasta != '') {
                $sql_query .= " AND gtia_sd.fecha_solucion <= '$solucion_hasta'";
            }

            // COSTO
            if ($hascosto == 'on' && is_numeric($filtercosto)) {
                $sql_query .= " AND gtia_sd.costo $criteriocosto $filtercosto";
            }


            //////////////////////////////////////////
            ///////     FILTROS ESTRUCTURA    ////////
            //////////////////////////////////////////
            // Si se filtra por Elemento de la estructura
            if ($objeto_parte != '') {

                if (strpos($objeto_parte, ',') == false) {

                    $proyect_name = $objeto_parte;

                    $sql_query .= " AND gtia_sd.proyecto = '$proyect_name'";
                    
                } elseif (strpos($objeto_parte, ',') !== false) {

                    $array_tree = explode(', ', $objeto_parte);

                    // Si es una Zona
                    if (count($array_tree) == 2) {

                        $proyect_name = $array_tree[0];
                        $array_zona = explode('Zona ', $array_tree[1]);
                        $zona_name = $array_zona[1];

                        $sql_query .= " AND gtia_sd.proyecto = '$proyect_name' AND gtia_sd.zona = '$zona_name'";
                    }
                    // Si es un Objeto
                    elseif (count($array_tree) == 3) {

                        $proyect_name = $array_tree[0];
                        $array_zona = explode('Zona ', $array_tree[1]);
                        $zona_name = $array_zona[1];
                        $objeto_name = $array_tree[2];

                        $sql_query .= " AND gtia_sd.proyecto = '$proyect_name' AND gtia_sd.zona = '$zona_name' AND gtia_sd.objeto_local LIKE'%$objeto_name%'";
                    }
                    // Si es una Parte
                    elseif (count($array_tree) == 4) {


                        $proyect_name = $array_tree[0];
                        $array_zona = explode('Zona ', $array_tree[1]);
                        $zona_name = $array_zona[1];
                        $objeto_name = $array_tree[2];
                        $parte_name = $array_tree[3];

                        $sql_query .= " AND gtia_sd.proyecto = '$proyect_name' AND gtia_sd.zona = '$zona_name' AND gtia_sd.objeto_local LIKE'%$parte_name%'";
                    }
                }
            }

            ///////////////////////////////////////////////
            ///////     FIN FILTROS ESTRUCTURA     ////////
            ///////////////////////////////////////////////                        

            // Total de SD
            if ($GLOBALS["polo"] == -1) {
                $sql_total = $sql_query;
            }
            else {
                $sql_query .= "AND gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $GLOBALS["polo"];
            }
            $qry_total = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_total);
            $total_reg = $qry_total->RecordCount();

            $sql_query .= " ORDER BY gtia_sd.id DESC OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";

            // Guardar la busqueda en Base de Datos
            $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_sd_find WHERE id_user = $id_usuario");
            if ($query->RecordCount() < 1) {
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_sd_find(id_user, descripcion, numero, problema, estructura, dpto, constructiva, suministro, afecta_explotacion, reportes_desde, reportes_hasta, solucion_desde, solucion_hasta, demora, criteriodemora, diasdemora, compra_imp, compra_nac, hascosto, criteriocosto, costo) VALUES($id_usuario, '$descripcion', '$numero', '$problema', '$objeto_parte', '$dpto', '$constructiva', '$suministro', '$aeh', '$reportes_desde', '$reportes_hasta', '$solucion_desde', '$solucion_hasta', '$demora', '$criteriodemora', '$diasdemora', '$compra_imp', '$compra_nac', '$hascosto', '$criteriocosto', $filtercosto)");
            } else {
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_sd_find SET descripcion = '$descripcion',numero = '$numero',problema = '$problema',estructura = '$objeto_parte',dpto = '$dpto',constructiva = '$constructiva',suministro = '$suministro',afecta_explotacion = '$aeh',reportes_desde = '$reportes_desde',reportes_hasta = '$reportes_hasta',solucion_desde = '$solucion_desde',solucion_hasta = '$solucion_hasta',demora = '$demora',criteriodemora = '$criteriodemora',diasdemora = '$diasdemora',compra_imp = '$compra_imp',compra_nac = '$compra_nac', hascosto = '$hascosto', criteriocosto = '$criteriocosto', costo = $filtercosto WHERE id_user = $id_usuario");
            }
            
        } else {
            //  Si no se realiza una busqueda avanzada...
            // Eliminar las busquedas en Base de Datos
            $id_usuario = $_SESSION['idsession'];
            $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_find WHERE id_user = $id_usuario");

            // Listar SD
            if ($GLOBALS["polo"] == -1) {
                $sql_total = "SELECT COUNT(id) AS ctdad FROM gtia_sd";
                $sql_query = "SELECT
                                gtia_sd.id,
                                gtia_sd.numero,
                                gtia_problemas.descripcion,
                                gtia_sd.descripcion,
                                gtia_sd.proyecto,
                                gtia_sd.zona,
                                gtia_sd.objeto_local,
                                gtia_sd.fecha_reporte,
                                gtia_sd.fecha_solucion,
                                gtia_sd.estado,
                                gtia_sd.constructiva,
                                gtia_sd.suministro,
                                gtia_sd.afecta_explotacion,
                                gtia_sd.comentario,
                                gtia_sd.fecha_mod,
                                gtia_sd.tipo_compra,
                                gtia_sd.documento,
                                gtia_proyectos.imagen,
                                gtia_sd.causa,
                                gtia_sd.fecha_almacen,
                                gtia_sd.costo
                            FROM
                                gtia_sd,
                                gtia_problemas,
                                gtia_proyectos
                            WHERE
                                gtia_sd.id_problema = gtia_problemas.id AND
                                gtia_sd.proyecto = gtia_proyectos.nombre";
            }
            else {
                $sql_total = "SELECT COUNT(gtia_sd.id) AS ctdad FROM gtia_sd, gtia_proyectos
                                WHERE gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $GLOBALS["polo"];
                $sql_query = "SELECT
                                gtia_sd.id,
                                gtia_sd.numero,
                                gtia_problemas.descripcion,
                                gtia_sd.descripcion,
                                gtia_sd.proyecto,
                                gtia_sd.zona,
                                gtia_sd.objeto_local,
                                gtia_sd.fecha_reporte,
                                gtia_sd.fecha_solucion,
                                gtia_sd.estado,
                                gtia_sd.constructiva,
                                gtia_sd.suministro,
                                gtia_sd.afecta_explotacion,
                                gtia_sd.comentario,
                                gtia_sd.fecha_mod,
                                gtia_sd.tipo_compra,
                                gtia_sd.documento,
                                gtia_proyectos.imagen,
                                gtia_sd.causa,
                                gtia_sd.fecha_almacen,
                                gtia_sd.costo
                            FROM
                                gtia_sd,
                                gtia_problemas,
                                gtia_proyectos
                            WHERE
                                gtia_sd.id_problema = gtia_problemas.id AND
                                gtia_sd.proyecto = gtia_proyectos.nombre AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $GLOBALS["polo"];
            }
            
            // Filtros Rápidos
            $listar_count = 0;
            if ($proyecto != 'Todos') {
                $listar_count++;
                $sql_query .= " AND gtia_proyectos.nombre = '$proyecto'";
                $sql_total .= " WHERE gtia_sd.proyecto = '$proyecto'";
            }
            if ($estado != 'Todos') {
                $sql_query .= " AND gtia_sd.estado = '$estado'";
                if($listar_count == 0){
                    $sql_total .= " WHERE gtia_sd.estado = '$estado'";
                }
                else{
                    $sql_total .= " AND gtia_sd.estado = '$estado'";
                }
                $listar_count++;
            }
            if ($tiposd == 'SD Comunes') {
                $sql_query .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
                if($listar_count == 0){
                    $sql_total .= " WHERE gtia_sd.objeto_local NOT LIKE '%BW%'";
                }
                else{
                    $sql_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
                }
            }
            if ($tiposd == 'SD Habitaciones') {
                $sql_query .= " AND gtia_sd.objeto_local LIKE '%BW%'";
                if($listar_count == 0){
                    $sql_total .= " WHERE gtia_sd.objeto_local LIKE '%BW%'";
                }
                else{
                    $sql_total .= " AND gtia_sd.objeto_local LIKE '%BW%'";
                }
            }
                   
            $sql_query .= " ORDER BY gtia_sd.id DESC OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY"; 
                        
            // Total de SD
            $qry_total = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_total);            
            $total_reg = $qry_total->fields[0];
        }

        // Ejecutar las consulta en la BD
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_query);
        
        $total = 0;

        // Construir el JSON de SD
        $response = '{"success": true, "sd": [';

        if ($query->RecordCount() > 0) {

            while (!$query->EOF) {

                $validar_demora = true;
                $id_sd          = $query->fields[0];

                // DEFINIR DPTOS
                $sd_dptos    = '';
                $count_dptos = 0;
                $sql_dptos   = "SELECT
                                    gtia_dptos.nombre
                                FROM
                                    gtia_dptos,
                                    gtia_sd_dpto
                                WHERE
                                    gtia_sd_dpto.id_sd = $id_sd AND
                                    gtia_sd_dpto.id_dpto = gtia_dptos.id";
                $qry_dptos   = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_dptos);
                while(!$qry_dptos->EOF){
                    $count_dptos++;
                    if($count_dptos == 1){
                        $sd_dptos .= $qry_dptos->fields[0];
                    }
                    else{
                        $sd_dptos .= ", ".$qry_dptos->fields[0]; 
                    }
                    $qry_dptos->MoveNext();
                }
                
                // CALCULAR DEMORA

                $suministro = $query->fields[11];
                $fecha_almacen = $query->fields[19];
                
                if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                    $calculo_demora = 0;
                }
                else {
                    $fin = ($query->fields[8] != '' && $query->fields[8] != null && $query->fields[8] == '1900-01-01') ? $query->fields[8] : date('Y-m-d');
                    if ($suministro == 'No') {
                        $inicio = $query->fields[7];
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
                        $inicio = $query->fields[7];
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

                if ($demora == 'on') {

                    // VALIDAR DEMORA
                    if ($criteriodemora == '<'){
                        $validar_demora = ($calculo_demora < $diasdemora);
                    }elseif ($criteriodemora == '<='){
                        $validar_demora = ($calculo_demora <= $diasdemora);
                    }elseif ($criteriodemora == '>'){
                        $validar_demora = ($calculo_demora > $diasdemora);
                    }elseif ($criteriodemora == '>='){
                        $validar_demora = ($calculo_demora >= $diasdemora);
                    }elseif ($criteriodemora == '='){
                        $validar_demora = ($calculo_demora = $diasdemora);
                    }
                }

                if ($validar_demora == true) {

                    $total++;
                    if ($total > 1) {
                        $response .= ',';
                    }

                    if ($query->fields[19] != '1900-01-01') {
                        $fecha_alm = $query->fields[19];
                    }else {
                        $fecha_alm = '';
                    }

                    // Agregar al JSON registros de SD
                    $response .= '{
                        id: "' . $id_sd. '", 
                        numero: "' . $query->fields[1] . '", 
                        problema: "' . utf8_encode($query->fields[2]) . '", 
                        descripcion: "' . utf8_encode($query->fields[3]) . '", 
                        proyecto: "' . utf8_encode($query->fields[4]) . '",
                        objeto: "' . utf8_encode($query->fields[6]) . '",
                        zona: "' . $query->fields[5] . '",
                        dpto: "' . utf8_encode($sd_dptos) . '",
                        fecha_reporte: "' . $query->fields[7] . '",
                        fechareporte_string: "' . $query->fields[7] . '",
                        fecha_solucion: "' . $query->fields[8] . '",
                        demora: "' . number_format($calculo_demora, 0) . '",
                        estado: "' . $query->fields[9] . '",
                        constructiva: "' . $query->fields[10] . '",
                        suministro: "' . $query->fields[11] . '",
                        afecta_explotacion: "' . $query->fields[12] . '",
                        comentario: "' . utf8_encode($query->fields[13]) . '",
                        documento: "' . utf8_encode($query->fields[16]) . '",
                        compra: "' . $query->fields[15] . '",
                        imagen: "' . $query->fields[17] . '",
                        causa: "'. $query->fields[18] .'",
                        fecha_almacen: "'. $fecha_alm .'",
                        costo: "'. $query->fields[20] .'"  
                    }';
                } 
                
                $query->MoveNext();
            }
                        
        }

        if ($demora == 'on') {
            $total_reg = $total;
        }

        // Cerrar JSON de SD
        $response .= '], "total": "' . $total_reg . '"}';

        return $response;
    }

    ////////////////////////////////////////////
    // Listar SD Pendientes
    function ReadSDPendientes($limit, $page, $listar) {

        $offset = ($limit * ($page - 1));

        // Validar Polo del usuario
        $polo = -1;
        if (intval($_SESSION['polo']) != 9) {
            $polo = intval($_SESSION['polo']);
        }
        
        // Declarar la consulta a la BD                 
        
        if ($polo == -1) {
            $sql_total = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE gtia_sd.estado = 'Por Resolver'";
            $sql = "SELECT
                        gtia_sd.id,
                        gtia_sd.numero,
                        gtia_sd.descripcion,
                        gtia_problemas.descripcion AS problema,
                        gtia_sd.proyecto,
                        gtia_sd.zona,
                        gtia_sd.objeto_local,
                        gtia_sd.fecha_reporte,
                        gtia_sd.fecha_solucion,
                        gtia_sd.constructiva,
                        gtia_sd.suministro,
                        gtia_sd.afecta_explotacion,
                        gtia_sd.comentario,
                        gtia_sd.tipo_compra,
                        gtia_sd.documento,
                        gtia_sd.causa,
                        gtia_sd.fecha_almacen,
                        gtia_sd.costo
                    FROM
                        gtia_sd,
                        gtia_problemas
                    WHERE
                        gtia_sd.id_problema = gtia_problemas.id AND
                        gtia_sd.estado = 'Por Resolver'";
        }
        else {
            $sql_total = "SELECT COUNT(gtia_sd.id) AS ctdad 
                            FROM gtia_sd, gtia_proyectos 
                            WHERE gtia_sd.estado = 'Por Resolver' AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND 
                                gtia_proyectos.id_polo = ". $polo;
            $sql = "SELECT
                        gtia_sd.id,
                        gtia_sd.numero,
                        gtia_sd.descripcion,
                        gtia_problemas.descripcion AS problema,
                        gtia_sd.proyecto,
                        gtia_sd.zona,
                        gtia_sd.objeto_local,
                        gtia_sd.fecha_reporte,
                        gtia_sd.fecha_solucion,
                        gtia_sd.constructiva,
                        gtia_sd.suministro,
                        gtia_sd.afecta_explotacion,
                        gtia_sd.comentario,
                        gtia_sd.tipo_compra,
                        gtia_sd.documento,
                        gtia_sd.causa,
                        gtia_sd.fecha_almacen,
                        gtia_sd.costo
                    FROM
                        gtia_sd,
                        gtia_problemas,
                        gtia_proyectos
                    WHERE
                        gtia_sd.id_problema = gtia_problemas.id AND
                        gtia_sd.estado = 'Por Resolver' AND 
                        gtia_proyectos.id = gtia_sd.id_proyecto AND 
                        gtia_proyectos.id_polo = ". $polo;
        }
        
        if($listar != ''){
            $listar_array = explode('.',$listar);
            $proyecto     = $listar_array[0];
            $tiposd       = $listar_array[1];
            
            if($proyecto != 'Todos'){
                $sql .= " AND gtia_sd.proyecto = '$proyecto'";
                $sql_total .= " AND gtia_sd.proyecto = '$proyecto'";
            }
            
            if ($tiposd == 'SD Comunes') {
                $sql .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
                $sql_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
            }
            
            if ($tiposd == 'SD Habitaciones') {
                $sql .= " AND gtia_sd.objeto_local LIKE '%BW%'";
                $sql_total .= " AND gtia_sd.objeto_local LIKE '%BW%'";
            } 
        }
                    
        $sql .= " ORDER BY
                    gtia_sd.id DESC
                OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
                
        // Total de SD
        $qry_total = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_total);            
        $total_reg = $qry_total->fields[0];            
            
        // Ejecutar la consulta en la BD
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

        // Construir el JSON de SD
        $response = '{"success": true, "total": "' . $total_reg . '", "sd": [';

        $count = 0;
        while (!$query->EOF) {

            $count++;

            // DEFINIR DPTOS
            $sd_dptos    = '';
            $id_sd       = $query->fields[0]; 
            $count_dptos = 0;
            $sql_dptos   = "SELECT
                                gtia_dptos.nombre
                            FROM
                                gtia_dptos,
                                gtia_sd_dpto
                            WHERE
                                gtia_sd_dpto.id_sd = $id_sd AND
                                gtia_sd_dpto.id_dpto = gtia_dptos.id";
            $qry_dptos   = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_dptos);
            while(!$qry_dptos->EOF){
                $count_dptos++;
                if($count_dptos == 1){
                    $sd_dptos .= $qry_dptos->fields[0];
                }
                else{
                    $sd_dptos .= ", ".$qry_dptos->fields[0]; 
                }
                $qry_dptos->MoveNext();
            }
            
            // CALCULAR DEMORA
            
            $suministro = $query->fields[10];
            $fecha_almacen = $query->fields[16];
            
            if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                $calculo_demora = 0;
            }
            else {
                $fin = ($query->fields[8] != '' && $query->fields[1] != null && $query->fields[8] == '1900-01-01') ? $query->fields[1] : date('Y-m-d');
                if ($suministro == 'No') {
                    $inicio = $query->fields[7];
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
                       $inicio = $query->fields[7];
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
            

            if ($count > 1) {
                $response .= ',';
            }

            if ($query->fields[16] != '1900-01-01') {
                $fecha_alm = $query->fields[16];
            }else {
                $fecha_alm = '';
            }

            // Agregar al JSON registros de SD
            $response .= '{
                id:"' . $id_sd . '", 
                numero: "' . $query->fields[1] . '",
                descripcion: "' . utf8_encode($query->fields[2]) . '", 
                problema: "' . utf8_encode($query->fields[3]) . '",  
                proyecto: "' . utf8_encode($query->fields[4]) . '",
                zona: "' . $query->fields[5] . '",
                objeto: "' . utf8_encode($query->fields[6]) . '",
                dpto: "' . utf8_encode($sd_dptos) . '",
                fecha_reporte: "' . $query->fields[7] . '",
                fecha_solucion: "' . $query->fields[8] . '",
                demora: "' . number_format($calculo_demora, 0) . '",
                constructiva: "' . $query->fields[9] . '",
                suministro: "' . $query->fields[10] . '",
                afecta_explotacion: "' . $query->fields[11] . '",
                comentario: "' . utf8_encode($query->fields[12]) . '",
                compra: "' . $query->fields[13] . '",   
                documento: "' . utf8_encode($query->fields[14]) . '",
                causa: "'. $query->fields[15] .'",
                fecha_almacen: "'. $fecha_alm .'",
                costo: "'. $query->fields[17] .'"
            }';

            $query->MoveNext();
        }
        
        $response .= ']}';

        return $response;
    }

    ////////////////////////////////////////////
    // Obtener texto del treepicker de proyectos
    function GetTextTreepicker($element, $id_element) {

        if ($element == 3) {

            $sql = "SELECT gtia_objetos.nombre AS objeto,gtia_zonas.nombre AS zona,gtia_proyectos.nombre AS proyecto FROM gtia_objetos,gtia_zonas,gtia_proyectos WHERE gtia_objetos.id = $id_element AND gtia_objetos.id_zona = gtia_zonas.id AND gtia_zonas.id_proyecto = gtia_proyectos.id";
            if ($qry = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql)) {

                $texto = 'Proyecto ' . $qry->fields[2] . ', Zona ' . $qry->fields[1] . ', ' . $qry->fields[0];

                $response = json_encode(array(
                    "success" => true,
                    "texto" => "" . $texto . "",
                ));
            } else {
                $response = json_encode(array(
                    "failure" => true,
                    "message" => $GLOBALS["message"][2]
                ));
            }
        } elseif ($element == 4) {

            $sql = "SELECT gtia_partes.nombre AS parte, gtia_objetos.nombre AS objeto, gtia_zonas.nombre AS zona, gtia_proyectos.nombre AS proyecto FROM gtia_partes, gtia_objetos, gtia_zonas, gtia_proyectos WHERE gtia_partes.id = $id_element AND gtia_partes.id_objeto = gtia_objetos.id AND gtia_objetos.id_zona = gtia_zonas.id AND gtia_zonas.id_proyecto = gtia_proyectos.id";
            if ($qry = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql)) {

                $texto = 'Proyecto ' . $qry->fields[3] . ', Zona ' . $qry->fields[2] . ', ' . $qry->fields[1] . ', ' . $qry->fields[0];

                $response = json_encode(array(
                    "success" => true,
                    "texto" => "" . $texto . "",
                ));
            } else {
                $response = json_encode(array(
                    "failure" => true,
                    "message" => $GLOBALS["message"][2]
                ));
            }
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Insertar objeto/parte en Form de nueva SD
    function sdFormAddGrid($ruta, $ubicacion, $estado) {

        $id_user = $_SESSION['idsession'];

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_sd_objetospartes_temp(id_user,ruta,ubicacion, estado) VALUES($id_user,'$ruta','$ubicacion','$estado')");

        if (!$GLOBALS["adoMYSQL_SEMTI"]->HasFailedTrans()) {

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

    // Modificar objeto/parte en Form de nueva SD
    function sdFormUpdGrid($id_row, $ubicacion, $estado) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_sd_objetospartes_temp SET ubicacion = '$ubicacion', estado = '$estado' WHERE id = $id_row");

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

    // Eliminar objeto/parte en Form de nueva SD
    function sdFormDelGrid($id_row) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_objetospartes_temp WHERE id = $id_row");

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

    // Insertar nueva SD
    function SdInsert($numero, $problema, $proyecto, $descripcion, $objectArray, $dpto, $fecha_reporte, $fecha_solucion, $estado, $constructiva, $suministro, $compra, $afecta_explotacion, $comentario, $document, $nombreDocument, $causa, $fecha_almacen, $costo) {

        $dpto = str_replace(',', ', ', $dpto);
        $nombreDocument = str_replace(' ', '', $nombreDocument);
        $costo = floatval($costo);

        // Inicializar la codificacion utf8 en la BD
        //$GLOBALS["adoMYSQL_SEMTI"]->Execute('SET NAMES UTF8');

        // ID proyecto
        $qry_proyecto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_proyectos WHERE nombre = '$proyecto'");
        $id_proyecto  = $qry_proyecto->fields[0];

        $costo = number_format(str_replace(',', '.', $costo), 2, '.', '');

        // Validar el numero de la SD
        $qry_checksd = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_sd WHERE numero = $numero AND proyecto = '$proyecto'");
        if ($qry_checksd->RecordCount() > 0) {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][3]
            ));
        } else {

            // consulta sql (Transacciones Inteligentes)
            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();
            
            // Comprobar el Problema
            /////////////////////////////////////
            $qry_prob = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @EXIST BIT; EXEC @EXIST = Gtia_problemas_Exist '$problema'; SELECT @EXIST");

            if (trim($qry_prob) == 1) {
                // Seleccionar el ID del problema
                $qry_problemid = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @ID_PROBLEMA INT; EXEC @ID_PROBLEMA = Gtia_problemas_SelectID '$problema'; SELECT @ID_PROBLEMA");
                $id_prob = trim($qry_problemid);
            } else {
                // Insertar el nuevo problema y seleccionar su ID
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_problemas(descripcion) VALUES('$problema')");
                $qry_prob = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_problemas ORDER BY id DESC");
                $id_prob = $qry_prob->fields[0];
            }
            
            // Insertar la nueva SD
            /////////////////////////////////////
            if ($fecha_solucion == '') {
                $sql = "INSERT INTO gtia_sd(numero, id_problema, descripcion, fecha_reporte, fecha_solucion, estado, constructiva, suministro, afecta_explotacion, comentario, fecha_mod, tipo_compra, documento, causa, fecha_almacen, costo, id_proyecto) VALUES($numero, $id_prob, '$descripcion', '$fecha_reporte', null, '$estado', '$constructiva', '$suministro', '$afecta_explotacion', '$comentario', '" . date('Y-m-d') . "', '$compra', '$nombreDocument', '$causa', '$fecha_almacen', $costo, $id_proyecto)";
            } else {
                $sql = "INSERT INTO gtia_sd(numero, id_problema, descripcion, fecha_reporte, fecha_solucion, estado, constructiva, suministro, afecta_explotacion, comentario, fecha_mod, tipo_compra, documento, causa, fecha_almacen, costo, id_proyecto) VALUES($numero, $id_prob, '$descripcion', '$fecha_reporte', '$fecha_solucion', '$estado', '$constructiva', '$suministro', '$afecta_explotacion', '$comentario', '" . date('Y-m-d') . "', '$compra', '$nombreDocument', '$causa', '$fecha_almacen', $costo, $id_proyecto)";
            }

            if ($GLOBALS["adoMSSQL_SEMTI"]->Execute($sql)) {

                // Obtener el ID de la nueva SD
                $qry_newsd = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT TOP 1 id FROM gtia_sd ORDER BY id DESC");
                $id_newsd  = $qry_newsd->fields[0];

                // Insertar el/los Dptos
                ////////////////////////////////////////////
                $dptos_array = explode(', ',$dpto);
                $ctdad_dptos = count($dptos_array);
                for($d = 0; $d < $ctdad_dptos; $d++){
                    
                    $dpto_name = $dptos_array[$d];
                    $qry_dpto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @EXIST BIT; EXEC @EXIST = Gtia_dptos_Exist '$dpto_name'; SELECT @EXIST");
                
                    if (trim($qry_dpto) == 1) {
                        // Seleccionar el ID del dpto
                        $qry_dptoid = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @ID_DPTO INT; EXEC @ID_DPTO = Gtia_dptos_SelectID '$dpto_name'; SELECT @ID_DPTO");
                        $id_dpto = trim($qry_dptoid);
                    } else {
                        // Insertar el nuevo dpto y seleccionar su ID
                        $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_dptos(nombre) VALUES('$dpto_name')");
                        $qry_dpto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_dptos ORDER BY id DESC");
                        $id_dpto = $qry_dpto->fields[0];
                    }
                    
                    $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_sd_dpto(id_sd,id_dpto) VALUES($id_newsd,$id_dpto)");
                }
            
                // Declarar los campos Proyecto,Zona y Objeto que seran insertados en la nueva SD
                $newsd_proyecto = '';
                $newsd_zona = '';
                $newsd_objetos = '';
                $count_objectos = 0;

                // Asignar los objetos o partes de la nueva SD
                $records = json_decode(stripslashes($objectArray));  // stripslashes($objectArray)

                foreach ($records as $record) {

                    // Obtener Objeto o Parte
                    $ruta       = $record->ruta;
                    $ruta_items = explode(',', $ruta);
                    $ubicacion  = $record->ubicacion;
                    $estado     = $record->estado;
                                        
                    $zona     = substr($ruta_items[1], 6);
                    $zona_str = (string) $zona;

                    $objeto     = substr($ruta_items[2], 1);
                    $objeto_str = (string) $GLOBALS['cadenas']->latin1($GLOBALS['cadenas']->codificarBD_utf8($objeto));

                    // Proyecto
                    $qry_proyect    = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_proyectos_SelectByName '$ruta_items[0]'");
                    $id_proyect     = $qry_proyect->fields[0];
                    $newsd_proyecto = $qry_proyect->fields[1];

                    // Zona
                    $qry_zona   = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_zonas_SelectByName $id_proyect,'$zona_str'");
                    $id_zona    = $qry_zona->fields[0];
                    $newsd_zona = $qry_zona->fields[1];

                    // Objeto
                    $qry_objeto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_objetos_SelectByName $id_zona,'$objeto_str'");
                    $id_objeto = $qry_objeto->fields[0];

                    // Si es una Parte
                    if (count($ruta_items) == 4) {

                        $count_objectos++;

                        $parte = substr($ruta_items[3], 1);
                        $parte_str = (string) $GLOBALS['cadenas']->latin1($GLOBALS['cadenas']->codificarBD_utf8($parte));       //  $GLOBALS['cadenas']->utf8($parte)

                        $qry_parte = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_partes_SelectByName $id_objeto,'$parte_str'");
                        $id_parte = $qry_parte->fields[0];

                        if ($count_objectos == 1) {
                            $newsd_objetos = $qry_objeto->fields[1] . ' (' . $qry_parte->fields[1] . ')';
                        } else {
                            $newsd_objetos .= ', ' . $qry_objeto->fields[1] . ' (' . $qry_parte->fields[1] . ')';
                        }

                        // SQL Insertar parte
                        $sql_objects = "INSERT INTO gtia_sd_partes(id_parte,id_sd,ubicacion,estado) VALUES($id_parte,$id_newsd,'$ubicacion','$estado')";
                        
                    } elseif (count($ruta_items) == 3) {

                        $count_objectos++;

                        if ($count_objectos == 1) {
                            $newsd_objetos = $qry_objeto->fields[1];
                        } else {
                            $newsd_objetos .= ', ' . $qry_objeto->fields[1];
                        }

                        // SQL Insertar objeto
                        $sql_objects = "INSERT INTO gtia_sd_objetos(id_objeto,id_sd,ubicacion,estado) VALUES($id_objeto,$id_newsd,'$ubicacion','$estado')";
                    }

                    $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_objects);
                }
                
                // Actualizar la nueva SD con los campos Proyecto,Zona,Objeto
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_sd SET proyecto = '$newsd_proyecto',zona = '$newsd_zona',objeto_local = '$newsd_objetos' WHERE id = $id_newsd");

                // Borrar los objetos temporales de la SD
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_objetospartes_temp WHERE id_user = " . $_SESSION['idsession']);
            }

            if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {

                // Subir la Imagen del proyecto al servidor
                if ($nombreDocument != '') {

                    copy($document['tmp_name'], '../../resources/documents/SD/' . $nombreDocument);
                    //rename('../../resources/documents/SD/'.$nombreDocument, '../../resources/documents/SD/prueba.pdf');
                }

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
    
    // Actualizar SD
    function SdUdate($id, $numero, $problema, $proyecto, $descripcion, $objectArray, $dpto, $fecha_reporte, $fecha_solucion, $estado, $constructiva, $suministro, $compra, $afecta_explotacion, $comentario, $document, $nombreDocument, $causa, $fecha_almacen, $costo) {

        $dpto = str_replace(',', ', ', $dpto);
        $nombreDocument = str_replace(' ', '', $nombreDocument);
        $costo = floatval($costo);

        // ID proyecto
        $qry_proyecto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_proyectos WHERE nombre = '$proyecto'");
        $id_proyecto  = $qry_proyecto->fields[0];

        $costo = number_format(str_replace(',', '.', $costo), 2, '.', '');

        // Validar el numero de la SD
        $checknumber = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_sd WHERE id != $id AND numero = $numero AND proyecto = '$proyecto'");
        if ($checknumber->RecordCount() > 0) {
            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][4]
            ));
        } else {

            // Validar y Subir archivo al servidor
            if ($nombreDocument != '') {

                $sql_valida = "SELECT id FROM gtia_sd WHERE id != $id AND documento = '$nombreDocument'";
                $query_valida = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_valida);

                if ($query_valida->RecordCount() > 0) {

                    $response = json_encode(array(
                        "failure" => true,
                        "message" => $GLOBALS["message"][9]
                    ));
                } else {

                    $sql_document = "SELECT documento FROM gtia_sd WHERE id = $id";
                    $qry_document = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_document);

                    $bd_archivo = $qry_document->fields[0];
                    $bd_archivo = (string) $bd_archivo;
                    $caracteres = strlen($bd_archivo);

                    // Eliminar el archivo almacenado en BD del servidor si se envia uno nuevo
                    if ($caracteres > 0) {

                        $link = "../../resources/documents/SD/" . $bd_archivo;
                        $link = (string) $link;
                        unlink($link);
                    }

                    copy($document['tmp_name'], '../../resources/documents/SD/' . $nombreDocument);
                }
            }

            // consulta sql (Transacciones Inteligentes)
            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            /*// Comprobar el Dpto
            /////////////////////////////////////
            $qry_dpto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @EXIST BIT; EXEC @EXIST = Gtia_dptos_Exist '$dpto'; SELECT @EXIST");
            
            if (trim($qry_dpto) == 1) {
                // Seleccionar el ID del dpto
                $qry_dptoid = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @ID_DPTO INT; EXEC @ID_DPTO = Gtia_dptos_SelectID '$dpto'; SELECT @ID_DPTO");
                $id_dpto = trim($qry_dptoid);
            }else {
                // Insertar el nuevo dpto y seleccionar su ID
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_dptos(nombre) VALUES('$dpto')");
                $qry_dptoid = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT TOP 1 id FROM gtia_dptos ORDER BY id DESC");
                $id_dpto = $qry_dptoid->fields[0];
            }*/
            
            // Comprobar el Problema
            /////////////////////////////////////
            $qry_prob = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @EXIST BIT; EXEC @EXIST = Gtia_problemas_Exist '$problema'; SELECT @EXIST");

            if (trim($qry_prob) == 1) {
                // Seleccionar el ID del problema
                $qry_problemid = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @ID_PROBLEMA INT; EXEC @ID_PROBLEMA = Gtia_problemas_SelectID '$problema'; SELECT @ID_PROBLEMA");
                $id_prob = trim($qry_problemid);
            } else {
                // Insertar el nuevo problema y seleccionar su ID
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_problemas(descripcion) VALUES('$problema')");
                $qry_prob = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_problemas ORDER BY id DESC");
                $id_prob = $qry_prob->fields[0];
            }
            
            // Actualizar la SD

            $sql = "UPDATE gtia_sd SET numero = $numero,id_problema = $id_prob,descripcion = '$descripcion',fecha_reporte = '$fecha_reporte',fecha_solucion = NULL,estado = '$estado',constructiva = '$constructiva',suministro = '$suministro',afecta_explotacion = '$afecta_explotacion',comentario = '$comentario',fecha_mod = '" . date('Y-m-d') . "',tipo_compra = '$compra', causa = '$causa', fecha_almacen = '$fecha_almacen', costo = $costo, id_proyecto = $id_proyecto WHERE id = $id";
            
            if ($GLOBALS["adoMSSQL_SEMTI"]->Execute($sql)) {

                // Borrar todos los objetos/partes/dptos de la SD antes de actualizar
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_objetos WHERE id_sd = $id");
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_partes WHERE id_sd = $id");
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_dpto WHERE id_sd = $id");
                
                // Insertar el/los Dptos
                ////////////////////////////////////////////
                $dptos_array = explode(', ',$dpto);
                $ctdad_dptos = count($dptos_array);
                for($d = 0; $d < $ctdad_dptos; $d++){
                    
                    $dpto_name = $dptos_array[$d];
                    $qry_dpto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @EXIST BIT; EXEC @EXIST = Gtia_dptos_Exist '$dpto_name'; SELECT @EXIST");
                
                    if (trim($qry_dpto) == 1) {
                        // Seleccionar el ID del dpto
                        $qry_dptoid = $GLOBALS["adoMSSQL_SEMTI"]->Execute("DECLARE @ID_DPTO INT; EXEC @ID_DPTO = Gtia_dptos_SelectID '$dpto_name'; SELECT @ID_DPTO");
                        $id_dpto = trim($qry_dptoid);
                    } else {
                        // Insertar el nuevo dpto y seleccionar su ID
                        $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_dptos(nombre) VALUES('$dpto_name')");
                        $qry_dpto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_dptos ORDER BY id DESC");
                        $id_dpto = $qry_dpto->fields[0];
                    }
                    
                    $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_sd_dpto(id_sd,id_dpto) VALUES($id,$id_dpto)");
                }
                
                // Actualizar campo FechaSolucion
                if ($fecha_solucion != ''){
                    $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_sd SET fecha_solucion = '$fecha_solucion' WHERE id = $id");
                }
                
                // Actualizar campo Documento
                if ($nombreDocument != ''){
                    $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_sd SET documento = '$nombreDocument' WHERE id = $id");
                }

                // Declarar los campos Proyecto,Zona y Objeto que seran actualizados en la SD
                $newsd_proyecto = '';
                $newsd_zona     = '';
                $newsd_objetos  = '';
                $count_objectos = 0;

                // Asignar los objetos o partes de la SD
                $records = json_decode(stripslashes($objectArray));

                foreach ($records as $record) {

                    // Obtener Objeto o Parte
                    $ruta       = $record->ruta;
                    $ruta_items = explode(',', $ruta);
                    $ubicacion  = $GLOBALS['cadenas']->latin1($GLOBALS['cadenas']->codificarBD_utf8($record->ubicacion));
                    $estado     = $record->estado;
                    
                    $zona     = substr($ruta_items[1], 6);
                    $zona_str = (string) $zona;

                    $objeto     = substr($ruta_items[2], 1);
                    $objeto_str = (string) $GLOBALS['cadenas']->utf8($GLOBALS['cadenas']->codificarBD_utf8($objeto));

                    // Proyecto
                    $qry_proyect    = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_proyectos_SelectByName '$ruta_items[0]'");
                    $id_proyect     = $qry_proyect->fields[0];
                    $newsd_proyecto = $qry_proyect->fields[1];

                    // Zona
                    $qry_zona   = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_zonas_SelectByName $id_proyect,'$zona_str'");
                    $id_zona    = $qry_zona->fields[0];
                    $newsd_zona = $qry_zona->fields[1];

                    // Objeto
                    $qry_objeto = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_objetos_SelectByName $id_zona,'$objeto_str'");
                    $id_objeto = $qry_objeto->fields[0];

                    // Si es una Parte
                    if (count($ruta_items) == 4) {

                        $count_objectos++;

                        $parte = substr($ruta_items[3], 1);
                        $parte_str = (string) $GLOBALS['cadenas']->utf8($GLOBALS['cadenas']->codificarBD_utf8($parte));
                        
                        $qry_parte = $GLOBALS["adoMSSQL_SEMTI"]->Execute("Gtia_partes_SelectByName $id_objeto,'$parte_str'");
                        $id_parte = $qry_parte->fields[0];

                        if ($count_objectos == 1) {
                            $newsd_objetos = $qry_objeto->fields[1] . ' (' . $parte_str . ')';
                        } else {
                            $newsd_objetos .= ', ' . $qry_objeto->fields[1] . ' (' . $parte_str . ')';
                        }
                        /* return  json_encode(array(
                          "failure" => true,
                          "message" => $newsd_objetos
                          )); */
                        // SQL Insertar parte
                        $sql_objects = "INSERT INTO gtia_sd_partes(id_parte,id_sd,ubicacion,estado) VALUES($id_parte,$id,'$ubicacion','$estado')";
                        
                    } elseif (count($ruta_items) == 3) {

                        $count_objectos++;

                        if ($count_objectos == 1) {
                            $newsd_objetos = $qry_objeto->fields[1];
                        } else {
                            $newsd_objetos .= ', ' . $qry_objeto->fields[1];
                        }

                        // SQL Insertar objeto
                        $sql_objects = "INSERT INTO gtia_sd_objetos(id_objeto,id_sd,ubicacion) VALUES($id_objeto,$id,'$ubicacion')";
                    }

                    $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_objects);
                }

                // Actualizar la nueva SD con los campos Proyecto,Zona,Objeto
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("UPDATE gtia_sd SET proyecto = '$newsd_proyecto',zona = '$newsd_zona',objeto_local = '$newsd_objetos' WHERE id = $id");

                // Borrar los objetos temporales de la SD
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_objetospartes_temp WHERE id_user = " . $_SESSION['idsession']);
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
    // Eliminar SD
    function SdDelete($idSD) {

        // consulta sql (Transacciones Inteligentes).
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        // Eliminar el archivo del contrato
        $sql = "SELECT documento FROM gtia_sd WHERE id = $idSD";
        $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
        $bd_archivo = $query->fields[0];
        $bd_archivo = (string) $bd_archivo;
        $caracteres = strlen($bd_archivo);

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("EXEC Gtia_sd_Delete $idSD");

        if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {

            if ($caracteres > 0) {

                $link = "../../resources/documents/SD/" . $bd_archivo;
                $link = (string) $link;
                unlink($link);
            }

            $response = json_encode(array(
                "success" => true,
                "parte" => $parte
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
    //  Eliminar SD Check
    function SdCheckDelete($parametros) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $records = json_decode(stripslashes($parametros));
        foreach ($records as $record) {

            $id_SD = $record->id;

            // Eliminar el archivo del contrato
            $sql = "SELECT documento FROM gtia_sd WHERE id = $id_SD";
            $query = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);
            $bd_archivo = $query->fields[0];
            $bd_archivo = (string) $bd_archivo;
            $caracteres = strlen($bd_archivo);

            if ($GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd WHERE id = $id_SD")) {

                if ($caracteres > 0) {

                    $link = "../../resources/documents/SD/" . $bd_archivo;
                    $link = (string) $link;
                    unlink($link);
                }
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
    // Cargar campos del Form de SD
    function SdFormLoad($id_sd) {

        $qry_sd = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT 
                        gtia_sd.id,
                        gtia_sd.numero,				
                        gtia_sd.descripcion,
                        gtia_problemas.descripcion AS problema,
                        gtia_sd.fecha_reporte,
                        gtia_sd.constructiva,
                        gtia_sd.afecta_explotacion,
                        gtia_sd.suministro,
                        gtia_sd.tipo_compra,
                        gtia_sd.estado,
                        gtia_sd.fecha_solucion,
                        gtia_sd.comentario,
                        gtia_sd.documento,
                        gtia_sd.proyecto,
                        gtia_sd.causa,
                        gtia_sd.fecha_almacen,
                        gtia_sd.costo
                        FROM 
                            gtia_sd, gtia_problemas
                        WHERE
                            gtia_sd.id = $id_sd AND 
                            gtia_sd.id_problema = gtia_problemas.id");
            
        if ($qry_sd->RecordCount() > 0) {
            
            // Constructiva, Suministro, AEH
            if ($qry_sd->fields[5] == 'Si'){
                $constructiva = 'true';
            }else{
                $constructiva = 'false';
            }
            if ($qry_sd->fields[7] == 'Si'){
                $suministro = 'true';
            }else{
                $suministro = 'false';
            }
            if ($qry_sd->fields[6] == 'Si'){
                $afecta_explot = 'true';
            }else{
                $afecta_explot = 'false';
            }
            
            // Definir Dpto
            $sd_dpto    = '';
            $dpto_count = 0;
            $qry_dpto   = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT
                                                                   dbo.gtia_dptos.nombre
                                                               FROM
                                                                   dbo.gtia_dptos
                                                               INNER JOIN
                                                                   dbo.gtia_sd_dpto
                                                               ON
                                                                   dbo.gtia_dptos.id = dbo.gtia_sd_dpto.id_dpto
                                                               WHERE
                                                                   (dbo.gtia_sd_dpto.id_sd = $id_sd)");
            while(!$qry_dpto->EOF){
                $dpto_count++;
                if($dpto_count == 1){
                    $sd_dpto .= $qry_dpto->fields[0];
                }
                else{
                    $sd_dpto .= ', '.$qry_dpto->fields[0];
                }
                $qry_dpto->MoveNext();
            }
            
            $response = json_encode(array(
                "success" => true,
                "data" => array(
                    "id" => $id_sd,
                    "numero" => $qry_sd->fields[1],
                    "descripcion" => $GLOBALS['cadenas']->utf8($qry_sd->fields[2]),
                    "dpto" => $GLOBALS['cadenas']->utf8($sd_dpto),
                    "problema" => $GLOBALS['cadenas']->utf8($qry_sd->fields[3]),
                    "fecha_reporte" => $qry_sd->fields[4],
                    "constructiva" => $constructiva,
                    "suministro" => $suministro,
                    "afecta_explotacion" => $afecta_explot,
                    "compra" => $qry_sd->fields[8],                    
                    "estado" => $qry_sd->fields[9],
                    "fecha_solucion" => $qry_sd->fields[10],
                    "comentario" => $qry_sd->fields[11],
                    "documento" => $qry_sd->fields[12],
                    "proyecto" => $qry_sd->fields[13],
                    "causa" => $GLOBALS['cadenas']->utf8($qry_sd->fields[14]),
                    "fecha_almacen" => $qry_sd->fields[15],
                    "costo" => $qry_sd->fields[16]
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
    // Cargar campos de Filtros de SD
    function SdFiltrosLoad() {

        $id_user = $_SESSION['idsession'];
        $qry_sd = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT 
                        descripcion,
                        numero,				
                        problema,
                        dpto,
                        constructiva,
                        suministro,
                        afecta_explotacion,
                        reportes_desde,
                        reportes_hasta,
                        solucion_desde,
                        solucion_hasta,
                        demora,
                        criteriodemora,
                        diasdemora,
                        compra_imp,
                        compra_nac,
                        hascosto,
                        criteriocosto,
                        costo
                        FROM 
                            gtia_sd_find
                        WHERE
                            id_user = $id_user");          

        if ($qry_sd->RecordCount() > 0) {

            // Fechas
            if ($qry_sd->fields[7] != '1900-01-01'){
                $reportes_desde = $qry_sd->fields[7];
            }else{
                $reportes_desde = '';
            }if ($qry_sd->fields[8] != '1900-01-01'){
                $reportes_hasta = $qry_sd->fields[8];
            }else{
                $reportes_hasta = '';
            }if ($qry_sd->fields[9] != '1900-01-01'){
                $solucion_desde = $qry_sd->fields[9];
            }else{
                $solucion_desde = '';
            }if ($qry_sd->fields[10] != '1900-01-01'){
                $solucion_hasta = $qry_sd->fields[10];
            }else{
                $solucion_hasta = '';
            }

            $response = json_encode(array(
                "success" => true,
                "data" => array(
                    "descripcion" => $GLOBALS['cadenas']->utf8($qry_sd->fields[0]),
                    "numero" => $qry_sd->fields[1],
                    "problema" => $GLOBALS['cadenas']->utf8($qry_sd->fields[2]),
                    "dpto" => $GLOBALS['cadenas']->utf8($qry_sd->fields[3]),
                    "constructiva" => $qry_sd->fields[4],
                    "suministro" => $qry_sd->fields[5],
                    "afecta_explotacion" => $qry_sd->fields[6],
                    "reportes_desde" => $reportes_desde,
                    "reportes_hasta" => $reportes_hasta,
                    "solucion_desde" => $solucion_desde,
                    "solucion_hasta" => $solucion_hasta,
                    "demora" => $qry_sd->fields[11],
                    "criteriodemora" => $qry_sd->fields[12],
                    "diasdemora" => $qry_sd->fields[13],
                    "compra_imp" => $qry_sd->fields[14],
                    "compra_nac" => $qry_sd->fields[15],
                    "hascosto" => $qry_sd->fields[16],
                    "criteriocosto" => $qry_sd->fields[17],
                    "costo" => $qry_sd->fields[18]
                )
            ));
        }
        else {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][2]
            ));
        }

        return $response;
    }

    ////////////////////////////////////////////		
    // Cargar Objetos/Partes de la SD
    function SdFormObjectLoad($id_sd) {

        $id_user = $_SESSION['idsession'];

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        // Listar los objetos
        $sql_obj = "SELECT id_objeto, id_sd, ubicacion, estado FROM gtia_sd_objetos WHERE id_sd = $id_sd";
        $qry_obj = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_obj);
        $tot_obj = $qry_obj->RecordCount();
        $count   = 0;

        if ($tot_obj > 0) {
            while (!$qry_obj->EOF) {

                $count++;
                $ubicacion = $qry_obj->fields[2];
                $estado    = $qry_obj->fields[3];
                $id_objeto = $qry_obj->fields[0];

                $sql_ruta  = "SELECT 
                                gtia_objetos.nombre AS objeto, 
                                gtia_zonas.nombre AS zona, 
                                gtia_proyectos.nombre AS proyecto 
                            FROM gtia_objetos, gtia_zonas, gtia_proyectos 
                            WHERE gtia_objetos.id = $id_objeto AND 
                                gtia_objetos.id_zona = gtia_zonas.id AND 
                                gtia_zonas.id_proyecto = gtia_proyectos.id";
                                
                $qry_ruta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_ruta);

                $ruta_obj  = $qry_ruta->fields[2] . ', Zona ' . $qry_ruta->fields[1] . ', ' . $qry_ruta->fields[0];
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_sd_objetospartes_temp(id_user, ruta, ubicacion, estado) VALUES($id_user,'$ruta_obj','$ubicacion','$estado')");
                            
                $qry_obj->MoveNext();
            }
        }
                
        // Listar las partes
        $sql_part = "SELECT id_parte,id_sd,ubicacion,estado FROM gtia_sd_partes WHERE id_sd = $id_sd";
        $qry_part = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_part);
        $tot_part = $qry_part->RecordCount();
        $count    = 0;

        if ($tot_part > 0) {
            while (!$qry_part->EOF) {

                $count++;
                $ubicacion = $qry_part->fields[2];
                $estado    = $qry_part->fields[3];
                $id_parte  = $qry_part->fields[0];
                $sql_ruta  = "SELECT gtia_partes.nombre AS parte, gtia_objetos.nombre AS objeto, gtia_zonas.nombre AS zona, gtia_proyectos.nombre AS proyecto FROM gtia_partes, gtia_objetos, gtia_zonas, gtia_proyectos WHERE gtia_partes.id = $id_parte AND gtia_partes.id_objeto = gtia_objetos.id AND gtia_objetos.id_zona = gtia_zonas.id AND gtia_zonas.id_proyecto = gtia_proyectos.id";
                $qry_ruta  = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_ruta);
                $ruta_part = $qry_ruta->fields[3] . ', Zona ' . $qry_ruta->fields[2] . ', ' . $qry_ruta->fields[1] . ', ' . $qry_ruta->fields[0];
                $GLOBALS["adoMSSQL_SEMTI"]->Execute("INSERT INTO gtia_sd_objetospartes_temp(id_user,ruta,ubicacion,estado) VALUES($id_user,'$ruta_part','$ubicacion','$estado')");
                
                $qry_part->MoveNext();
            }
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
    // Limpiar la tabla temporal de objetos/partes
    function SdCleanGridTemp() {

        $id_user = $_SESSION['idsession'];

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute("DELETE FROM gtia_sd_objetospartes_temp WHERE id_user = $id_user");

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

    // Cargar campo Objeto_Parte de Filtros de SD
    function SdFiltrosObjectLoad() {

        $id_user = $_SESSION['idsession'];
        $qry_sd = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT estructura FROM gtia_sd_find WHERE id_user = $id_user");

        if ($qry_sd->RecordCount() > 0) {

            $response = json_encode(array(
                "success" => true,
                "objeto" => $GLOBALS['cadenas']->utf8($qry_sd->fields[0])
            ));
        } else {

            $response = json_encode(array(
                "success" => true,
                "objeto" => ""
            ));
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Preguntar existencia de Filtros de SD
    function SdExistFilters() {

        $id_usuario = $_SESSION['idsession'];
        $qry_sd = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT id FROM gtia_sd_find WHERE id_user = $id_usuario");

        if ($qry_sd->RecordCount() > 0) {

            $response = json_encode(array(
                "success" => true,
                "existe" => "true"
            ));
        } else {

            $response = json_encode(array(
                "success" => true,
                "existe" => "false"
            ));
        }

        return $response;
    }
    
    //////////////////////////////////////////////////////////////
    
    // GRAFICAS
    
    function ActualizarMetaSDDemora($meta){
        
        $fecha = date('Y-m-d');
                        
        $existe_meta = $GLOBALS["adoMSSQL_SEMTI"]->Execute("SELECT COUNT(id) AS ctdad FROM gtia_graficas_meta WHERE fecha == '$fecha'");
        
        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();
        
        if($existe_meta->fields[0] > 0){        
            $qry_meta = "UPDATE gtia_graficas_meta SET meta = $meta,fecha = '$fecha' WHERE id_grafica = 1";
        }
        else{
            $qry_meta = "INSERT INTO gtia_graficas_meta(id_grafica,meta,fecha) VALUES(1,$meta,'$fecha')";
        }
        $GLOBALS["adoMSSQL_SEMTI"]->Execute($qry_meta);
        
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