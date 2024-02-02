<?php
// Incluir la clase de conexion
include_once '../sistema/connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once '../sistema/cadenas.php';
$cadenas = new Cadenas();

// Serie de Pastel

$sdTotal     = $_GET['sdTotal'];

$sdPR        = $_GET['sdPR'];
$porcientoPR = ($sdPR / $sdTotal) * 100;

$sdNP        = $_GET['sdNP'];
$porcientoNP = ($sdNP / $sdTotal) * 100;

$sdR         = $_GET['sdR'];
$porcientoR  = ($sdR / $sdTotal) * 100;

$sdF         = $_GET['sdF'];
$porcientoF  = ($sdF / $sdTotal) * 100;

$sdEP         = $_GET['sdEP'];
$porcientoEP  = ($sdEP / $sdTotal) * 100;
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SD ESTADOS</title>

        <script type="text/javascript" src="../../js/highcharts423/js/jquery.1.8.2.min.js"></script>
        <link href="../../resources/css/charts.css" type="text/css" rel="stylesheet">
        
        <script type="text/javascript">
            $(function() {
                $('#codirChartContainer').highcharts({
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 60,
                            margin: 25,
                        }
                    },
                    title: {
                        text: ''
                    }/*,
                    subtitle: {
                        text: 'AEI CCO - Dpto Garantía',
                        x: -20
                    }*/,
                    plotOptions: {
                        pie: {
                            innerSize: 100,
                            depth: 60,
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y:.2f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                            name: '% del Total de SD',
                            data: [
                                {name: '<span style="color:#627998">POR RESOLVER</span> (<?php echo number_format($sdPR, 0); ?>)', y: <?php echo number_format($porcientoPR, 2); ?>, color: '#627998'}, // Verde
                                {name: '<span style="color:#FB7316">RECLAMADAS</span> (<?php echo number_format($sdR, 0); ?>)', y: <?php echo number_format($porcientoR, 2); ?>, color: '#FB7316'},
                                {name: '<span style="color:#44B122">FIRMADAS</span> (<?php echo number_format($sdF, 0); ?>)', y: <?php echo number_format($porcientoF, 2); ?>, color: '#44B122'},
                                {name: '<span style="color:#E51018">NO PROCEDEN</span> (<?php echo number_format($sdNP, 0); ?>)', y: <?php echo number_format($porcientoNP, 2); ?>, color: '#E51018'},
                                {name: '<span style="color:#59A4F1">EN PROCESO</span> (<?php echo number_format($sdEP, 0); ?> SD)', y: <?php echo number_format($porcientoEP, 2); ?>, color: '#59A4F1'}
                            ]
                        }]
                });
            });
        </script>
    </head>
    <body>

        <script src="../../js/highcharts423/js/highcharts.js"></script>
        <script src="../../js/highcharts423/js/highcharts-3d.js"></script>
        <script src="../../js/highcharts423/js/modules/canvas-tools.js"></script>
        <!--<script src="../../js/highcharts423/js/export-csv.js"></script>
        <script src="../../js/highcharts423/js/jspdf.min.js"></script>
        <script src="../../js/highcharts423/js/offline-exporting.js"></script>
        <script src="../../js/highcharts423/js/exporting.js"></script>
        <script src="../../js/highcharts423/js/highcharts-export-clientside.js"></script>-->

        <div id="codirChartContainer"></div>
    </body>
</html>
