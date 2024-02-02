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

// Validar Polo del usuario
$polo = -1;
if (intval($_SESSION['polo']) != 9) {
    $polo = intval($_SESSION['polo']);
}

// Construir el arbol de navegacion
echo '{"success": true, "children": [';


/////////////////////////////////////////////
////////   RECORRER LOS PROYECTOS   /////////
/////////////////////////////////////////////

/*if(isset($_GET['loadProyect'])){
    $proyecto = $_GET['loadProyect'];
    $qry_proyectos = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM gtia_proyectos WHERE nombre = '$proyecto'");
    echo $proyecto;
}
else {*/
    if ($polo == -1) {
    $qry_proyectos = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM gtia_proyectos WHERE activo = 1 ORDER BY nombre ASC"); 
    }
    else {
        $qry_proyectos = $adoMSSQL_SEMTI->Execute("SELECT id,nombre FROM gtia_proyectos WHERE activo = 1 AND id_polo = ". $polo ." ORDER BY nombre ASC"); 
    }
//}

if ($qry_proyectos->RecordCount() > 0) {

    $ctdad_proyectos = $qry_proyectos->RecordCount();
    $proyecto = 0;

    while (!$qry_proyectos->EOF) {

        $proyecto++;

        // PROYECTO
        ////////////////////////////////
        echo '{id:"1.' . $qry_proyectos->fields[0] . '", text: "' . $cadenas->utf8($qry_proyectos->fields[1]) . '", ruta: "' . $cadenas->utf8($qry_proyectos->fields[1]) . '", iconCls: "icon_proyecto", cls: "linked", expanded: false, children: [';

        /////////////////////////////////////////
        ////////    RECORRER LAS ZONAS   ////////
        /////////////////////////////////////////

        $sql_zonas = "SELECT id,nombre FROM gtia_zonas WHERE id_proyecto = " . $qry_proyectos->fields[0] . " ORDER BY nombre ASC";
        $qry_zonas = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_zonas);

        if ($qry_zonas->RecordCount() > 0) {

            $ctdad_zonas = $qry_zonas->RecordCount();
            $zona = 0;

            while (!$qry_zonas->EOF) {

                $zona++;

                // ZONA
                ////////////////////////////////
                echo '{id:"2.' . $qry_proyectos->fields[0] . '.' . $qry_zonas->fields[0] . '", text: "Zona ' . $cadenas->utf8($qry_zonas->fields[1]) . '", ruta: "' . $cadenas->utf8($qry_proyectos->fields[1]) . ', Zona ' . $cadenas->utf8($qry_zonas->fields[1]) . '", iconCls: "icon_zonas", cls: "system_name", expanded: false, children: [';

                ////////////////////////////////////////////
                ////////    RECORRER LOS OBJETOS    ////////
                ////////////////////////////////////////////

                $sql_objetos = "SELECT id,nombre FROM gtia_objetos WHERE id_zona = " . $qry_zonas->fields[0] . " ORDER BY nombre ASC";
                $qry_objetos = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_objetos);

                if ($qry_objetos->RecordCount() > 0) {

                    $ctdad_objetos = $qry_objetos->RecordCount();
                    $objeto = 0;

                    while (!$qry_objetos->EOF) {

                        $objeto++;

                        // OBJETO
                        ////////////////////////////////
                        echo '{id:"3.' . $qry_proyectos->fields[0] . '.' . $qry_zonas->fields[0] . '.' . $qry_objetos->fields[0] . '", text: "' . $cadenas->utf8($qry_objetos->fields[1]) . '", ruta: "' . $cadenas->utf8($qry_proyectos->fields[1]) . ', Zona ' . $cadenas->utf8($qry_zonas->fields[1]) . ', ' . $cadenas->utf8($qry_objetos->fields[1]) . '", iconCls: "icon_objetos", cls: "system_name", expanded: false, children: [';

                        ///////////////////////////////////////////
                        ////////    RECORRER LAS PARTES    ////////
                        ///////////////////////////////////////////

                        $sql_partes = "SELECT id,nombre FROM gtia_partes WHERE id_objeto = " . $qry_objetos->fields[0] . " ORDER BY nombre ASC";
                        $qry_partes = $adoMSSQL_SEMTI->Execute($sql_partes);

                        if ($qry_partes->RecordCount() > 0) {

                            $ctdad_partes = $qry_partes->RecordCount();
                            $parte = 0;

                            while (!$qry_partes->EOF) {

                                $parte++;

                                // PARTE
                                ////////////////////////////////
                                echo '{id:"4.' . $qry_proyectos->fields[0] . '.' . $qry_zonas->fields[0] . '.' . $qry_objetos->fields[0] . '.' . $qry_partes->fields[0] . '", text: "' . $cadenas->utf8($qry_partes->fields[1]) . '", ruta: "' . $cadenas->utf8($qry_proyectos->fields[1]) . ', Zona ' . $cadenas->utf8($qry_zonas->fields[1]) . ', ' . $cadenas->utf8($qry_objetos->fields[1]) . ', ' . $cadenas->utf8($qry_partes->fields[1]) . '", iconCls: "icon_partes", cls: "system_name", leaf: true';

                                // Cerrar Llave de la Parte
                                if ($parte < $ctdad_partes)
                                    echo '},';
                                else
                                    echo '}';
                                
                                $qry_partes->MoveNext();
                            }
                        }

                        // Cerrar Hijos y Llave del Objeto
                        if ($objeto < $ctdad_objetos)
                            echo ']},';
                        else
                            echo ']}';
                        
                        $qry_objetos->MoveNext();
                    }
                }

                // Cerrar Hijos y Llave de la Zona
                if ($zona < $ctdad_zonas)
                    echo ']},';
                else
                    echo ']}';
                
                $qry_zonas->MoveNext();
            }
        }

        // Cerrar Hijos y Llave del Proyecto
        if ($proyecto < $ctdad_proyectos)
            echo ']},';
        else
            echo ']}';
        
        $qry_proyectos->MoveNext();
    }
}
echo ']}';