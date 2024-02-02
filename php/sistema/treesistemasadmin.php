<?php

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Asignar el icono del nodo/pagina
function getIcon($icon, $nodo) {
    if ($icon == 'Configuración') {
        return 'icon_admon';
    } elseif ($icon == 'Cambiar Contraseña') {
        return 'icon-password';
    } elseif ($icon == 'Roles y Permisos') {
        return 'icon-perfil';
    } elseif ($icon == 'Usuarios') {
        return 'icon-user';
    } elseif ($icon == 'Garantía') {
        return 'icon_garantia';
    } elseif ($icon == 'Estructuras de Proyectos') {
        return 'icon_proyecto';
    } elseif ($icon == 'Solicitudes de Defectación') {
        return 'icon_SD';
    } elseif ($icon == 'Departamentos' || $icon == 'Tipos de Problemas' || $icon == 'Parámetros Generales') {
        return 'icon_dptos';
    }  elseif ($icon == 'Informes' || $icon == 'Habitaciones Fuera de Orden' || $icon == 'Resumen de Garantía') {
        return 'icon_resumen';
    } elseif ($icon == 'Seguimiento a Indicadores') {
        return 'icon_dashboard';
    } elseif ($icon == 'SD Por Resolver') {
        return 'icon-chartpie';
    } elseif ($icon == 'SD  Const/Sumin/AEH' || $icon == 'Relación Tipo Defecto / Reportes SD' || $icon == 'SD que No Proceden' || $icon == 'Comparativa de SD entre Proyectos') {
        return 'icon-chartbar';
    } elseif ($icon == 'Demora Prom en SD' || $icon == 'Demora Prom en SD AEH' || $icon == 'Demora Prom SD Const, no AEH y no Sumin' || $icon == 'Demora Prom SD Const, AEH y no Sumin' || $icon == 'Demora Prom SD Const, AEH y Sumin' || $icon == 'Demora Prom SD Const, no AEH y Sumin') {
        return 'icon-chartline';
    } elseif ($icon == 'Tipo de Portada') {
        return 'icon_portada';
    }else {
        return 'doc_unknow_icon';
    }
}

/////////////////////////////////////////////////

include_once 'connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();


// Construir el arbol de navegacion
echo '{"success": true, "children": [';


//////////////////////////////
////////   MODULOS   /////////
//////////////////////////////

$query_modulos = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM syst_modulos WHERE nivel = 0");

$ctdad_modulos = $query_modulos->RecordCount();
$modulo = 1;


