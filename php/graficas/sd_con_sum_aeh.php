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
    $title = 'Comportamiento de SD Const/Sumin/AEH';
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
$proyecto      = '';
$estado        = '';
$tiposd        = '';
$fecha_ini     = '';
$fecha_fin     = '';
$fecha_ini_pie = '';
$fecha_fin_pie = '';

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

// Rango de Fechas del Pastel
$xAxix_arr     = explode(',', substr($xAxix, 1));
$count_xAxix   = count($xAxix_arr);
$mesanio       = explode(' ', substr(substr($xAxix_arr[0], 1), 0, -1));
$mes           = $arMesesNum[$mesanio[0]];
$anio          = $mesanio[1];
$fecha_ini_pie = $anio . '-' . $mes . '-01';
$mesanio       = explode(' ', substr(substr($xAxix_arr[$count_xAxix - 1], 1), 0, -1));
$mes           = $arMesesNum[$mesanio[0]];
$anio          = $mesanio[1];
$fecha_fin_pie = $anio . '-' . $mes . '-01';

$xAxix .= ']';

// Obtener los datos de las series

$serie_con = '[';
$serie_sum = '[';
$serie_aeh = '[';
        
$arr_xAxix     = explode(',', substr(substr($xAxix, 1), 0, -1));
    
