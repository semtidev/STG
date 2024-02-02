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
    } elseif ($icon == 'Proyectos') {
        return 'icon_proyectos';
    } elseif ($icon == 'Archivo de Proyectos') {
        return 'icon_archivo_proyectos';
    } elseif ($icon == 'Garantía') {
        return 'icon_garantia';
    } elseif ($icon == 'Solicitudes de Defectación') {
        return 'icon_SD';
    } elseif ($icon == 'Departamentos' || $icon == 'Tipos de Problemas' || $icon == 'Parámetros Generales') {
        return 'icon_dptos';
    } elseif ($icon == 'Informes' || $icon == 'Habitaciones Fuera de Orden' || $icon == 'Resumen de Garantía') {
        return 'icon_resumen';
    } elseif ($icon == 'Tipo de Portada') {
        return 'icon_portada';
    } elseif ($icon == 'Seguimiento a Indicadores') {
        return 'icon_dashboard';
    } elseif ($icon == 'SD Por Resolver') {
        return 'icon-chartpie';
    } elseif ($icon == 'SD  Const/Sumin/AEH' || $icon == 'Relación Tipo Defecto / Reportes SD' || $icon == 'SD que No Proceden' || $icon == 'Comparativa de SD entre Proyectos') {
        return 'icon-chartbar';
    } elseif ($icon == 'Demora Prom en SD' || $icon == 'Demora Prom en SD AEH' || $icon == 'Demora Prom SD Const, no AEH y no Sumin' || $icon == 'Demora Prom SD Const, AEH y no Sumin' || $icon == 'Demora Prom SD Const, AEH y Sumin' || $icon == 'Demora Prom SD Const, no AEH y Sumin') {
        return 'icon-chartline';
    } else {
        return 'doc_unknow_icon';
    }
}

//////////////////////////////////////////////////////////

include_once 'connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once 'cadenas.php';
$cadenas = new Cadenas();

// Seleccionar los perfiles del usuario
$id_usuario = $_SESSION['idusuario'];

$query_perfiluser = $adoMSSQL_SEMTI->Execute("SELECT id_perfil FROM syst_usuarios_perfil WHERE id_usuario = $id_usuario");
$perfiles = array();

while (!$query_perfiluser->EOF) {

    $perfiles[] = $query_perfiluser->fields[0];
    $query_perfiluser->MoveNext();
}

$ctdad_pefiles = count($perfiles);


// Construir el arbol de navegacion
echo '{"success": true, "children": [';


//////////////////////////////
////////   MODULOS   /////////
//////////////////////////////

$query_modulos = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM syst_modulos WHERE nivel = 0");

$ctdad_modulos = $query_modulos->RecordCount();
$modulo = 1;
$modulo_nodos = 0;
$permiso_modulo = 0;
$permiso_nodo = 0;
$permiso_pagina = 0;

