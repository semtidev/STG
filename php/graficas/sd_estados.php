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
    $title = 'Comportamiento del Estado de las SD';
}
else{
    $title = $_GET['titulo'];
}

// Validar Polo del usuario
$polo = -1;
if (intval($_SESSION['polo']) != 9) {
    $polo = intval($_SESSION['polo']);
}

// Obtener los valores de la grafica    
$count_total = 0;
if ($polo == -1) {
    $sql_total   = "SELECT COUNT(id) AS total FROM gtia_sd WHERE gtia_sd.id > 0";
    $sql_totalPR = "SELECT COUNT(id) AS total FROM gtia_sd WHERE gtia_sd.estado = 'Por Resolver'";
    $sql_totalNP = "SELECT COUNT(id) AS total FROM gtia_sd WHERE gtia_sd.estado = 'No Procede'";
    $sql_totalR  = "SELECT COUNT(id) AS total FROM gtia_sd WHERE gtia_sd.estado = 'Reclamada'";
    $sql_totalF  = "SELECT COUNT(id) AS total FROM gtia_sd WHERE gtia_sd.estado = 'Firmada'";
    $sql_totalEP = "SELECT COUNT(id) AS total FROM gtia_sd WHERE gtia_sd.estado = 'En Proceso'";
}
else {
    $sql_total   = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos 
                        WHERE gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    $sql_totalPR = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos 
                        WHERE gtia_sd.estado = 'Por Resolver' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
                            gtia_proyectos.id_polo = ". $polo;
    $sql_totalNP = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos 
                        WHERE gtia_sd.estado = 'No Procede' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
                            gtia_proyectos.id_polo = ". $polo;
    $sql_totalR  = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos 
                        WHERE gtia_sd.estado = 'Reclamada' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
                            gtia_proyectos.id_polo = ". $polo;
    $sql_totalF  = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos 
                        WHERE gtia_sd.estado = 'Firmada' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
                            gtia_proyectos.id_polo = ". $polo;
    $sql_totalEP = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos 
                        WHERE gtia_sd.estado = 'En Proceso' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
                            gtia_proyectos.id_polo = ". $polo;
}

