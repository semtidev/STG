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

// Título de la gráfica
if(!isset($_GET['titulo'])){
    $title = 'Relación Demora/Tipo de Defecto/Reportes SD';
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
$proyecto  = '';
$estado    = '';
$tiposd    = '';
$fecha_ini = '';
$fecha_fin = '';

if(isset($_GET['listar'])){
    
    $listar_array = explode('.',$_GET['listar']);
    if($listar_array[0] != 'Todos') $proyecto = $listar_array[0];
    if($listar_array[1] != 'Todos') $estado   = $listar_array[1];
    if($listar_array[2] != 'Todas') $tiposd   = $listar_array[2];
}

if(isset($_GET['desde']) && $_GET['desde'] != 'Inicio'){
    $fecha_ini = $cadenas->fecha_extjs_mssql($_GET['desde']);
}

if(isset($_GET['hasta']) && $_GET['hasta'] != 'Final'){
    $fecha_fin = $cadenas->fecha_extjs_mssql($_GET['hasta']);
}

// Obtener los parametros de la xAxis
// xAxix
$xAxix = '[';

$sql_defectos = "SELECT id,descripcion FROM gtia_problemas";
$qry_defectos = $adoMSSQL_SEMTI->Execute($sql_defectos);
$total_defectos = $qry_defectos->RecordCount();
$count_defectos = 0;

if($qry_defectos){
    while (!$qry_defectos->EOF) {

        $count_defectos++;
        if ($count_defectos < $total_defectos) {
            $xAxix .= "'" . $cadenas->utf8($qry_defectos->fields[1]) . "',";
        } else {
            $xAxix .= "'" . $cadenas->utf8($qry_defectos->fields[1]) . "'";
        }
        $defectos[] = $qry_defectos->fields[0];
        
        $qry_defectos->MoveNext();
    }
    $qry_defectos->Close();
}

$xAxix .= ']';

// Obtener los datos de las series

$serie_sd  = '[';
$serie_dp  = '[';
$serie_rep = '[';
                
for ($i = 0; $i < $total_defectos; $i++) {
            
    // Serie SD
    if ($polo == -1) {
        $sql_sd = "SELECT COUNT(id) AS total 
                    FROM gtia_sd 
                    WHERE id_problema = " . $defectos[$i];
    }
    else {
        $sql_sd = "SELECT COUNT(gtia_sd.id) AS total 
                    FROM gtia_sd, gtia_proyectos 
                    WHERE gtia_sd.id_problema = " . $defectos[$i] ." AND 
                            gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    }
    
    // Filtros    
    if($fecha_ini != ''){ $sql_sd .= " AND gtia_sd.fecha_reporte >= '$fecha_ini'"; }
    if($fecha_fin != ''){ $sql_sd .= " AND gtia_sd.fecha_reporte <= '$fecha_fin'"; }
    if($estado != ''){ $sql_sd .= " AND gtia_sd.estado = '$estado'"; }else{ $sql_sd .= " AND gtia_sd.estado != 'No Procede'"; }
    if($proyecto != ''){ $sql_sd .= " AND gtia_sd.proyecto = '$proyecto'"; }
    if($tiposd == 'SD Comunes'){ $sql_sd .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
    if($tiposd == 'SD Habitaciones'){ $sql_sd .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
                    
    $qry_sd = $adoMSSQL_SEMTI->Execute($sql_sd);
    
    if ($i < $total_defectos - 1) {
        $serie_sd .= $qry_sd->fields[0] . ',';
    } else {
        $serie_sd .= $qry_sd->fields[0];
    }

    // Series Demora promedio y Repetitividad		
    if ($polo == -1) {
        $sql_dr = "SELECT id, fecha_reporte, fecha_solucion, suministro, fecha_almacen 
                    FROM gtia_sd 
                    WHERE id_problema = " . $defectos[$i];
    }
    else {
        $sql_dr = "SELECT gtia_sd.id, gtia_sd.fecha_reporte, gtia_sd.fecha_solucion, gtia_sd.suministro, gtia_sd.fecha_almacen 
                    FROM gtia_sd, gtia_proyectos 
                    WHERE gtia_sd.id_problema = " . $defectos[$i] . " AND 
                            gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    }
    
    // Filtros    
    if($fecha_ini != ''){ $sql_dr .= " AND gtia_sd.fecha_reporte >= '$fecha_ini'"; }
    if($fecha_fin != ''){ $sql_dr .= " AND gtia_sd.fecha_reporte <= '$fecha_fin'"; }
    if($estado != ''){ $sql_dr .= " AND gtia_sd.estado = '$estado'"; }else{ $sql_dr .= " AND gtia_sd.estado != 'No Procede'"; }
    if($proyecto != ''){ $sql_dr .= " AND gtia_sd.proyecto = '$proyecto'"; }
    if($tiposd == 'SD Comunes'){ $sql_dr .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
    if($tiposd == 'SD Habitaciones'){ $sql_dr .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
    
    $qry_dr = $adoMSSQL_SEMTI->Execute($sql_dr);

    $sum_demoras   = 0;
    $cont_demora   = 0;
    $repetitividad = 0;

    if($qry_dr){
        while (!$qry_dr->EOF) {

            $suministro = $qry_dr->fields[3];
            $fecha_almacen = $qry_dr->fields[4];
            
            if ($suministro == 'Si' && ($fecha_almacen == '' || $fecha_almacen == null || $fecha_almacen == '1900-01-01')) {
                $calculo_demora = 0;
            }
            else {
                $fin = ($qry_dr->fields[2] != '' && $qry_dr->fields[2] != null && $qry_dr->fields[2] == '1900-01-01') ? $qry_dr->fields[2] : date('Y-m-d');
                if ($suministro == 'No') {
                    $inicio = $qry_dr->fields[1];
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
                       $inicio = $qry_dr->fields[1];
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

            $sum_demoras += $calculo_demora;
            $cont_demora++;
            
            // Repetitividad
            $qry_repetitividad_objetos = $adoMSSQL_SEMTI->Execute("SELECT * FROM gtia_sd_objetos WHERE id_sd = ".$qry_dr->fields[0]);
            $repetitividad += $qry_repetitividad_objetos->RecordCount();
            
            $qry_repetitividad_partes = $adoMSSQL_SEMTI->Execute("SELECT * FROM gtia_sd_partes WHERE id_sd = ".$qry_dr->fields[0]);
            $repetitividad += $qry_repetitividad_partes->RecordCount();
            
            $qry_dr->MoveNext();
        }
        $qry_dr->Close();
    }

    if ($sum_demoras != 0 && $cont_demora != 0) {
        $demora_prom = $sum_demoras / $cont_demora;
    } else {
        $demora_prom = 0;
    }
    if ($i < $total_defectos - 1) {
        $serie_dp  .= number_format($demora_prom, 0) . ',';
        $serie_rep .= number_format($repetitividad, 0) . ',';
    } else {
        $serie_dp  .= number_format($demora_prom, 0);
        $serie_rep .= number_format($repetitividad, 0);
    }

}

$serie_sd  .= ']';
$serie_dp  .= ']';
$serie_rep .= ']';
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Relación Demora/Tipo de Defecto/Reportes SD</title>

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
                        text: 'ASOCIACIÓN ECONÓMICA INTERNACIONAL UCM - BBI',
                        /*floating: true,
                         align: 'right',
                         verticalAlign: 'bottom',
                         y: 15*/
                    },
                    xAxis: {
                        categories: <?php echo $xAxix; ?>,
                        crosshair: true,
                        //marginTop:200,
                        //allowDecimals: false,
                        //tickmarkPlacement: 'on',
                        labels: {
                            rotation: -30,
                            style: {
                                fontSize: '10px',
                                //fontFamily: 'Verdana, sans-serif'
                            },
                            //align: 'left',
                            //x: 3,
                            //y: 80
                        },
                        //maxPadding: 0.05,
                        /*plotLines: [{
                         color: 'black',
                         dashStyle: 'dot',
                         width: 2,
                         value: 65,
                         label: {
                         rotation: 0,
                         y: 15,
                         style: {
                         fontStyle: 'italic'
                         },
                         text: 'Safe fat intake 65g/day'
                         },
                         zIndex: 3
                         }]*/
                        //gridLineWidth: 1,
                        /*plotBands: [{ // visualize the weekend
                         from: 4.5,
                         to: 6.5,
                         color: 'rgba(68, 170, 213, .2)'
                         }]*/
                    },
                    yAxis: {
                        //min: 0,
                        //showFirstLabel: false
                        //max: 10,
                        title: {
                            text: 'Cantidad'
                        }//,
                        //maxPadding: 2,
                        /*labels: {
                         formatter: function () {
                         return this.value / 1000 + 'k';
                         }
                         }*/
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -150,
                        y: 100,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    }, /**/
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                                //valueSuffix: ' millions'
                                //useHTML: true
                                /*useHTML: true,
                                 headerFormat: '<table>',
                                 pointFormat: '<tr><th colspan="2"><h3>{point.country}</h3></th></tr>' +
                                 '<tr><th>Fat intake:</th><td>{point.x}g</td></tr>' +
                                 '<tr><th>Sugar intake:</th><td>{point.y}g</td></tr>' +
                                 '<tr><th>Obesity (adults):</th><td>{point.z}%</td></tr>',
                                 footerFormat: '</table>',
                                 followPointer: true*/
                    },
                    plotOptions: {
                        /*column: {
                         pointPadding: 0.2,
                         stacking: 'normal',
                         depth: 40
                         },*/
                        line: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [{
                            type: 'column',
                            name: 'Solicitudes Defectación (Ctdad)',
                            data: <?php echo $serie_sd; ?>,
                            color: '#3280cf' // Azul,
                            /*dataLabels: {
                                enabled: true
                            }*/
                        }, {
                            type: 'column',
                            name: 'Repetitividad (Ctdad)',
                            data: <?php echo $serie_rep; ?>,
                            color: '#6eaa2e' // Verde
                            /*dataLabels: {
                                enabled: true
                            }*/
                        },{
                            type: 'line',
                            name: 'Demora Promedio (Días)',
                            data: <?php echo $serie_dp; ?>,
                            color: '#464643'
                        }]
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