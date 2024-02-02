<?php

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Asignar el icono del nodo/pagina
function getIcon($icon, $nodo) {
    if ($icon == 'SD Por Resolver' || $icon == 'SD  Const/Sumin/AEH' || $icon == 'Demora Prom en SD' || $icon == 'Demora Prom en SD AEH' || $icon == 'Demora Prom SD Const, no AEH y no Sumin' || $icon == 'Demora Prom SD Const, AEH y no Sumin' || $icon == 'Demora Prom SD Const, AEH y Sumin' || $icon == 'Demora Prom SD Const, no AEH y Sumin' || $icon == 'Relación Tipo Defecto / Reportes SD' || $icon == 'SD que No Proceden' || $icon == 'Comparativa de SD entre Proyectos') {
        return 'icon_reporte';
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

$query_modulos = $adoMSSQL_SEMTI->Execute("SELECT id FROM syst_modulos WHERE nombre = 'Seguimiento a Indicadores'");
$modulo = $query_modulos->fields[0];

/////////////////////////////////////////////
///////     PAGINAS HIJAS DE ROOT     ///////
/////////////////////////////////////////////

$query_paginas = $adoMSSQL_SEMTI->Execute("SELECT syst_paginas.id AS id, syst_paginas.nombre AS nombre FROM syst_paginas,syst_paginas_view WHERE syst_paginas.id_nodo = ".$modulo." AND syst_paginas.id = syst_paginas_view.id_pagina AND syst_paginas_view.id_view = 2");

if ($query_paginas->RecordCount() > 0) {

    $nodo = 0;
    while (!$query_paginas->EOF) {

        $nodo++;
        if($nodo == 1){
            echo '{';
        }
        else{
            echo ',{';
        }
        // Imprimir los datos de cada pagina
        echo 'id:"2' . $query_paginas->fields[0] . '", text: "' . $cadenas->utf8($query_paginas->fields[1]) . '", iconCls: "' . getIcon($cadenas->utf8($query_paginas->fields[1]), '') . '", cls: "linked", leaf: true}';

        $query_paginas->MoveNext();
    }
}

echo ']}';