$count_meses = count($arr_xAxix);
for ($i = 0; $i < $count_meses; $i++) {

    $mesanio    = explode(' ', substr(substr($arr_xAxix[$i], 1), 0, -1));
    $mes        = $arMesesNum[$mesanio[0]];
    $anio       = $mesanio[1];
    $desde      = $anio . '-' . $mes . '-01';
    $strtotime  = strtotime($desde);
    $ultimo_dia = date('d',strtotime('last day of this month'.date('Y-m-d',$strtotime)));
    $hasta      = $anio . '-' . $mes . '-' . $ultimo_dia;
    
    // Serie Constructiva
    if ($polo == -1) {
        $sql_con_total   =  "SELECT
                                COUNT(id) AS total
                            FROM
                                gtia_sd
                            WHERE
                                constructiva = 'Si' AND
                                (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
    }
    else {
        $sql_con_total   =  "SELECT
                                COUNT(gtia_sd.id) AS total
                            FROM
                                gtia_sd, gtia_proyectos
                            WHERE
                                gtia_sd.constructiva = 'Si' AND
                                (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta') AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    }
    // Filtros    
    if($estado != '' && $estado != 'Todos'){ $sql_con_total .= " AND gtia_sd.estado = '$estado'"; }
    else{ $sql_con_total .= " AND gtia_sd.estado != 'No Procede'"; }
    if($proyecto != '' && $proyecto != 'Todos'){ $sql_con_total .= " AND gtia_sd.proyecto = '$proyecto'"; }
    if($tiposd == 'SD Comunes'){ $sql_con_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
    if($tiposd == 'SD Habitaciones'){ $sql_con_total .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
    
    $qry_con_total = $adoMSSQL_SEMTI->Execute($sql_con_total);
    
    if ($i == 0) {
        $serie_con .= $qry_con_total->fields[0];
    } else {
        $serie_con .= ',' . $qry_con_total->fields[0];
    }

    // Serie Suministro
    if ($polo == -1) {
        $sql_sum_total  =   "SELECT
                                COUNT(id) AS total
                            FROM
                                gtia_sd
                            WHERE
                                suministro = 'Si' AND
                                (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
    }
    else {
        $sql_sum_total  =   "SELECT
                                COUNT(gtia_sd.id) AS total
                            FROM
                                gtia_sd, gtia_proyectos
                            WHERE
                                gtia_sd.suministro = 'Si' AND
                                (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta') AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    }
    // Filtros    
    if($estado != '' && $estado != 'Todos'){ $sql_sum_total .= " AND gtia_sd.estado = '$estado'"; }
    else{ $sql_sum_total .= " AND gtia_sd.estado != 'No Procede'"; }
    if($proyecto != '' && $proyecto != 'Todos'){ $sql_sum_total .= " AND gtia_sd.proyecto = '$proyecto'"; }
    if($tiposd == 'SD Comunes'){ $sql_sum_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
    if($tiposd == 'SD Habitaciones'){ $sql_sum_total .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
                            
    $qry_sum_total = $adoMSSQL_SEMTI->Execute($sql_sum_total);
    
    if ($i == 0) {
        $serie_sum .= $qry_sum_total->fields[0];
    } else {
        $serie_sum .= ',' . $qry_sum_total->fields[0];
    }

    // Serie AEH
    if ($polo == -1) {
        $sql_aeh_total   =  "SELECT
                                COUNT(id) AS total
                            FROM
                                gtia_sd
                            WHERE
                                afecta_explotacion = 'Si' AND
                                (fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
    }
    else {
        $sql_aeh_total   =  "SELECT
                                COUNT(gtia_sd.id) AS total
                            FROM
                                gtia_sd, gtia_proyectos
                            WHERE
                                gtia_sd.afecta_explotacion = 'Si' AND
                                (gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta') AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    }
    // Filtros    
    if($estado != '' && $estado != 'Todos'){ $sql_aeh_total .= " AND gtia_sd.estado = '$estado'"; }
    else{ $sql_aeh_total .= " AND gtia_sd.estado != 'No Procede'"; }
    if($proyecto != '' && $proyecto != 'Todos'){ $sql_aeh_total .= " AND gtia_sd.proyecto = '$proyecto'"; }
    if($tiposd == 'SD Comunes'){ $sql_aeh_total .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'"; }
    if($tiposd == 'SD Habitaciones'){ $sql_aeh_total .= " AND gtia_sd.objeto_local LIKE '%BW%'"; }
                            
    $qry_aeh_total = $adoMSSQL_SEMTI->Execute($sql_aeh_total);
    
    if ($i == 0) {
        $serie_aeh .= $qry_aeh_total->fields[0];
    } else {
        $serie_aeh .= ',' . $qry_aeh_total->fields[0];
    }
}

$serie_con .= ']';
$serie_sum .= ']';
$serie_aeh .= ']';

    
// Serie de Pastel

if ($polo == -1) {
    $sql_totalcon = "SELECT COUNT(id) AS total FROM gtia_sd WHERE constructiva = 'Si' AND (fecha_reporte >= '$fecha_ini_pie' AND fecha_reporte <= '$fecha_fin_pie')";
    
    $sql_totalsum = "SELECT COUNT(id) AS total FROM gtia_sd WHERE suministro = 'Si' AND (fecha_reporte >= '$fecha_ini_pie' AND fecha_reporte <= '$fecha_fin_pie')";

    $sql_totalaeh = "SELECT COUNT(id) AS total FROM gtia_sd WHERE afecta_explotacion = 'Si' AND (fecha_reporte >= '$fecha_ini_pie' AND fecha_reporte <= '$fecha_fin_pie')";
}
else {
    $sql_totalcon = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos WHERE gtia_sd.constructiva = 'Si' AND (gtia_sd.fecha_reporte >= '$fecha_ini_pie' AND gtia_sd.fecha_reporte <= '$fecha_fin_pie') AND gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
    
    $sql_totalsum = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos WHERE gtia_sd.suministro = 'Si' AND (gtia_sd.fecha_reporte >= '$fecha_ini_pie' AND gtia_sd.fecha_reporte <= '$fecha_fin_pie') AND gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;

    $sql_totalaeh = "SELECT COUNT(gtia_sd.id) AS total FROM gtia_sd, gtia_proyectos WHERE gtia_sd.afecta_explotacion = 'Si' AND (gtia_sd.fecha_reporte >= '$fecha_ini_pie' AND gtia_sd.fecha_reporte <= '$fecha_fin_pie') AND gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}

if($proyecto != '' && $proyecto != 'Todos'){
    $count_total++;
    $sql_totalcon .= " AND gtia_sd.proyecto = '$proyecto'";
    $sql_totalsum .= " AND gtia_sd.proyecto = '$proyecto'";
    $sql_totalaeh .= " AND gtia_sd.proyecto = '$proyecto'";
}
if($estado != '' && $estado != 'Todos'){
    $sql_totalcon .= " AND gtia_sd.estado = '$estado'";
    $sql_totalsum .= " AND gtia_sd.estado = '$estado'";
    $sql_totalaeh .= " AND gtia_sd.estado = '$estado'";
}
if($tiposd == 'SD Comunes'){
    $sql_totalcon .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
    $sql_totalsum .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
    $sql_totalaeh .= " AND gtia_sd.objeto_local NOT LIKE '%BW%'";
}
if($tiposd == 'SD Habitaciones'){
    $sql_totalcon .= " AND gtia_sd.objeto_local LIKE '%BW%'";
    $sql_totalsum .= " AND gtia_sd.objeto_local LIKE '%BW%'";
    $sql_totalaeh .= " AND gtia_sd.objeto_local LIKE '%BW%'";
}

$qry_totalcon = $adoMSSQL_SEMTI->Execute($sql_totalcon);
$total_con    = $qry_totalcon->fields[0];
$qry_totalsum = $adoMSSQL_SEMTI->Execute($sql_totalsum);
$total_sum    = $qry_totalsum->fields[0];
$qry_totalaeh = $adoMSSQL_SEMTI->Execute($sql_totalaeh);
$total_aeh    = $qry_totalaeh->fields[0];

$total_sd  = $total_con + $total_sum + $total_aeh;

$porcientoCon = 0;
if($total_con > 0 && $total_sd > 0){ $porcientoCon = ($total_con / $total_sd) * 100; }

$porcientoSum = 0;
if($total_sum > 0 && $total_sd > 0){ $porcientoSum = ($total_sum / $total_sd) * 100; }

$porcientoAeh = 0;
if($total_aeh > 0 && $total_sd > 0){ $porcientoAeh = ($total_aeh / $total_sd) * 100; }

?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Comportamiento de SD Const/Sumin/AEH</title>

        <script type="text/javascript" src="../../js/highcharts423/js/jquery.1.8.2.min.js"></script>
        <!--<style type="text/css">
            ${demo.css}
        </style>-->
        <script type="text/javascript">
            $(function() {
                $('#container').highcharts({
                    chart: {
                        marginTop: 80
                    },
                    title: {
                        text: '<?php echo $title; ?>'
                    },
                    subtitle: {
                        text: 'ASOCIACIÓN ECONÓMICA INTERNACIONAL UCM - BBI',
                        x: -20
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
                    /*labels: {
                        items: [{
                                html: 'Total Solicitudes Defectación',
                                style: {
                                    left: '66px',
                                    top: '10px',
                                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                                }
                            }]
                    },*/
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
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
                            color: '#6eaa2e',
                            name: 'Constructiva',
                            data: <?php echo $serie_con; ?>
                        }, {
                            type: 'column',
                            color: '#464643',
                            name: 'Suministro',
                            data: <?php echo $serie_sum; ?>
                        }, {
                            type: 'column',
                            color: '#3280cf',
                            name: 'Afecta Explotación del Hotel',
                            data: <?php echo $serie_aeh; ?>
                        }, /*{
                         type: 'spline',
                         name: 'Average',
                         data: [3, 2.67, 3, 6.33, 3.33],
                         marker: {
                         lineWidth: 2,
                         lineColor: Highcharts.getOptions().colors[3],
                         fillColor: 'white'
                         }
                         },*/ {
                            type: 'pie',
                            name: '% del Total',
                            data: [{
                                    name: 'Constructiva',
                                    y: <?php echo $porcientoCon; ?>,
                                    color: '#6eaa2e' // Verde
                                }, {
                                    name: 'Suministro',
                                    y: <?php echo $porcientoSum; ?>,
                                    color: '#464643' // Negro
                                }, {
                                    name: 'Afecta Explotación del Hotel',
                                    y: <?php echo $porcientoAeh; ?>,
                                    color: '#3280cf' // Azul
                                }],
                            center: [130, 60],
                            size: 100,
                            showInLegend: false,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
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
        <!--<script src="../../js/offline-exporting.js"></script>-->

        <script src="../../js/highcharts423/js/highcharts-export-clientside.js"></script>

        <center>
            <div id="container" style="width: 95%; height: 500px; margin: 0 auto; margin-top: 30px"></div>
        </center>

    </body>
</html>
