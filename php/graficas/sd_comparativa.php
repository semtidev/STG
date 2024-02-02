<?php
// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir la clase de conexion
include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

// Incluir la Paleta de Colores del Sistema
include_once '../sistema/colors.php';

// Título de la gráfica
if(!isset($_GET['titulo'])){
    $title = 'Comparativa de SD entre Proyectos';
}
else{
    $title = $_GET['titulo'];
}

// Validar Polo del usuario
$polo = -1;
if (intval($_SESSION['polo']) != 9) {
    $polo = intval($_SESSION['polo']);
}

// Establecer los filtros
$proyectos = '';
$tiposd    = '';
if(isset($_GET['listar'])){
    
    $listar_array = explode('.',$_GET['listar']);
    $proyectos    = $listar_array[0];
    $estado       = $listar_array[1];
    $tiposd       = $listar_array[2];
    $proyectoArr  = explode(',',$proyectos);
    $proyectCount = count($proyectoArr);
    
    // Definir los meses que seran evaluados en la comparacion
    
    $meses_arr    = array();
    $fechaini_arr = array();
    for($n = 0; $n < $proyectCount; $n++){
        
        $proyecto = $proyectoArr[$n];
        
        // Seleccionar el inicio y final del periodo de Garantia
        $sql_fechaini = "SELECT TOP 1
                            gtia_zonas.fecha_ini
                         FROM
                            gtia_proyectos, gtia_zonas
                         WHERE
                            gtia_proyectos.nombre = '$proyecto' AND
                            gtia_proyectos.id = gtia_zonas.id_proyecto
                         ORDER BY
                            gtia_zonas.fecha_ini ASC";
        $qry_fechaini = $adoMSSQL_SEMTI->Execute($sql_fechaini);
        $fecha_ini    = $qry_fechaini->fields[0];
        
        $sql_fechafin = "SELECT TOP 1
                            gtia_zonas.fecha_fin
                         FROM
                            gtia_proyectos, gtia_zonas
                         WHERE
                            gtia_proyectos.nombre = '$proyecto' AND
                            gtia_proyectos.id = gtia_zonas.id_proyecto
                         ORDER BY
                            gtia_zonas.fecha_fin DESC";
        $qry_fechafin = $adoMSSQL_SEMTI->Execute($sql_fechafin);
        $fecha_fin    = $qry_fechafin->fields[0];
        
        // Calcular los meses del periodo de Garantia
        $fechainicial = new DateTime($fecha_ini);
        $fechafinal   = new DateTime($fecha_fin);               
        $diferencia   = $fechainicial->diff($fechafinal);
        $meses_gtia   = ( $diferencia->y * 12 ) + ( $diferencia->m + 1 );
        
        // Agregar la ctdad de meses al vector de meses
        $meses_arr[$n] = $meses_gtia;
        $fechaini_arr[$n] = $fecha_ini;
    }
    
    // Ordenar el Arreglo de meses para seleccionar el menor valor, comun para todos los proyectos.
    asort($meses_arr);
    
    // Seleccionar el primer mes y construir la Axisa X
    $count_meses_arr = 0;    
    foreach($meses_arr as $valor)
    {
        $count_meses_arr++;
        if($count_meses_arr == 1){

            // xAxix
            $xAxix = '[';
            for ($i = 1; $i < $valor + 1; $i++) {
                if($i==1){ $xAxix .= "'Mes " . $i. "'"; }
                else{ $xAxix .= ",'Mes " . $i. "'"; }
            }            
            $xAxix .= ']';
        }
    }        
         
    // Definir las Series e Inicializarlas
    $serie_sd = array();
    
    for($n = 0; $n < $proyectCount; $n++){
        $serie_sd[$n] = '[';
    }    
    
    // Obtener los datos de las series    
    
    $arr_xAxix   = explode(',', substr(substr($xAxix, 1), 0, -1));
    $count_xAxix = count($arr_xAxix);
    
    for ($i = 0; $i < $count_xAxix; $i++) {
    
        $mes_arr     = explode(' ', $arr_xAxix[$i]);
        $mes_proyect = $mes_arr[1];
            
        for($j = 0; $j < $proyectCount; $j++){
            
            $mes_gtia = $mes_proyect - 1;
            $desde = date('Y-m-d',strtotime($fechaini_arr[$j]." +".$mes_gtia." months"));
            $hasta = date('Y-m-d',strtotime('last day of this month '.$desde));
                        
            // Serie SD
            if ($polo == -1) {
                $sql_sd_total = "SELECT
                                    COUNT(id) AS total
                                FROM
                                    gtia_sd
                                WHERE
                                    proyecto = '$proyectoArr[$j]' AND 
                                    (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
            }
            else {
                $sql_sd_total = "SELECT
                                    COUNT(gtia_sd.id) AS total
                                FROM
                                    gtia_sd, gtia_proyectos
                                WHERE
                                    gtia_sd.proyecto = '$proyectoArr[$j]' AND 
                                    (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta') AND 
                                        gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
            }

            // Filtros    
            if($estado != '' && $estado != 'Todos'){ $sql_sd_total .= " AND gtia_sd.estado = '$estado'"; }
            else{ $sql_sd_total .= " AND gtia_sd.estado != 'No Procede'"; }
            if($tiposd == 'SD Comunes'){ $sql_sd_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
            if($tiposd == 'SD Habitaciones'){ $sql_sd_total .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
                                
            $qry_sd_total = $adoMSSQL_SEMTI->Execute($sql_sd_total);
            
            if ($i == 0) {
                $serie_sd[$j] .= $qry_sd_total->fields[0];
            } else {
                $serie_sd[$j] .= ',' . $qry_sd_total->fields[0];
            }
        }
    }
    
    for($m = 0; $m < $proyectCount; $m++){
        $serie_sd[$m] .= ']';
    }
    
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Comparativa de SD entre Proyectos</title>

        <script type="text/javascript" src="../../js/highcharts423/js/jquery.1.8.2.min.js"></script>
        <script type="text/javascript">
            $(function() {
                // Set up the chart
                $('#container').highcharts({
                //var chart = new Highcharts.Chart({
                   chart: {
                        marginTop: 80
                    },
                    title: {
                        text: '<?php echo $title; ?>'
                    },
                    subtitle: {
                        text: 'ASOCIACIÓN ECONÓMICA INTERNACIONAL UCM - BBI'
                    },
                    xAxis: {
                        categories: <?php echo $xAxix; ?>,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Cantidad de SD'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [
                        <?php
                        if($proyectos != '' && $proyectos != 'Todos'){
                            $count_series = count($serie_sd);
                            for($e = 0; $e < $count_series; $e++){                                 
                        ?>
                            {
                                type: 'column',
                                name: '<?php echo $proyectoArr[$e] ?>',
                                data: <?php echo $serie_sd[$e]; ?>,
                                color: '<?php echo $colors[$e]; ?>'
                            },
                        <?php }} ?>
                        ]
                });

            });
        </script>
    </head>
    <body>
        <script src="../../js/highcharts423/js/highcharts.js"></script>
        <script src="../../js/highcharts423/js/exporting.js"></script>
        <script src="../../js/highcharts423/js/modules/canvas-tools.js"></script>
        <script src="../../js/highcharts423/js/export-csv.js"></script>
        <script src="../../js/highcharts423/js/jspdf.min.js"></script>

        <script src="../../js/highcharts423/js/highcharts-export-clientside.js"></script>

        <center>
            <div id="container" style="width: 95%; height: 500px; margin: 0 auto; margin-top: 30px"></div>
        </center>
    </body>
</html>
