<?php

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

include_once 'connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();


// Construir el arbol de navegacion
echo '{"success": true, "children": [';


////////////////////////////////
////////   PROYECTOS   /////////
////////////////////////////////

$polo = -1;
if (isset($_GET['polo']) && $_GET['polo'] > 0 && $_GET['polo'] != 9) {
    $polo = intval($_GET['polo']);
}

if ($polo == -1) {
    $query_proyectos = $adoMSSQL_SEMTI->Execute("SELECT id, nombre FROM gtia_proyectos");
}
else {
    $query_proyectos = $adoMSSQL_SEMTI->Execute("SELECT id, nombre FROM gtia_proyectos WHERE id_polo = " . $polo);
}


$ctdad_proyectos = $query_proyectos->RecordCount();
$proyecto        = 0;

// Recorrer los Modulos primero para mostrar los que tengan permisos
while (!$query_proyectos->EOF) {

    $proyecto++;

    $checked = 'false';
    $modify  = 'false';
    $readexp = 'false';
    $read    = 'false';
    $write   = 'false';

    // Obtener checks del Modulo
    if (isset($_GET['id_usuario'])) {

        // Checked
        $qry_checked = $adoMSSQL_SEMTI->Execute("SELECT id FROM syst_usuarios_proyectos WHERE id_proyecto = " . $query_proyectos->fields[0] . " AND id_usuario = " . $_GET['id_usuario']);

        if ($qry_checked->RecordCount() > 0) {
           
           $checked = 'true';

           // Modificar
           $qry_modificar = $adoMSSQL_SEMTI->Execute("SELECT id FROM syst_usuarios_proyectos WHERE id_proyecto = " . $query_proyectos->fields[0] . " AND id_usuario = " . $_GET['id_usuario'] . " AND modificar = 1");
            if ($qry_modificar->RecordCount() > 0) {
                $modify = 'true';
            }
            // Read & Export
            $qry_readexp = $adoMSSQL_SEMTI->Execute("SELECT id FROM syst_usuarios_proyectos WHERE id_proyecto = " . $query_proyectos->fields[0] . " AND id_usuario = " . $_GET['id_usuario'] . " AND lectura_exportar = 1");
            if ($qry_readexp->RecordCount() > 0) {
                $readexp = 'true';
            }
            // Read
            $qry_read = $adoMSSQL_SEMTI->Execute("SELECT id FROM syst_usuarios_proyectos WHERE id_proyecto = " . $query_proyectos->fields[0] . " AND id_usuario = " . $_GET['id_usuario'] . " AND lectura = 1");
            if ($qry_read->RecordCount() > 0) {
                $read = 'true';
            }
            // Write
            $qry_write = $adoMSSQL_SEMTI->Execute("SELECT id FROM syst_usuarios_proyectos WHERE id_proyecto = " . $query_proyectos->fields[0] . " AND id_usuario = " . $_GET['id_usuario'] . " AND escritura = 1");
            if ($qry_write->RecordCount() > 0) {
                $write = 'true';
            }

        }        
    }
    /////////////////////////////////

    echo    '{
                id:"1' . $query_proyectos->fields[0] . '", 
                text: "' . $cadenas->utf8($query_proyectos->fields[1]) . '", 
                iconCls: "icon_proyecto", 
                cls: "system_name", 
                checked: ' . $checked . ',
                modificar: ' . $modify . ',
                lectura_exportar: ' . $readexp . ',
                lectura: ' . $read . ',
                escritura: ' . $write. ',
                children: []
            }';

    if($proyecto < $ctdad_proyectos){
        echo ',';
    }

    $query_proyectos->MoveNext();
    
}

echo ']}';