// Recorrer los Modulos primero para mostrar los que tengan permisos
while (!$query_modulos->EOF) {

    $checked = 'false';
    $modify = 'false';
    $readexp = 'false';
    $read = 'false';
    $write = 'false';

    // Obtener checks del Modulo
    if (isset($_GET['id_perfil'])) {

        $qry_total_paginas = $adoMSSQL_SEMTI->Execute("SELECT * FROM syst_paginas WHERE id_nodo = " . $query_modulos->fields[0]);
        $mod_total_paginas = $qry_total_paginas->RecordCount();

        if ($mod_total_paginas > 0) {

            // Checked
            $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_modulos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil']);
            $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

            if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                $checked = 'true';
            }
            // Modify
            $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_modulos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.modificar = 1");
            $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

            if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                $modify = 'true';
            }
            // Read & Export
            $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_modulos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.lectura_exportar = 1");
            $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

            if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                $readexp = 'true';
            }
            // Read
            $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_modulos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.lectura = 1");
            $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

            if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                $read = 'true';
            }
            // Write
            $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_modulos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.escritura = 1");
            $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

            if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                $write = 'true';
            }
        }
    }
    /////////////////////////////////

    echo    '{id:"1' . $query_modulos->fields[0] . '", 
            text: "' . $cadenas->utf8($query_modulos->fields[1]) . '", 
            iconCls: "' . getIcon($cadenas->utf8($query_modulos->fields[1]), '') . '", 
            cls: "system_name", 
            checked: ' . $checked . ',
            modificar: ' . $modify . ',
            lectura_exportar: ' . $readexp . ',
            lectura: ' . $read . ',
            escritura: ' . $write . ',
            expanded: true, 
            children: [';


    /////////////////////////////////////////
    ////////   NODOS HIJOS MODULO   /////////
    /////////////////////////////////////////

    $query_nodoshijos = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM syst_modulos WHERE padre = '" . $query_modulos->fields[1] . "'");

    $ctdad_nodoshijos = $query_nodoshijos->RecordCount();
    $nodohijo = 0;
    
    // Recorrer los Modulos primero para mostrar los que tengan permisos
    while (!$query_nodoshijos->EOF) {

        $checked = 'false';
        $modify = 'false';
        $readexp = 'false';
        $read = 'false';
        $write = 'false';
        $nodohijo++;

        // Obtener checks del Modulo
        if (isset($_GET['id_perfil'])) {

            $qry_total_paginas = $adoMSSQL_SEMTI->Execute("SELECT * FROM syst_paginas WHERE id_nodo = " . $query_nodoshijos->fields[0]);
            $mod_total_paginas = $qry_total_paginas->RecordCount();

            if ($mod_total_paginas > 0) {

                // Checked
                $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_nodoshijos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil']);
                $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

                if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                    $checked = 'true';
                }
                // Modify
                $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_nodoshijos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.modificar = 1");
                $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

                if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                    $modify = 'true';
                }
                // Read & Export
                $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_nodoshijos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.lectura_exportar = 1");
                $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

                if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                    $readexp = 'true';
                }
                // Read
                $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_nodoshijos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.lectura = 1");
                $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

                if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                    $read = 'true';
                }
                // Write
                $qry_mod_pag_conpermiso = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id FROM syst_paginas,syst_permisos,syst_modulos WHERE syst_modulos.id = " . $query_nodoshijos->fields[0] . " AND syst_paginas.id_nodo = syst_modulos.id AND syst_permisos.id_pagina = syst_paginas.id AND syst_permisos.id_perfil = " . $_GET['id_perfil'] . " AND syst_permisos.escritura = 1");
                $mod_total_paginas_conpermiso = $qry_mod_pag_conpermiso->RecordCount();

                if ($mod_total_paginas == $mod_total_paginas_conpermiso) {
                    $write = 'true';
                }
            }
        }
        /////////////////////////////////

        if ($nodohijo == 1) {
            echo '{';
        } elseif ($nodohijo <= $ctdad_nodoshijos) {
            echo ',{';
        }

        echo    'id:"1' . $query_nodoshijos->fields[0] . '", 
                text: "' . $cadenas->utf8($query_nodoshijos->fields[1]) . '", 
                iconCls: "' . getIcon($cadenas->utf8($query_nodoshijos->fields[1]), '') . '", 
                cls: "system_name", 
                checked: ' . $checked . ',
                modificar: ' . $modify . ',
                lectura_exportar: ' . $readexp . ',
                lectura: ' . $read . ',
                escritura: ' . $write . ',
                expanded: true, 
                children: [';

        
        ////////////////////////////////////////
        ////////   PAGINAS NODO HIJO   /////////
        ////////////////////////////////////////
        // Listar paginas del nodo
        $query_paginas = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id AS id,syst_paginas.nombre AS nombre FROM syst_paginas,syst_paginas_view WHERE syst_paginas.id_nodo = " . $query_nodoshijos->fields[0] . " AND syst_paginas.id = syst_paginas_view.id_pagina AND syst_paginas_view.id_view = 1");

        $ctdad_paginas = $query_paginas->RecordCount();
        $pagina = 0;

        while (!$query_paginas->EOF) {

            $pagina++;

            // Comprobar permisos de la pagina
            if (isset($_GET['id_perfil'])) {

                $query_pag_pemisos = $adoMSSQL_SEMTI->Execute("SELECT modificar,lectura_exportar,lectura,escritura FROM syst_permisos WHERE id_pagina = " . $query_paginas->fields[0] . " AND id_perfil = " . $_GET['id_perfil'] . "");

                if ($query_pag_pemisos->RecordCount() > 0) {

                    //$permiso = $query_pag_pemisos->FetchRow();
                    if ($query_pag_pemisos->fields[0] == 1) {
                        $modificar = 'true';
                    } else {
                        $modificar = 'false';
                    }
                    if ($query_pag_pemisos->fields[1] == 1) {
                        $lectura_exportar = 'true';
                    } else {
                        $lectura_exportar = 'false';
                    }
                    if ($query_pag_pemisos->fields[2] == 1) {
                        $lectura = 'true';
                    } else {
                        $lectura = 'false';
                    }
                    if ($query_pag_pemisos->fields[3] == 1) {
                        $escritura = 'true';
                    } else {
                        $escritura = 'false';
                    }

                    if ($pagina == 1) {
                        echo '{';
                    } else {
                        echo ',{';
                    }

                    // Imprimir los datos de cada pagina
                    echo    'id:"2' . $query_paginas->fields[0] . '", 
                            text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                            iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '", 
                            cls: "linked", 
                            checked: true, 
                            modificar: ' . $modificar . ',
                            lectura_exportar: ' . $lectura_exportar . ',
                            lectura: ' . $lectura . ',
                            escritura: ' . $escritura . ',
                            leaf: true}';
                } else {

                    if ($pagina == 1){
                        echo '{';
                    }
                    else{
                        echo ',{';
                    }

                    // Imprimir los datos de cada pagina
                    echo    'id:"2' . $query_paginas->fields[0] . '", 
                            text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                            iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '",
                            cls: "linked", 
                            checked: false, 
                            modificar: false,
                            lectura_exportar: false,
                            lectura: false,
                            escritura: false,
                            leaf: true}';
                }
            }
            else {

                if ($pagina == 1){
                    echo '{';
                }
                else{
                    echo ',{';
                }

                // Imprimir los datos de cada pagina
                echo    'id:"2' . $query_paginas->fields[0] . '", 
                        text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                        iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '",
                        cls: "linked", 
                        checked: false, 
                        modificar: false,
                        lectura_exportar: false,
                        lectura: false,
                        escritura: false,
                        leaf: true}';
            }
            /////////////////////////////////////////

            $query_paginas->MoveNext();
        }

        echo "]}";
        
        $query_nodoshijos->MoveNext();
    }
    ///////////////////////////////////////////
    //////    FIN NODOS HIJOS MODULOS    //////
    ///////////////////////////////////////////
    
    
    //////////////////////////////////////
    ////////   PAGINAS MODULOS   /////////
    //////////////////////////////////////
    // Listar paginas del nodo
    $query_paginas = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id AS id,syst_paginas.nombre AS nombre FROM syst_paginas,syst_paginas_view WHERE syst_paginas.id_nodo = " . $query_modulos->fields[0] . " AND syst_paginas.id = syst_paginas_view.id_pagina AND syst_paginas_view.id_view = 1");

    $ctdad_paginas = $query_paginas->RecordCount();
    $pagina = 1;

    while (!$query_paginas->EOF) {

        // Comprobar permisos de la pagina
        if (isset($_GET['id_perfil'])) {

            $query_pag_pemisos = $adoMSSQL_SEMTI->Execute("SELECT modificar,lectura_exportar,lectura,escritura FROM syst_permisos WHERE id_pagina = " . $query_paginas->fields[0] . " AND id_perfil = " . $_GET['id_perfil'] . "");

            if ($query_pag_pemisos->RecordCount() > 0) {

                //$permiso = $query_pag_pemisos->FetchRow();
                if ($query_pag_pemisos->fields[0] == 1){
                    $modificar = 'true';
                }
                else{
                    $modificar = 'false';
                }
                if ($query_pag_pemisos->fields[1] == 1){
                    $lectura_exportar = 'true';
                }
                else{
                    $lectura_exportar = 'false';
                }
                if ($query_pag_pemisos->fields[2] == 1){
                    $lectura = 'true';
                }
                else{
                    $lectura = 'false';
                }
                if ($query_pag_pemisos->fields[3] == 1){
                    $escritura = 'true';
                }
                else{
                    $escritura = 'false';
                }

                if ($pagina == 1 && $ctdad_nodoshijos == 0){
                    echo '{';
                }
                elseif ($pagina <= $ctdad_paginas || $ctdad_nodoshijos > 0){
                    echo ',{';
                }

                // Imprimir los datos de cada pagina
                echo    'id:"2' . $query_paginas->fields[0] . '", 
                        text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                        iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '", 
                        cls: "linked", 
                        checked: true, 
                        modificar: ' . $modificar . ',
                        lectura_exportar: ' . $lectura_exportar . ',
                        lectura: ' . $lectura . ',
                        escritura: ' . $escritura . ',
                        leaf: true}';
            }
            else {

                if ($pagina == 1 && $ctdad_nodoshijos == 0){
                    echo '{';
                }
                elseif ($pagina <= $ctdad_paginas || $ctdad_nodoshijos > 0){
                    echo ',{';
                }

                // Imprimir los datos de cada pagina
                echo    'id:"2' . $query_paginas->fields[0] . '", 
                        text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                        iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '",
                        cls: "linked", 
                        checked: false, 
                        modificar: false,
                        lectura_exportar: false,
                        lectura: false,
                        escritura: false,
                        leaf: true}';
            }
        }
        else {

            if ($pagina == 1 && $ctdad_nodoshijos == 0){
                echo '{';
            }
            elseif ($pagina <= $ctdad_paginas || $ctdad_nodoshijos > 0){
                echo ',{';
            }

            // Imprimir los datos de cada pagina
            echo    'id:"2' . $query_paginas->fields[0] . '", 
                    text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                    iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '",
                    cls: "linked", 
                    checked: false, 
                    modificar: false,
                    lectura_exportar: false,
                    lectura: false,
                    escritura: false,
                    leaf: true}';
        }
        /////////////////////////////////////////
        /////////////////////////////////////////

        $pagina++;
        
        $query_paginas->MoveNext();
    }

    //}
    // Cerrar arreglo de cada modulo
    if ($modulo < $ctdad_modulos) {
        echo "]},";
    } else {
        echo "]}";
    }

    $modulo = $modulo + 1;

    $query_modulos->MoveNext();
}