if(isset($_GET['listar'])){
    
    $listar_array = explode('.',$_GET['listar']);
    $proyecto     = $listar_array[0];
    $tiposd       = $listar_array[1];
    
    if($proyecto != 'Todos'){
        $count_total++;
        $sql_total   .= " AND gtia_sd.proyecto = '$proyecto'";
        $sql_totalPR .= " AND gtia_sd.proyecto = '$proyecto'";
        $sql_totalNP .= " AND gtia_sd.proyecto = '$proyecto'";
        $sql_totalR  .= " AND gtia_sd.proyecto = '$proyecto'";
        $sql_totalF  .= " AND gtia_sd.proyecto = '$proyecto'";
        $sql_totalEP .= " AND gtia_sd.proyecto = '$proyecto'";
    }
    
    if ($tiposd == 'SD Comunes') {
        if($count_total == 0) { $sql_total .= " WHERE gtia_sd.objeto_local NOT LIKE '%BW%'"; }else{ $sql_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
        $sql_totalPR .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
        $sql_totalNP .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
        $sql_totalR  .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
        $sql_totalF  .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
        $sql_totalEP .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
    }
    
    if ($tiposd == 'SD Habitaciones') {
        if($count_total == 0) { $sql_total .= " WHERE gtia_sd.objeto_local LIKE '%BW%'"; }else{ $sql_total .= $sql_total .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
        $sql_totalPR .= " AND gtia_sd.objeto_local LIKE '%BW%'";
        $sql_totalNP .= " AND gtia_sd.objeto_local LIKE '%BW%'";
        $sql_totalR  .= " AND gtia_sd.objeto_local LIKE '%BW%'";
        $sql_totalF  .= " AND gtia_sd.objeto_local LIKE '%BW%'";
        $sql_totalEP .= " AND gtia_sd.objeto_local LIKE '%BW%'";
    } 
}

$qry_total = $adoMSSQL_SEMTI->Execute($sql_total);
$total_sd  = $qry_total->fields[0];

$qry_totalPR = $adoMSSQL_SEMTI->Execute($sql_totalPR);
$total_PR    = $qry_totalPR->fields[0];
$porcientoPR = ($total_PR > 0 && $total_sd > 0) ? ($total_PR / $total_sd) * 100 : 0;

$qry_totalNP = $adoMSSQL_SEMTI->Execute($sql_totalNP);
$total_NP    = $qry_totalNP->fields[0];
$porcientoNP = ($total_NP > 0 && $total_sd > 0) ? ($total_NP / $total_sd) * 100 : 0;

$qry_totalR = $adoMSSQL_SEMTI->Execute($sql_totalR);
$total_R    = $qry_totalR->fields[0];
$porcientoR = ($total_R > 0 && $total_sd > 0) ? ($total_R / $total_sd) * 100 : 0;

$qry_totalF = $adoMSSQL_SEMTI->Execute($sql_totalF);
$total_F    = $qry_totalF->fields[0];
$porcientoF = ($total_F > 0 && $total_sd > 0) ? ($total_F / $total_sd) * 100 : 0;

$qry_totalEP = $adoMSSQL_SEMTI->Execute($sql_totalEP);
$total_EP    = $qry_totalEP->fields[0];
$porcientoEP = ($total_EP > 0 && $total_sd > 0) ? ($total_EP / $total_sd) * 100 : 0;

?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SD ESTADOS</title>

        <script type="text/javascript" src="../../js/highcharts423/js/jquery.1.8.2.min.js"></script>
        <!--<style type="text/css">
            ${demo.css}
        </style>-->
        <script type="text/javascript">
            $(function() {
                $('#container').highcharts({
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 50,
                            margin: 0,
                        }
                    },
                    title: {
                        text: '<?php echo $title; ?>'
                    },
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 45,
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y:.2f} % del total',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                            name: '% del Total de SD',
                            data: [
                                {name: '<span style="color:#627998">POR RESOLVER</span> (<?php echo number_format($total_PR, 0); ?> SD)', y: <?php echo number_format($porcientoPR, 2); ?>, color: '#627998'}, // Verde
                                {name: '<span style="color:#FB7316">RECLAMADAS</span> (<?php echo number_format($total_R, 0); ?> SD)', y: <?php echo number_format($porcientoR, 2); ?>, color: '#FB7316'},
                                {name: '<span style="color:#44B122">FIRMADAS</span> (<?php echo number_format($total_F, 0); ?> SD)', y: <?php echo number_format($porcientoF, 2); ?>, color: '#44B122'},
                                {name: '<span style="color:#E51018">NO PROCEDEN</span> (<?php echo number_format($total_NP, 0); ?> SD)', y: <?php echo number_format($porcientoNP, 2); ?>, color: '#E51018'},
                                {name: '<span style="color:#59A4F1">EN PROCESO</span> (<?php echo number_format($total_EP, 0); ?> SD)', y: <?php echo number_format($porcientoEP, 2); ?>, color: '#59A4F1'}
                            ]
                        }]
                });
            });
        </script>
    </head>
    <body>

        <script src="../../js/highcharts423/js/highcharts.js"></script>
        <script src="../../js/highcharts423/js/highcharts-3d.js"></script>
        <script src="../../js/highcharts423/js/exporting.js"></script>
        <script src="../../js/highcharts423/js/modules/canvas-tools.js"></script>
        <script src="../../js/highcharts423/js/export-csv.js"></script>
        <script src="../../js/highcharts423/js/jspdf.min.js"></script>
        <!--<script src="../../js/offline-exporting.js"></script>-->

        <script src="../../js/highcharts423/js/highcharts-export-clientside.js"></script>

        <center><div id="container" style="min-width: 400px; height: 350px; margin: 0;"></div></center>
    </body>
</html>