// Recorrer los Modulos primero para mostrar los que tengan permisos
while (!$query_modulos->EOF) {

    for ($i = 0; $i < $ctdad_pefiles; $i++) {

        // Comprobar si el modulo tiene permisos
        $query_mod_permisos = $adoMSSQL_SEMTI->Execute("SELECT DISTINCT(syst_modulos.nombre) FROM syst_modulos,syst_paginas,syst_permisos WHERE (syst_permisos.id_perfil = " . $perfiles[$i] . ") AND (syst_permisos.id_pagina = syst_paginas.id) AND (syst_paginas.id_nodo = syst_modulos.id) AND ((syst_modulos.id = " . $query_modulos->fields[0] . ") OR (syst_modulos.padre = '" . $query_modulos->fields[1] . "'))");
        //$query_mod_permisos = $adoMYSQL_SEMTI->Execute("SELECT syst_modulos.nombre FROM syst_modulos,syst_paginas,syst_permisos");
        if ($query_mod_permisos->RecordCount() > 0){
            $permiso_modulo++;
        }
    }

    // Si tiene permiso entonces se muestra el modulo
    if ($permiso_modulo > 0) {

        $expanded = 'true';
        if($cadenas->utf8($query_modulos->fields[1]) == 'Configuración'){ $expanded = 'true'; }
        // Si tiene permisos se imprimen los datos del modulo
        echo '{id:"1' . $query_modulos->fields[0] . '", text: "' . $cadenas->utf8($query_modulos->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_modulos->fields[1]), '') . '", cls: "system_name", expanded: '.$expanded.', children: [';


        ////////////////////////////
        ////////   NODOS   /////////
        ////////////////////////////
        
        // Comprobar si el modulo tiene nodos hijos
        $query_nodos = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM syst_modulos WHERE nombre <> 'Seguimiento a Indicadores' AND padre = '" . $query_modulos->fields[1] . "'");
        // Recorrer los nodos hijos si tiene
        if ($query_nodos->RecordCount() > 0) {

            $ctdad_nodos = $query_nodos->RecordCount();
            $nodo = 0;

            while (!$query_nodos->EOF) {

                // Comprobar si el nodo tiene permisos
                for ($i = 0; $i < $ctdad_pefiles; $i++) {

                    $query_nodo_permisos = $adoMSSQL_SEMTI->Execute("SELECT DISTINCT(syst_modulos.nombre) FROM syst_modulos,syst_paginas,syst_permisos WHERE (syst_permisos.id_perfil = " . $perfiles[$i] . ") AND (syst_permisos.id_pagina = syst_paginas.id) AND (syst_paginas.id_nodo = " . $query_nodos->fields[0] . ")");

                    if ($query_nodo_permisos->RecordCount() > 0){
                        $permiso_nodo++;
                    }
                }

                // Si tiene permiso entonces se muestra el nodo
                if ($permiso_nodo > 0) {
                    
                    $nodo++;
                    
                    // Listar paginas del nodo
                    $query_paginas = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM syst_paginas WHERE id_nodo = " . $query_nodos->fields[0] . "");

                    $ctdad_paginas = $query_paginas->RecordCount();

                    if ($ctdad_paginas > 0) {

                        if($nodo == 1){
                            echo '{';
                        }
                        else{
                            echo ',{';
                        }

                        // Imprimir los datos de cada nodo
                        echo 'id:"1' . $query_nodos->fields[0] . '", text: "' . $cadenas->utf8($query_nodos->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_nodos->fields[1]), '') . '", cls: "system_name", expanded: true, children: [';

                        ////////////////////////////////////
                        ////////   PAGINAS NODOS   /////////
                        ////////////////////////////////////

                        $pagina = 1;
                        
                        while (!$query_paginas->EOF) {

                            // Comprobar si tiene paginas con permisos
                            for ($i = 0; $i < $ctdad_pefiles; $i++) {

                                $query_pag_permisos = $adoMSSQL_SEMTI->Execute("SELECT DISTINCT(syst_paginas.nombre) FROM syst_modulos,syst_paginas,syst_permisos WHERE (syst_permisos.id_perfil = " . $perfiles[$i] . ") AND (syst_permisos.id_pagina = " . $query_paginas->fields[0] . ") AND (syst_paginas.id_nodo = " . $query_nodos->fields[0] . ")");

                                if ($query_pag_permisos->RecordCount() > 0){
                                    $permiso_pagina++;
                                }
                            }

                            if ($permiso_pagina > 0) {

                                if ($pagina < $ctdad_paginas) {
                                    // Imprimir los datos de cada pagina
                                    echo '{id:"3' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_nodos->fields[1])) . '", cls: "linked", leaf: true},';
                                } else {
                                    // Imprimir los datos de cada pagina
                                    echo '{id:"3' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_nodos->fields[1])) . '", cls: "linked", leaf: true}';
                                }

                                $pagina++;
                            }

                            $permiso_pagina = 0;
                            
                            $query_paginas->MoveNext();
                        }
                    }

                    echo "]}";
                    
                    $modulo_nodos++;
                }

                $nodo++;
                $permiso_nodo = 0;
                
                $query_nodos->MoveNext();
            }
        }

        
        //////////////////////////////////////
        ////////   PAGINAS MODULOS   /////////
        //////////////////////////////////////
        // Listar paginas del nodo
        $query_paginas = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id AS id, syst_paginas.nombre AS nombre FROM syst_paginas,syst_paginas_view WHERE syst_paginas.id_nodo = " . $query_modulos->fields[0] . " AND syst_paginas.id = syst_paginas_view.id_pagina AND syst_paginas_view.id_view = 2");

        $ctdad_paginas = $query_paginas->RecordCount();
        $pagina = 1;

        while (!$query_paginas->EOF) {

            // Comprobar si tiene paginas con permisos
            for ($i = 0; $i < $ctdad_pefiles; $i++) {

                $query_pag_permisos = $adoMSSQL_SEMTI->Execute("SELECT DISTINCT(syst_paginas.nombre) FROM syst_modulos,syst_paginas,syst_permisos WHERE (syst_permisos.id_perfil = " . $perfiles[$i] . ") AND (syst_permisos.id_pagina = " . $query_paginas->fields[0] . ") AND (syst_paginas.id_nodo = " . $query_modulos->fields[0] . ")");

                if ($query_pag_permisos->RecordCount() > 0){
                    $permiso_pagina++;
                }
            }

            if ($permiso_pagina > 0) {

                if ($pagina < $ctdad_paginas) {

                    if ($modulo_nodos > 0 && $pagina == 1) {

                        // Imprimir los datos de cada pagina
                        echo ',{id:"2' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '", cls: "linked", leaf: true},';
                    } else {

                        // Imprimir los datos de cada pagina
                        echo '{id:"2' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '", cls: "linked", leaf: true},';
                    }
                } else {

                    if ($modulo_nodos > 0 && $pagina == 1) {

                        // Imprimir los datos de cada pagina
                        echo ',{id:"2' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '", cls: "linked", leaf: true}';
                    } else {

                        // Imprimir los datos de cada pagina
                        echo '{id:"2' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), $cadenas->utf8($query_modulos->fields[1])) . '", cls: "linked", leaf: true}';
                    }
                }
            }

            $pagina++;
            $permiso_pagina = 0;
            
            $query_paginas->MoveNext();
        }

        if ($modulo < $ctdad_modulos) {
            echo "]},";
        } else {
            echo "]}";
        }
    }

    $modulo = $modulo + 1;
    $permiso_modulo = 0;
    $modulo_nodos = 0;
    
    $query_modulos->MoveNext();
}

