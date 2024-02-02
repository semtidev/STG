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
    $title = 'Comportamiento de las SD que No Proceden';
}
else{
    $title = $_GET['titulo'];
}

// Validar Polo del usuario
$polo = -1;
if (intval($_SESSION['polo']) != 9) {
    $polo = intval($_SESSION['polo']);
}

// Establecer los filtros y rangos
$proyecto  = '';
$tiposd    = '';
$fecha_ini = '';
$fecha_fin = '';

if(isset($_GET['listar'])) {
    
    $listar_array = explode('.',$_GET['listar']);
    if($listar_array[0] != 'Todos') $proyecto = $listar_array[0];
    if($listar_array[1] != 'Todas') $tiposd   = $listar_array[1];
}

if(isset($_GET['desde']) && $_GET['desde'] != 'Inicio'){
    $fecha_ini = $cadenas->fecha_extjs_mssql($_GET['desde']);
}

if(isset($_GET['hasta']) && $_GET['hasta'] != 'Final'){
    $fecha_fin = $cadenas->fecha_extjs_mssql($_GET['hasta']);
}

// Obtener tiempo de Garantia del Proyecto
if($proyecto != ''){

    $sql_fechaini = "SELECT TOP 1
                        gtia_zonas.fecha_ini
                        FROM
                        gtia_proyectos, gtia_zonas
                        WHERE
                        gtia_proyectos.nombre = '$proyecto' AND
                        gtia_proyectos.id = gtia_zonas.id_proyecto
                        ORDER BY
                        gtia_zonas.fecha_ini ASC";
    $qry_fechaini     = $adoMSSQL_SEMTI->Execute($sql_fechaini);
    $fechaini_proyect = $qry_fechaini->fields[0];
    
    $sql_fechafin = "SELECT TOP 1
                        gtia_zonas.fecha_fin
                        FROM
                        gtia_proyectos, gtia_zonas
                        WHERE
                        gtia_proyectos.nombre = '$proyecto' AND
                        gtia_proyectos.id = gtia_zonas.id_proyecto
                        ORDER BY
                        gtia_zonas.fecha_fin DESC";
    $qry_fechafin     = $adoMSSQL_SEMTI->Execute($sql_fechafin);
    $fechafin_proyect = $qry_fechafin->fields[0];
}

//  Construir xAxix

