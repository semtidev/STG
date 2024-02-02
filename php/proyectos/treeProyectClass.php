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

//////////////////////////////////////////////
//////      CLASE TREE PROYECTOS       ///////
//////////////////////////////////////////////

class treeProyects {

    /////////////////////////////////////////
    //////////////  Atributos  //////////////
    /////////////////////////////////////////

    
    /////////////////////////////////////////
    ///////////  Implementacion  ////////////
    /////////////////////////////////////////
    // Listar Proyectos
    function ReadProyects() {

        $tree = '{"success": true, "children": [';
        $polo = -1;

        // Buscar roles de usuario
        $roles = array();
        $projects = 'Polo';
        $qry_roles = $GLOBALS["adoMSSQL_SEMTI"]->Execute("
                        SELECT syst_perfiles.nombre 
                        FROM syst_usuarios_perfil 
                            LEFT JOIN syst_perfiles 
                            ON syst_usuarios_perfil.id_perfil = syst_perfiles.id
                        WHERE syst_usuarios_perfil.id_usuario = ". intval($_SESSION['idusuario'])
                    );
        while (!$qry_roles->EOF) {
            $roles[] = $qry_roles->fields[0];
            $qry_roles->MoveNext();
        }

        if (array_search('Superadmin', $roles) !== false || array_search($GLOBALS["cadenas"]->codificarBD_utf8('Responsable Garantía BBI'), $roles) !== false) {
            $projects = 'All';
        }

        if ($projects == 'All') {
            $qry_proyectos = $GLOBALS["adoMSSQL_SEMTI"]->Execute("
                SELECT 
                    gtia_proyectos.id, 
                    gtia_proyectos.nombre, 
                    gtia_proyectos.presupuesto, 
                    gtia_proyectos.activo, 
                    gtia_proyectos.sd, 
                    gtia_proyectos.id_polo,
                    syst_polos.nombre AS polo_nombre, 
                    syst_polos.abbr AS polo_abbr,
                    gtia_proyectos.nombre_comercial,
                    gtia_proyectos.fecha_inicio,
                    gtia_proyectos.fecha_terminacion
                FROM 
                    gtia_proyectos
                    LEFT JOIN syst_polos 
                    ON gtia_proyectos.id_polo = syst_polos.id
                ORDER BY id_polo ASC, nombre ASC");
        }
        else {
            $qry_proyectos = $GLOBALS["adoMSSQL_SEMTI"]->Execute("
                SELECT 
                    gtia_proyectos.id, 
                    gtia_proyectos.nombre, 
                    gtia_proyectos.presupuesto, 
                    gtia_proyectos.activo, 
                    gtia_proyectos.sd, 
                    gtia_proyectos.id_polo,
                    syst_polos.nombre AS polo_nombre, 
                    syst_polos.abbr AS polo_abbr,
                    gtia_proyectos.nombre_comercial,
                    gtia_proyectos.fecha_inicio,
                    gtia_proyectos.fecha_terminacion
                FROM 
                    gtia_proyectos
                    LEFT JOIN syst_polos 
                    ON gtia_proyectos.id_polo = syst_polos.id 
                WHERE id_polo = " . intval($_SESSION['polo']) . " 
                ORDER BY id_polo ASC, nombre ASC");
        }
        
        /////////////////////////////////////////////
        ////////   RECORRER LOS PROYECTOS   /////////
        /////////////////////////////////////////////
        

        if ($qry_proyectos->RecordCount() > 0) {

            $ctdad_proyectos = $qry_proyectos->RecordCount();
            $proyecto = 0;

            while (!$qry_proyectos->EOF) {

                $proyecto++;
                if ($qry_proyectos->fields[4]) {
                    $proyecto_icon = "icon_proyecto_afectado";
                } else {
                    $proyecto_icon = "icon_proyecto";
                }                

                // POLO
                ////////////////////////////////
                if ($polo != $qry_proyectos->fields[5]) {
                    $polo = $qry_proyectos->fields[5];
                    if ($proyecto > 1) {
                        $proyecto = 1;
                        $tree .= ']},';
                    }
                    $tree .= '{ id:"P.1.' . $qry_proyectos->fields[0] . '",
                        text: "Polo ' . $GLOBALS["cadenas"]->utf8($qry_proyectos->fields[6]) . '",
                        ruta: "Polo ' . $GLOBALS["cadenas"]->utf8($qry_proyectos->fields[6]) . '",
                        presupuesto: "",
                        tipo: "polo",
                        activo: "' . $qry_proyectos->fields[3] . '",
                        iconCls: "icon_polo",
                        cls: "tree_polo",
                        expanded: false,
                        children: [';
                }
                
                // PROYECTO
                ////////////////////////////////
                if ($proyecto > 1) {
                    $tree .= ', ';
                }
                $tree .= '{ id:"1.' . $qry_proyectos->fields[0] . '",
                            text: "' . $GLOBALS["cadenas"]->utf8($qry_proyectos->fields[1]) . '",
                            ruta: "Proyecto ' . $GLOBALS["cadenas"]->utf8($qry_proyectos->fields[1]) . '",
                            presupuesto: "' . $qry_proyectos->fields[2] . '",
                            tipo: "proyecto",
                            activo: "' . $qry_proyectos->fields[3] . '",
                            fecha_inicio: "'.$qry_proyectos->fields[9].'",
                            fecha_terminacion: "'.$qry_proyectos->fields[10].'",
                            iconCls: "'.$proyecto_icon.'",
                            cls: "linked",
                            expanded: false,
                            polo: "' . strval($qry_proyectos->fields[5]) . '",
                            nombre_comercial: "' . $qry_proyectos->fields[8] . '",
                            children: [';

                /////////////////////////////////////////
                ////////    RECORRER LAS ZONAS   ////////
                /////////////////////////////////////////

                $sql_zonas = "SELECT id,nombre,fecha_ini,fecha_fin, sd FROM gtia_zonas WHERE id_proyecto = " . $qry_proyectos->fields[0] . " ORDER BY nombre ASC";
                $qry_zonas = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_zonas);

                if ($qry_zonas->RecordCount() > 0) {

                    $ctdad_zonas = $qry_zonas->RecordCount();
                    $zona = 0;

                    while (!$qry_zonas->EOF) {

                        $zona++;
                        if ($qry_zonas->fields[4]) {
                            $zona_icon = "icon_zonas_afectadas";
                        } else {
                            $zona_icon = "icon_zonas";
                        }                        

                        // ZONA
                        ////////////////////////////////
                        $tree .= '{id:"2.' . $qry_proyectos->fields[0] . '.' . $qry_zonas->fields[0] . '", text: "Zona ' . $GLOBALS["cadenas"]->utf8($qry_zonas->fields[1]) . '", ruta: "Proyecto ' . $GLOBALS["cadenas"]->utf8($qry_proyectos->fields[1]) . ', Zona ' . $GLOBALS["cadenas"]->utf8($qry_zonas->fields[1]) . '", fecha_ini: "' . $qry_zonas->fields[2] . '", fecha_fin: "' . $qry_zonas->fields[3] . '", iconCls: "'.$zona_icon.'", cls: "linked", expanded: false, children: [';

                        ////////////////////////////////////////////
                        ////////    RECORRER LOS OBJETOS    ////////
                        ////////////////////////////////////////////

                        $sql_objetos = "SELECT id, nombre, sd FROM gtia_objetos WHERE id_zona = " . $qry_zonas->fields[0] . " ORDER BY id ASC";
                        $qry_objetos = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql_objetos);

                        if ($qry_objetos->RecordCount() > 0) {

                            $ctdad_objetos = $qry_objetos->RecordCount();
                            $objeto = 0;

                            while (!$qry_objetos->EOF) {

                                $objeto++;

                                if ($qry_objetos->fields[2]) {
                                    $objetos_icon = "icon_objetos_afectados";
                                } else {
                                    $objetos_icon = "icon_objetos";
                                }                                

                                // OBJETO
                                ////////////////////////////////
                                $tree .= '{id:"3.' . $qry_proyectos->fields[0] . '.' . $qry_zonas->fields[0] . '.' . $qry_objetos->fields[0] . '", text: "' . $GLOBALS["cadenas"]->utf8($qry_objetos->fields[1]) . '", ruta: "Proyecto ' . $GLOBALS["cadenas"]->utf8($qry_proyectos->fields[1]) . ', Zona ' . $GLOBALS["cadenas"]->utf8($qry_zonas->fields[1]) . ', ' . $GLOBALS["cadenas"]->utf8($qry_objetos->fields[1]) . '", iconCls: "'.$objetos_icon.'", cls: "linked", leaf: true';

                                // Cerrar Hijos y Llave del Objeto
                                if ($objeto < $ctdad_objetos)
                                    $tree .= '},';
                                else
                                    $tree .= '}';
                                
                                $qry_objetos->MoveNext();
                            }
                        }

                        // Cerrar Hijos y Llave de la Zona
                        if ($zona < $ctdad_zonas)
                            $tree .= ']},';
                        else
                            $tree .= ']}';
                        
                        $qry_zonas->MoveNext();
                    }
                }

                // Cerrar Hijos y Llave del Proyecto
                /*if ($proyecto < $ctdad_proyectos)
                    $tree .= ']},*';
                else*/
                    $tree .= ']}';
                
                $qry_proyectos->MoveNext();
            }
        }

        if ($polo != -1) {
            $tree .= ']}';
        }

        $tree .= ']}';

        return $tree;
    }

    ////////////////////////////////////////////
    // Modificar Elemento del Tree
    function UpdateTreeElemento($idElement, $nameElement) {

        // Nombre
        $nombre = $nameElement;

        // arreglo de parametros del ID del elemento
        $array_id = explode('.', $idElement);

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        // Si es un Proyecto
        if (count($array_id) == 2 && $array_id[0] == 1) {

            $sql_update = "UPDATE gtia_proyectos SET nombre = '$nombre' WHERE id = " . $array_id[1];
        }
        // Si es una Zona
        elseif (count($array_id) == 3 && $array_id[0] == 2) {

            $sql_update = "UPDATE gtia_zonas SET nombre = '$nombre' WHERE id = " . $array_id[2];
        }
        // Si es un Objeto
        elseif (count($array_id) == 4 && $array_id[0] == 3) {

            $sql_update = "UPDATE gtia_objetos SET nombre = '$nombre' WHERE id = " . $array_id[3];
        }

        $GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update);

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

    // Crear Proyecto
    function CreateProyect($nombre, $presupuesto, $imagen, $nombreImagen, $activo, $nombre_comercial, $polo, $fecha_inicio, $fecha_terminacion) {

        $polo = intval($polo);
        
        // Subir la Imagen del proyceto al servidor
        if ($nombreImagen != '') {

            copy($imagen['tmp_name'], '../../resources/images/proyectos/' . $nombreImagen);
        }
        
        if($presupuesto == ''){ $presupuesto = 0; } else { $presupuesto = floatval($presupuesto); }

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $sql_create = "INSERT INTO gtia_proyectos(nombre, presupuesto, imagen, activo, nombre_comercial, id_polo, fecha_inicio, fecha_terminacion) VALUES('$nombre', $presupuesto, '$nombreImagen', $activo, '$nombre_comercial', $polo, '$fecha_inicio', '$fecha_terminacion')";
        $GLOBALS["adoMSSQL_SEMTI"]->execute($sql_create);

        if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {

            $response = json_encode(array(
                "success" => true,
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
    // Editar Proyecto
    function EditProyect($id, $nombre, $presupuesto, $imagen, $nombreImagen, $activo, $nombre_comercial, $polo, $fecha_inicio, $fecha_terminacion) {

        $polo = intval($polo);
        
        $id_array = explode('.', $id);
        $id = $id_array[1];
                
        // Subir la Imagen del proyceto al servidor
        if ($nombreImagen != '') {

            // Capturar el campo imagen de la BD para eliminarla si existe
            $sql_img = "SELECT imagen FROM gtia_proyectos WHERE id = $id";
            $qry_img = $GLOBALS["adoMSSQL_SEMTI"]->execute($sql_img);
            $img_db  = $qry_img->fields[0];
            if($img_db != ''){
                $link = "../../resources/images/proyectos/" . $img_db;
                $link = (string) $link;
                unlink($link);
            }
            
            copy($imagen['tmp_name'], '../../resources/images/proyectos/' . $nombreImagen);
            $sql = "UPDATE gtia_proyectos SET nombre = '$nombre', presupuesto = $presupuesto, imagen = '$nombreImagen', activo = $activo, fecha_inicio = '$fecha_inicio', fecha_terminacion = '$fecha_terminacion' WHERE id = $id";
        
        } else {
            
            $sql = "UPDATE gtia_proyectos SET nombre = '$nombre',presupuesto = $presupuesto, activo = $activo, nombre_comercial = '$nombre_comercial', id_polo = $polo, fecha_inicio = '$fecha_inicio', fecha_terminacion = '$fecha_terminacion' WHERE id = $id";
        }

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->execute($sql);

        if (!$GLOBALS["adoMSSQL_SEMTI"]->HasFailedTrans()) {

            $response = json_encode(array(
                "success" => true,
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
    // Modificar Elemento
    function UpdateElement($params) {

        try {

            // Nombre
            $nombre = $GLOBALS["cadenas"]->latin1($GLOBALS["cadenas"]->codificarBD_utf8($params->text));

            // arreglo de parametros del ID del nodo
            $array_id = explode('.', $params->id);

            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            // Si es un Proyecto
            if ($array_id[0] == 1) {

                $sql_update = "UPDATE gtia_proyectos SET nombre = '" . $nombre . "' WHERE id = " . $array_id[1];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }
            // Si es una Zona
            if ($array_id[0] == 2) {

                $nombre_array = explode('Zona ', $nombre);
                $sql_update = "UPDATE gtia_zonas SET nombre = '" . $nombre_array[1] . "' WHERE id = " . $array_id[2];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }
            // Si es un Objeto
            if ($array_id[0] == 3) {

                $sql_update = "UPDATE gtia_objetos SET nombre = '$nombre' WHERE id = " . $array_id[3];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }
            // Si es una Parte
            if ($array_id[0] == 4) {

                $sql_update = "UPDATE gtia_partes SET nombre = '$nombre' WHERE id = " . $array_id[4];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }

            $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
            $GLOBALS["adoMSSQL_SEMTI"]->Close();
                 
        } catch (Exception $e) {

            $jsonResult = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }

        return json_encode($jsonResult);
    }

    ////////////////////////////////////////////
    // Eliminar Elemento
    function DestroyElement($params) {

        try {

            // arreglo de parametros del ID del nodo
            //$array_id = explode('.',$params->id);
            $array_id = explode('.', $params);

            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            // Si es un Proyecto
            if ($array_id[0] == 1) {

                $sql_update = "DELETE FROM gtia_proyectos WHERE id = " . $array_id[1];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }
            // Si es una Zona
            if ($array_id[0] == 2) {

                $sql_update = "DELETE FROM gtia_zonas WHERE id = " . $array_id[2];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }
            // Si es un Objeto
            if ($array_id[0] == 3) {

                $sql_update = "DELETE FROM gtia_objetos WHERE id = " . $array_id[3];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));    
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }
            // Si es una Parte
            if ($array_id[0] == 4) {

                $sql_update = "DELETE FROM gtia_partes WHERE id = " . $array_id[4];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true,
                    'children' => $params
                );
            }

            $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
            $GLOBALS["adoMSSQL_SEMTI"]->Close();
            
        } catch (Exception $e) {

            $jsonResult = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }

        return json_encode($jsonResult);
    }

    ////////////////////////////////////////////
    // Nueva Zona
    function CreateZone($id_parent, $nombre, $fecha_ini, $fecha_fin) {

        $id_array = explode('.', $id_parent);
        $id_proyect = $id_array[1];

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $sql = "INSERT INTO gtia_zonas(id_proyecto,nombre,fecha_ini,fecha_fin) VALUES($id_proyect,'$nombre','$fecha_ini','$fecha_fin')";

        $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

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
    // Editar Zona
    function EditZone($id, $nombre, $fecha_ini, $fecha_fin) {

        $id_array = explode('.', $id);
        $id_zone = $id_array[2];

        if (strstr($nombre, 'Zona') != false) {
            $nombre_array = explode('Zona ', $nombre);
            $nombre = $nombre_array[1];
        }

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $sql = "UPDATE gtia_zonas SET nombre = '$nombre',fecha_ini = '$fecha_ini',fecha_fin = '$fecha_fin' WHERE id = $id_zone";

        $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

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
    // Nuevo Objeto
    function NuevObjeto($zona, $nombre) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $sql = "INSERT INTO gtia_objetos(id_zona,nombre) VALUES($zona,'$nombre')";

        $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

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
    // Nueva Parte
    function NuevaParte($objeto, $nombre) {

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $sql = "INSERT INTO gtia_partes(id_objeto,nombre) VALUES($objeto,'$nombre')";

        $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

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
    // Leer Zona
    function LoadZone($objectId) {

        $sql = "SELECT gtia_zonas.nombre AS nombre FROM gtia_objetos,gtia_zonas WHERE gtia_objetos.id = $objectId AND gtia_objetos.id_zona = gtia_zonas.id";

        if ($qry = $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql)) {

            $res = $qry->FetchRow();
            $response = json_encode(array(
                "success" => true,
                "message" => 'Zona ' . $res['nombre']
            ));
        } else {

            $response = json_encode(array(
                "failure" => true,
                "message" => $GLOBALS["message"][2]
            ));
        }

        return $response;
    }

    ////////////////////////////////////////////
    // Insertar nuevo elemento
    function NuevoElemento($idElement, $nombre) {

        $array_idElement = explode('.', $idElement);

        if (count($array_idElement) == 2 && $array_idElement[0] == 1) {

            $id_proyecto = $array_idElement[1];
            $sql = "INSERT INTO gtia_zonas(id_proyecto,nombre) VALUES($id_proyecto,'$nombre')";
        } elseif (count($array_idElement) == 3 && $array_idElement[0] == 2) {

            $id_zona = $array_idElement[2];
            $sql = "INSERT INTO gtia_objetos(id_zona,nombre) VALUES($id_zona,'$nombre')";
        } elseif (count($array_idElement) == 4 && $array_idElement[0] == 3) {

            $id_objeto = $array_idElement[3];
            $sql = "INSERT INTO gtia_partes(id_objeto,nombre) VALUES($id_objeto,'$nombre')";
        }

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        $GLOBALS["adoMSSQL_SEMTI"]->Execute($sql);

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
    // Modificar Elemento del Grid
    function UpdateGridElement($idElement, $nameElement) {

        // Nombre
        $nombre = $nameElement;
        // arreglo de parametros del ID del elemento
        $array_id = explode('.', $idElement);

        $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

        // Si es una Zona
        if (count($array_id) == 3 && $array_id[0] == 1) {

            $sql_update = "UPDATE gtia_zonas SET nombre = '$nombre' WHERE id = " . $array_id[2];
        }
        // Si es un Objeto
        elseif (count($array_id) == 4 && $array_id[0] == 2) {

            $sql_update = "UPDATE gtia_objetos SET nombre = '$nombre' WHERE id = " . $array_id[3];
        }
        // Si es una Parte
        elseif (count($array_id) == 5 && $array_id[0] == 3) {

            $sql_update = "UPDATE gtia_partes SET nombre = '$nombre' WHERE id = " . $array_id[4];
        }

        $GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update);

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
    // Eliminar Elemento del Grid
    function DestroyGridElement($idElement, $nameElement) {

        try {

            // Nombre
            $nombre = $GLOBALS["cadenas"]->latin1($GLOBALS["cadenas"]->codificarBD_utf8($nameElement));

            // arreglo de parametros del ID del elemento
            $array_id = explode('.', $idElement);

            $GLOBALS["adoMSSQL_SEMTI"]->StartTrans();

            // Si es una Zona
            if (count($array_id) == 3 && $array_id[0] == 1) {

                $sql_update = "DELETE FROM gtia_zonas WHERE id = " . $array_id[2];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true
                );
            }
            // Si es un Objeto
            if (count($array_id) == 4 && $array_id[0] == 2) {

                $sql_update = "DELETE FROM gtia_objetos WHERE id = " . $array_id[3];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true
                );
            }
            // Si es una Parte
            if (count($array_id) == 5 && $array_id[0] == 3) {

                $sql_update = "DELETE FROM gtia_partes WHERE id = " . $array_id[4];

                if (!$GLOBALS["adoMSSQL_SEMTI"]->execute($sql_update)) {
                    throw new Exception(implode(', ', $GLOBALS["adoMSSQL_SEMTI"]->ErrorMsg()));
                }

                $jsonResult = array(
                    'success' => true
                );
            }

            $GLOBALS["adoMSSQL_SEMTI"]->CompleteTrans();
            $GLOBALS["adoMSSQL_SEMTI"]->Close();
            
        } catch (Exception $e) {

            $jsonResult = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }

        return json_encode($jsonResult);
    }

    ////////////////////////////////////////////
    ////////////////////////////////////////////
    ///////////  Getters && Setters  ///////////
    ////////////////////////////////////////////
}

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
?>