/////////////////////////////////////////////
///////     PAGINAS HIJAS DE ROOT     ///////
/////////////////////////////////////////////
// Listar paginas de la raiz
$query_paginas = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id AS id,syst_paginas.nombre AS nombre FROM syst_paginas,syst_paginas_view WHERE syst_paginas.id_nodo = 0 AND syst_paginas.id = syst_paginas_view.id_pagina AND syst_paginas_view.id_view = 1");

if ($query_paginas->RecordCount() > 0) {

    while (!$query_paginas->EOF) {

        // Comprobar permisos de la pagina

        if (isset($_GET['id_perfil'])) {

            $query_pag_pemisos = $adoMSSQL_SEMTI->Execute("SELECT modificar,lectura_exportar,lectura,escritura FROM syst_permisos WHERE id_pagina = " . $query_paginas->fields[0] . " AND id_perfil = " . $_GET['id_perfil'] . "");

            if ($query_pag_pemisos->RecordCount() > 0) {

                //$permiso = $query_pag_pemisos->FetchRow();
                if ($query_pag_pemisos->fields[0] == 1){
                    $modificar = 'true';
                }
                else{
                    $modificar = 'false';
                }
                if ($query_pag_pemisos->fields[1] == 1){
                    $lectura_exportar = 'true';
                }
                else{
                    $lectura_exportar = 'false';
                }
                if ($query_pag_pemisos->fields[2] == 1){
                    $lectura = 'true';
                }
                else{
                    $lectura = 'false';
                }
                if ($query_pag_pemisos->fields[3] == 1){
                    $escritura = 'true';
                }
                else{
                    $escritura = 'false';
                }

                // Imprimir los datos de cada pagina
                echo    ',{id:"2' . $query_paginas->fields[0] . '", 
                        text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                        iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), '') . '", 
                        cls: "linked", 
                        checked: true,
                        modificar: ' . $modificar . ',
                        lectura_exportar: ' . $lectura_exportar . ',
                        lectura: ' . $lectura . ',
                        escritura: ' . $escritura . ', 
                        leaf: true}';
            }
            else {
                // Imprimir los datos de cada pagina
                echo    ',{id:"2' . $query_paginas->fields[0] . '", 
                        text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                        iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), '') . '", 
                        cls: "linked",  
                        checked: false, 
                        modificar: false,
                        lectura_exportar: false,
                        lectura: false,
                        escritura: false,
                        leaf: true}';
            }
            
        } else {
            // Imprimir los datos de cada pagina
            echo    ',{id:"2' . $query_paginas->fields[0] . '", 
                    text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", 
                    iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), '') . '", 
                    cls: "linked",  
                    checked: false, 
                    modificar: false,
                    lectura_exportar: false,
                    lectura: false,
                    escritura: false,
                    leaf: true}';
        }
        
        $query_paginas->MoveNext();
    }
}

echo ']}';