$arMeses = array("", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

$count_mes = 0;
$xAxix = '[';

// Cuando No se filtra un Proyeto
if($proyecto == '' && $fecha_ini == '' && $fecha_fin == ''){
    
    $anio_actual = date('Y');
    $mes_actual  = date('n');
    
    for ($i = $mes_actual; $i <= 12; $i++) {
    
        $xAxix .= "'" . $arMeses[$i] . ' ' . (date('Y') - 1) . "',";
        $count_mes++;
    }
    for ($j = 1; $j < $mes_actual + 1; $j++) {
    
        $count_mes++;
        if ($count_mes < 13) {
            $xAxix .= "'" . $arMeses[$j] . ' ' . date('Y') . "',";
        } else {
            $xAxix .= "'" . $arMeses[$j] . ' ' . date('Y') . "'";
        }
    }        
}
elseif($proyecto == '' && $fecha_ini != '' && $fecha_fin == ''){
    
    // Calcular los meses del periodo de Garantia
    $fechainicial = new DateTime($fecha_ini);
    $fechafinal   = new DateTime(date('Y-m-d'));               
    $diferencia   = $fechainicial->diff($fechafinal);
    $meses_gtia   = ( $diferencia->y * 12 ) + ( $diferencia->m + 1 );
    
    $fechaXaxis = '';
    
    for ($i = 1; $i < $meses_gtia + 1; $i++) {
        
        if($i == 1){  $fechaXaxis = $fecha_ini;  }
        else{
            $fechastandar = date('Y-m-01',strtotime($fechaXaxis));
            $fechaXaxis   = date('Y-m-d',strtotime($fechastandar." +1 months"));
        }
        
        $fechaXaxix_arr = explode('-',$fechaXaxis);
        $mes = $fechaXaxix_arr[1] - 0;
        if ($i == 1) {  $xAxix .= "'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
        else {  $xAxix .= ",'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
    }
}
elseif($fecha_ini != '' && $fecha_fin != ''){
    
    if($fecha_ini < $fecha_fin){ $fecha1 = $fecha_ini; $fecha2 = $fecha_fin; }
    else{ $fecha1 = $fecha_fin; $fecha2 = $fecha_ini; }
    
    // Calcular los meses del periodo de Garantia
    $fechainicial = new DateTime($fecha1);
    $fechafinal   = new DateTime($fecha2);               
    $diferencia   = $fechainicial->diff($fechafinal);
    $meses_gtia   = ( $diferencia->y * 12 ) + ( $diferencia->m + 1 );
    
    $fechaXaxis = '';
    
    for ($i = 1; $i < $meses_gtia + 1; $i++) {
        
        if($i == 1){  $fechaXaxis = $fecha1;  }
        else{
            $fechastandar = date('Y-m-01',strtotime($fechaXaxis));
            $fechaXaxis   = date('Y-m-d',strtotime($fechastandar." +1 months"));
        }
        
        $fechaXaxix_arr = explode('-',$fechaXaxis);
        $mes = $fechaXaxix_arr[1] - 0;
        if ($i == 1) {  $xAxix .= "'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
        else {  $xAxix .= ",'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
    }
}
elseif($proyecto == '' && $fecha_ini == '' && $fecha_fin != ''){
    
    $fecha_fin_arr = explode('-',$fecha_fin);
    $mes           = $fecha_fin_arr[1] - 0;
    
    for ($i = $mes; $i <= 12; $i++) {
    
        $xAxix .= "'" . $arMeses[$i] . ' ' . ($fecha_fin_arr[0] - 1) . "',";
        $count_mes++;
    }
    for ($j = 1; $j < $mes + 1; $j++) {
    
        $count_mes++;
        if ($count_mes < 13) {
            $xAxix .= "'" . $arMeses[$j] . ' ' . $fecha_fin_arr[0] . "',";
        } else {
            $xAxix .= "'" . $arMeses[$j] . ' ' . $fecha_fin_arr[0] . "'";
        }
    }  
}

// Cuando Si se filtra un Proyeto
elseif($proyecto != '' && $fecha_ini == '' && $fecha_fin == ''){
    
    // Calcular los meses del periodo de Garantia
    $fechainicial = new DateTime($fechaini_proyect);
    $fechafinal   = new DateTime($fechafin_proyect);               
    $diferencia   = $fechainicial->diff($fechafinal);
    $meses_gtia   = ( $diferencia->y * 12 ) + ( $diferencia->m + 1 );
    
    $fechaXaxis = '';       
    
    for ($i = 1; $i < $meses_gtia + 1; $i++) {
        
        if($i == 1){  $fechaXaxis = $fechaini_proyect;  }
        else{
            $fechastandar = date('Y-m-01',strtotime($fechaXaxis));
            $fechaXaxis   = date('Y-m-d',strtotime($fechastandar." +1 months"));
        }
        
        $fechaXaxix_arr = explode('-',$fechaXaxis);
        $mes = $fechaXaxix_arr[1] - 0;
        if ($i == 1) {  $xAxix .= "'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
        else {  $xAxix .= ",'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
    }      
}
elseif($proyecto != '' && $fecha_ini != '' && $fecha_fin == ''){
    
    if($fecha_ini < $fechafin_proyect){ $fecha1 = $fecha_ini; $fecha2 = $fechafin_proyect; }
    else{ $fecha1 = $fechafin_proyect; $fecha2 = $fecha_ini; }
    
    // Calcular los meses del periodo de Garantia
    $fechainicial = new DateTime($fecha1);
    $fechafinal   = new DateTime($fecha2);               
    $diferencia   = $fechainicial->diff($fechafinal);
    $meses_gtia   = ( $diferencia->y * 12 ) + ( $diferencia->m + 1 );
    
    $fechaXaxis = '';
    
    for ($i = 1; $i < $meses_gtia + 1; $i++) {
        
        if($i == 1){  $fechaXaxis = $fecha1;  }
        else{
            $fechastandar = date('Y-m-01',strtotime($fechaXaxis));
            $fechaXaxis   = date('Y-m-d',strtotime($fechastandar." +1 months"));
        }
        
        $fechaXaxix_arr = explode('-',$fechaXaxis);
        $mes = $fechaXaxix_arr[1] - 0;
        if ($i == 1) {  $xAxix .= "'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
        else {  $xAxix .= ",'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
    }
}
elseif($proyecto != '' && $fecha_ini == '' && $fecha_fin != ''){
    
    if($fecha_fin < $fechaini_proyect){ $fecha1 = $fecha_fin; $fecha2 = $fechaini_proyect; }
    else{ $fecha1 = $fechaini_proyect; $fecha2 = $fecha_fin; }
    
    // Calcular los meses del periodo de Garantia
    $fechainicial = new DateTime($fecha1);
    $fechafinal   = new DateTime($fecha2);               
    $diferencia   = $fechainicial->diff($fechafinal);
    $meses_gtia   = ( $diferencia->y * 12 ) + ( $diferencia->m + 1 );
    
    $fechaXaxis = '';
    
    for ($i = 1; $i < $meses_gtia + 1; $i++) {
        
        if($i == 1){  $fechaXaxis = $fecha1;  }
        else{
            $fechastandar = date('Y-m-01',strtotime($fechaXaxis));
            $fechaXaxis   = date('Y-m-d',strtotime($fechastandar." +1 months"));
        }
        
        $fechaXaxix_arr = explode('-',$fechaXaxis);
        $mes = $fechaXaxix_arr[1] - 0;
        if ($i == 1) {  $xAxix .= "'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
        else {  $xAxix .= ",'" . $arMeses[$mes] . ' ' . $fechaXaxix_arr[0] . "'";  }
    }
}

$xAxix .= ']';    

// Obtener los datos de las series

$serie_sd = '[';

// Arreglo de meses por numero
$arMesesNum['Ene'] = '01';
$arMesesNum['Feb'] = '02';
$arMesesNum['Mar'] = '03';
$arMesesNum['Abr'] = '04';
$arMesesNum['May'] = '05';
$arMesesNum['Jun'] = '06';
$arMesesNum['Jul'] = '07';
$arMesesNum['Ago'] = '08';
$arMesesNum['Sep'] = '09';
$arMesesNum['Oct'] = '10';
$arMesesNum['Nov'] = '11';
$arMesesNum['Dic'] = '12';

$arr_xAxix = explode(',', substr(substr($xAxix, 1), 0, -1));
$count_meses = count($arr_xAxix);
for ($i = 0; $i < $count_meses; $i++) {

    $mesanio    = explode(' ', substr(substr($arr_xAxix[$i], 1), 0, -1));
    $mes        = $arMesesNum[$mesanio[0]];
    $anio       = $mesanio[1];
    $desde      = $anio . '-' . $mes . '-01';
    $strtotime  = strtotime($desde);
    $ultimo_dia = date('d',strtotime('last day of this month'.date('Y-m-d',$strtotime)));
    $hasta      = $anio . '-' . $mes . '-' . $ultimo_dia;

    // Serie SD
    if ($polo == -1) {
        $sql_sd_total = "SELECT
                            COUNT(id) AS total
                        FROM
                            gtia_sd
                        WHERE
                            estado = 'No Procede' AND 
                            (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
    }
    else {
        $sql_sd_total = "SELECT
                            COUNT(gtia_sd.id) AS total
                        FROM
                            gtia_sd, gtia_proyectos
                        WHERE
                            gtia_sd.estado = 'No Procede' AND 
                            (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta') AND 
                            gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    }

    // Filtros    
    if($proyecto != '' && $proyecto != 'Todos'){ $sql_sd_total .= " AND gtia_sd.proyecto = '$proyecto'"; }
    if($tiposd == 'SD Comunes'){ $sql_sd_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
    if($tiposd == 'SD Habitaciones'){ $sql_sd_total .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
                        
    $qry_sd_total = $adoMSSQL_SEMTI->Execute($sql_sd_total);
    
    if ($i == 0) {
        $serie_sd .= $qry_sd_total->fields[0];
    } else {
        $serie_sd .= ',' . $qry_sd_total->fields[0];
    }

}

$serie_sd .= ']';
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Comportamiento de las SD que No Proceden</title>

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
                            text: 'Cantidad SD'
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
                    series: [{
                            type: 'column',
                            name: 'Solicitudes de Defectación que No Proceden',
                            data: <?php echo $serie_sd; ?>,
                            color: '#3280cf' // Azul
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