/////////////////////////////////////////////
///////     PAGINAS HIJAS DE ROOT     ///////
/////////////////////////////////////////////
// Listar paginas de la raiz
$query_paginas = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id AS id, syst_paginas.nombre AS nombre FROM syst_paginas,syst_paginas_view WHERE syst_paginas.id_nodo = 0 AND syst_paginas.id = syst_paginas_view.id_pagina AND syst_paginas_view.id_view = 2");

if ($query_paginas->RecordCount() > 0) {

    while (!$query_paginas->EOF) {

        // Comprobar si tiene paginas con permisos
        for ($i = 0; $i < $ctdad_pefiles; $i++) {

            $query_pag_permisos = $adoMSSQL_SEMTI->Execute("SELECT DISTINCT(syst_paginas.nombre) FROM syst_modulos,syst_paginas,syst_permisos WHERE (syst_permisos.id_perfil = " . $perfiles[$i] . ") AND (syst_permisos.id_pagina = " . $query_paginas->fields[0] . ")");

            if ($query_pag_permisos->RecordCount() > 0){
                $permiso_pagina++;
            }
        }

        if ($permiso_pagina > 0) {
            // Imprimir los datos de cada pagina
            echo ',{id:"2' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), '') . '", cls: "linked", leaf: true}';
        }
    }
}

echo ']}';