<?php
// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

//  LIBRERIA ADODB5  
include_once("../../../../php/adodb519/adodb.inc.php");

// Conexion a Base de Datos
$adoMSSQL_SEMTI = ADONewConnection('odbc_mssql');

$sql_dsn  = 'Driver={SQL Server};Server=localhost;Database=Garantia;';
$sql_user = 'sa';
$sql_pass = 'webmaster';

$adoMSSQL_SEMTI->PConnect($sql_dsn,$sql_user,$sql_pass);

// Incluir la clase de tratamiento de cadenas
include_once '../../../../php/sistema/cadenas.php';
$cadenas = new Cadenas();

// Incluir la Paleta de Colores del Sistema
include_once '../../../../php/sistema/colors.php';

// Validar Polo del usuario
$polo = -1;
if (intval($_SESSION['polo']) != 9) {
    $polo = intval($_SESSION['polo']);
}

/////////////////////////////////////////////////////
//////////      CAJAS DE INFORMACION       //////////
/////////////////////////////////////////////////////

// SD Pendientes
// ----------------------

// Pendientes
if ($polo == -1) {
	$sql_sdpend   = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE estado = 'Por Resolver'";
} else {
	$sql_sdpend   = "SELECT COUNT(gtia_sd.id) AS ctdad 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.estado = 'Por Resolver' AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_sdpend   = $adoMSSQL_SEMTI->Execute($sql_sdpend);
$ctdad_sdpend = $qry_sdpend->fields[0];

// Firmadas
if ($polo == -1) {
	$sql_sdfirm   = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE estado = 'Firmada'";
} else {
	$sql_sdfirm   = "SELECT COUNT(gtia_sd.id) AS ctdad 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.estado = 'Firmada' AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_sdfirm   = $adoMSSQL_SEMTI->Execute($sql_sdfirm);
$ctdad_sdfirm = $qry_sdfirm->fields[0];

// Reclamadas
if ($polo == -1) {
	$sql_sdrecl   = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE estado = 'Reclamada'";
} else {
	$sql_sdrecl   = "SELECT COUNT(gtia_sd.id) AS ctdad 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.estado = 'Reclamada' AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_sdrecl   = $adoMSSQL_SEMTI->Execute($sql_sdrecl);
$ctdad_sdrecl = $qry_sdrecl->fields[0];

// En proceso
if ($polo == -1) {
	$sql_sdproc   = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE estado = 'En Proceso'";
} else {
	$sql_sdproc   = "SELECT COUNT(gtia_sd.id) AS ctdad 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.estado = 'En Proceso' AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_sdproc   = $adoMSSQL_SEMTI->Execute($sql_sdproc);
$ctdad_sdproc = $qry_sdproc->fields[0];

// Total
if ($polo == -1) {
	$sql_sdtotal   = "SELECT COUNT(id) AS ctdad FROM gtia_sd";
} else {
	$sql_sdtotal   = "SELECT COUNT(gtia_sd.id) AS ctdad 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_sdtotal   = $adoMSSQL_SEMTI->Execute($sql_sdtotal);
$ctdad_sdtotal = $qry_sdtotal->fields[0];

// Porciento
$sdpend_porciento = 0;
if($ctdad_sdpend > 0 && $ctdad_sdtotal > 0)
$sdpend_porciento = ($ctdad_sdpend / $ctdad_sdtotal) * 100;

// Habitaciones Fuera de Orden
// ------------------------------------

// HFO
if ($polo == -1) {
	$sql_hfo  = "SELECT
					gtia_partes.nombre
					FROM
					gtia_sd_partes, gtia_partes
					WHERE
					gtia_sd_partes.estado = 'Por Resolver' AND
					gtia_sd_partes.id_parte = gtia_partes.id";
} else {
	$sql_hfo  = "SELECT gtia_partes.nombre
					FROM gtia_sd_partes, gtia_partes, gtia_sd, gtia_proyectos
					WHERE gtia_sd_partes.estado = 'Por Resolver' AND
						gtia_sd_partes.id_parte = gtia_partes.id AND 
						gtia_sd_partes.id_sd = gtia_sd.id AND
						gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_hfo   = $adoMSSQL_SEMTI->Execute($sql_hfo);
$ctdad_hfo = 0;
while(!$qry_hfo->EOF){
	$nombre = $qry_hfo->fields[0];
	if(is_numeric($nombre)){
		$ctdad_hfo++;
	}
	$qry_hfo->MoveNext();
}

// Total
if ($polo == -1) {
	$sql_habit = "SELECT
					gtia_partes.nombre
					FROM
					gtia_sd_partes, gtia_partes
					WHERE
					gtia_sd_partes.id_parte = gtia_partes.id";
} else {
	$sql_habit = "SELECT gtia_partes.nombre
					FROM gtia_sd_partes, gtia_partes, gtia_sd, gtia_proyectos
					WHERE gtia_sd_partes.id_parte = gtia_partes.id AND 
						gtia_sd_partes.id_sd = gtia_sd.id AND
						gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_habit   = $adoMSSQL_SEMTI->Execute($sql_habit);
$ctdad_habit = 0;
while(!$qry_habit->EOF){
	$nombre = $qry_habit->fields[0];
	if(is_numeric($nombre)){
		$ctdad_habit++;
	}
	$qry_habit->MoveNext();
}

// Porciento
$hfo_porciento = 0;
if($ctdad_hfo > 0 && $ctdad_habit > 0)
$hfo_porciento = ($ctdad_hfo / $ctdad_habit) * 100;


// Costo de Garantía
// ------------------------

// Costo de SD
if ($polo == -1) {
	$sql_costo   = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE estado = 'Por Resolver'";
} else {
	$sql_costo   = "SELECT SUM(gtia_sd.costo) AS costo 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.estado = 'Por Resolver' AND 
                                gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_costo   = $adoMSSQL_SEMTI->Execute($sql_costo);
$costo_gtia = $qry_costo->fields[0];

// Presupuesto proyecto
if ($polo == -1) {
	$sql_presupuesto   = "SELECT SUM(presupuesto) AS presupuesto
							FROM gtia_proyectos 
							WHERE gtia_proyectos.activo = 1";
} else {
	$sql_presupuesto   = "SELECT SUM(presupuesto) AS presupuesto
							FROM gtia_proyectos 
							WHERE gtia_proyectos.activo = 1 AND gtia_proyectos.id_polo = ". $polo;
}
$qry_presupuesto   = $adoMSSQL_SEMTI->Execute($sql_presupuesto);
$presupuesto = $qry_presupuesto->fields[0];

// Porciento
$presupuesto_porciento = 0;
if($costo_gtia > 0 && $presupuesto > 0)
$presupuesto_porciento = ($costo_gtia / $presupuesto) * 100;

////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////
//////////      GRAFICA PRINCIPALES INDICADORES       //////////
////////////////////////////////////////////////////////////////

$xAxix         = '[';
$serie_sdpend  = '[';
$serie_hfo     = '[';
$serie_presup  = '[';
$proyect_count = 0;

if ($polo == -1) {
	$sql_proyects = "SELECT gtia_proyectos.id, gtia_proyectos.nombre, gtia_proyectos.presupuesto, syst_polos.abbr 
						FROM gtia_proyectos, syst_polos 
						WHERE activo = 1 AND gtia_proyectos.id_polo = syst_polos.id";
} else {
	$sql_proyects = "SELECT gtia_proyectos.id, gtia_proyectos.nombre, gtia_proyectos.presupuesto, syst_polos.abbr 
						FROM gtia_proyectos, syst_polos 
						WHERE activo = 1 AND gtia_proyectos.id_polo = syst_polos.id AND gtia_proyectos.id_polo = ". $polo;
}
$qry_proyects = $adoMSSQL_SEMTI->Execute($sql_proyects);

if($qry_proyects->RecordCount() > 0){
	
	$recordcount = $qry_proyects->RecordCount();
	
	while(!$qry_proyects->EOF){
		
		$proyect_count++;
		
		// xAxix
		$proyect_name = $qry_proyects->fields[1];
		$polo_abbr = $qry_proyects->fields[3];
		if($proyect_count < $recordcount){	$xAxix .= "'". $proyect_name ." (". $polo_abbr .")',";  }
		else{  $xAxix .= "'".$proyect_name." (". $polo_abbr .")'";  }
		
		// Serie SD Pendientes
		$sql_sdpend   = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE proyecto = '$proyect_name' AND estado = 'Por Resolver'";
		$qry_sdpend   = $adoMSSQL_SEMTI->Execute($sql_sdpend);
		$sdpendientes = $qry_sdpend->fields[0];
		if($proyect_count < $recordcount){	$serie_sdpend .= $sdpendientes.",";  }
		else{  $serie_sdpend .= $sdpendientes;  }
		
		// Serie HFO
		$sql_hfo  = "SELECT
						gtia_partes.nombre
					FROM
						gtia_sd, gtia_sd_partes, gtia_partes
					WHERE
						gtia_sd.proyecto = '$proyect_name' AND 
						gtia_sd.id = gtia_sd_partes.id_sd AND
						gtia_sd_partes.estado = 'Por Resolver' AND
						gtia_sd_partes.id_parte = gtia_partes.id";
		$qry_hfo   = $adoMSSQL_SEMTI->Execute($sql_hfo);
		$hfo = 0;
		while(!$qry_hfo->EOF){
			$nombre = $qry_hfo->fields[0];
			if(is_numeric($nombre)){
				$hfo++;
			}
			$qry_hfo->MoveNext();
		}
		if($proyect_count < $recordcount){	$serie_hfo .= $hfo.",";  }
		else{  $serie_hfo .= $hfo;  }
		
		// Serie Presupuesto			
		$sql_bcosto   = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE proyecto = '$proyect_name'";
		$qry_bcosto   = $adoMSSQL_SEMTI->Execute($sql_bcosto);
		$bar_costo = $qry_bcosto->fields[0] / 1000;	
		if($proyect_count < $recordcount){	$serie_presup .= $bar_costo . ",";  }
		else{  $serie_presup .= $bar_costo;  }
		
		$qry_proyects->MoveNext();
	}
}

$xAxix         .= ']';
$serie_sdpend  .= ']';
$serie_hfo     .= ']';
$serie_presup  .= ']';
///////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////
//////////      GRAFICA SOLICITUDES DE DEFECTACION       //////////
///////////////////////////////////////////////////////////////////

$serie_sd      = '[';
$proyect_count = 0;
	
if ($polo == -1) {
	$sql_proyects = "SELECT gtia_proyectos.nombre, syst_polos.abbr 
						FROM gtia_proyectos, syst_polos 
						WHERE activo = 1 AND gtia_proyectos.id_polo = syst_polos.id";
} else {
	$sql_proyects = "SELECT gtia_proyectos.nombre, syst_polos.abbr 
						FROM gtia_proyectos, syst_polos 
						WHERE activo = 1 AND gtia_proyectos.id_polo = syst_polos.id AND gtia_proyectos.id_polo = ". $polo;
}
$qry_proyects = $adoMSSQL_SEMTI->Execute($sql_proyects);

if($qry_proyects->RecordCount() > 0){
	
	$recordcount = $qry_proyects->RecordCount();
	
	while(!$qry_proyects->EOF){
		
		$proyect_count++;
		$proyect_name = $qry_proyects->fields[0];
		$polo_abbr = $qry_proyects->fields[1];
		$sql_sd    = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE proyecto = '$proyect_name' AND estado != 'No Procede'";
		$qry_sd    = $adoMSSQL_SEMTI->Execute($sql_sd);
		$ctdad_sd  = $qry_sd->fields[0];
		$porciento = 0;
		
		if($ctdad_sdtotal > 0 && $ctdad_sd > 0){
			$porciento = ($ctdad_sd / $ctdad_sdtotal) * 100;
		}
		
		if($proyect_count < $recordcount){
			$serie_sd .= "{name: '".$proyect_name." (".$polo_abbr.") - ".$ctdad_sd." SD', y: ".$porciento.", color: '".$colors[$proyect_count-1]."'},";
		}
		else{
			$serie_sd .= "{name: '".$proyect_name." (".$polo_abbr.") - ".$ctdad_sd." SD', y: ".$porciento.", color: '".$colors[$proyect_count-1]."'}";
		}
		
		$qry_proyects->MoveNext();
	}
}

$serie_sd .= ']';
///////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////
//////////      PANEL DEFECTOS MAS REPETITIVOS       //////////
///////////////////////////////////////////////////////////////

$sql_defectos   = "SELECT id,descripcion FROM gtia_problemas";
$qry_defectos   = $adoMSSQL_SEMTI->Execute($sql_defectos);
$array_defectos = array(); 

if($qry_defectos){
	while (!$qry_defectos->EOF) {
		
		if ($polo == -1) {
			$sql_sdr = "SELECT id, costo FROM gtia_sd WHERE id_problema = " . $qry_defectos->fields[0];
		} else {
			$sql_sdr = "SELECT gtia_sd.id, gtia_sd.costo 
							FROM gtia_sd, gtia_proyectos 
							WHERE gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo ." AND 
								gtia_sd.id_problema = " . $qry_defectos->fields[0];
		}
		$qry_sdr = $adoMSSQL_SEMTI->Execute($sql_sdr);
	
		$repetitividad = 0;
		$costos = 0;
	
		if($qry_sdr){
			while (!$qry_sdr->EOF) {
						
				// Repetitividad
				$qry_repetitividad_objetos = $adoMSSQL_SEMTI->Execute("SELECT * FROM gtia_sd_objetos WHERE id_sd = ".$qry_sdr->fields[0]);
				$repetitividad += $qry_repetitividad_objetos->RecordCount();
				
				$qry_repetitividad_partes = $adoMSSQL_SEMTI->Execute("SELECT * FROM gtia_sd_partes WHERE id_sd = ".$qry_sdr->fields[0]);
				$repetitividad += $qry_repetitividad_partes->RecordCount();

				// Costos
				$costos += $qry_sdr->fields[1];
				
				$qry_sdr->MoveNext();
			}
			$qry_sdr->Close();
		}
		
		$defecto = $cadenas->utf8($qry_defectos->fields[1]);
		$array_defectos[$defecto] = [$repetitividad, $costos];
		
		$qry_defectos->MoveNext();
	}
	$qry_defectos->Close();
}

arsort($array_defectos);
////////////////////////////////////////////////////////


////////////////////////////////////////////////////////
//////////      PANEL COSTO DE GARANTIA       //////////
////////////////////////////////////////////////////////

// Costo total
if ($polo == -1) {
	$sql_total_costo = "SELECT SUM(costo) AS costo FROM gtia_sd";
} else {
	$sql_total_costo = "SELECT SUM(gtia_sd.costo) AS costo 
							FROM gtia_sd, gtia_proyectos 
							WHERE gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_total_costo = $adoMSSQL_SEMTI->Execute($sql_total_costo);
$total_costo = $qry_total_costo->fields[0];

// SD Constructiva
if ($polo == -1) {
	$sql_construct = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE constructiva = 'Si'";
	$sql_construct_costo = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE constructiva = 'Si'";
} else {
	$sql_construct = "SELECT COUNT(gtia_sd.id) AS ctdad 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.constructiva = 'Si' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
							gtia_proyectos.id_polo = ". $polo;
	$sql_construct_costo = "SELECT SUM(gtia_sd.costo) AS costo 
								FROM gtia_sd, gtia_proyectos 
								WHERE gtia_sd.constructiva = 'Si' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
									gtia_proyectos.id_polo = ". $polo;
}
$qry_construct = $adoMSSQL_SEMTI->Execute($sql_construct);
$qry_construct_costo = $adoMSSQL_SEMTI->Execute($sql_construct_costo);
$ctdad_construct = $qry_construct->fields[0];
$ctdad_construct_costo = $qry_construct_costo->fields[0];

// SD AEH
if ($polo == -1) {
	$sql_aeh = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE afecta_explotacion = 'Si'";
	$sql_aeh_costo = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE afecta_explotacion = 'Si'";
} else {
	$sql_aeh = "SELECT COUNT(gtia_sd.id) AS ctdad 
					FROM gtia_sd, gtia_proyectos 
					WHERE gtia_sd.afecta_explotacion = 'Si' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
						gtia_proyectos.id_polo = ". $polo;
	$sql_aeh_costo = "SELECT SUM(gtia_sd.costo) AS costo 
								FROM gtia_sd, gtia_proyectos 
								WHERE gtia_sd.afecta_explotacion = 'Si' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
									gtia_proyectos.id_polo = ". $polo;						
}
$qry_aeh = $adoMSSQL_SEMTI->Execute($sql_aeh);
$qry_aeh_costo = $adoMSSQL_SEMTI->Execute($sql_aeh_costo);
$ctdad_aeh = $qry_aeh->fields[0];
$ctdad_aeh_costo = $qry_aeh_costo->fields[0];

// SD Suministro
if ($polo == -1) {
	$sql_sumin = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE suministro = 'Si'";
	$sql_sumin_costo = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE suministro = 'Si'";
} else {
	$sql_sumin = "SELECT COUNT(gtia_sd.id) AS ctdad 
					FROM gtia_sd, gtia_proyectos 
					WHERE gtia_sd.suministro = 'Si' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
						gtia_proyectos.id_polo = ". $polo;
	$sql_sumin_costo = "SELECT SUM(gtia_sd.costo) AS costo 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.suministro = 'Si' AND gtia_proyectos.id = gtia_sd.id_proyecto AND 
							gtia_proyectos.id_polo = ". $polo;
}
$qry_sumin = $adoMSSQL_SEMTI->Execute($sql_sumin);
$qry_sumin_costo = $adoMSSQL_SEMTI->Execute($sql_sumin_costo);
$ctdad_sumin = $qry_sumin->fields[0];
$ctdad_sumin_costo = $qry_sumin_costo->fields[0];

// Importación
if ($polo == -1) {
	$sql_import = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE suministro = 'Si' AND tipo_compra = 'Import'";
	$sql_import_costo = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE suministro = 'Si' AND tipo_compra = 'Import'";
} else {
	$sql_import = "SELECT COUNT(gtia_sd.id) AS ctdad 
					FROM gtia_sd, gtia_proyectos 
					WHERE gtia_sd.suministro = 'Si' AND tipo_compra = 'Import' AND 
						gtia_proyectos.id = gtia_sd.id_proyecto AND	gtia_proyectos.id_polo = ". $polo;
	$sql_import_costo = "SELECT SUM(gtia_sd.costo) AS costo 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.suministro = 'Si' AND tipo_compra = 'Import' AND 
							gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_import = $adoMSSQL_SEMTI->Execute($sql_import);
$qry_import_costo = $adoMSSQL_SEMTI->Execute($sql_import_costo);
$ctdad_import = $qry_import->fields[0];
$ctdad_import_costo = $qry_import_costo->fields[0];

// Compra Interna
if ($polo == -1) {
	$sql_inter = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE suministro = 'Si' AND tipo_compra = 'Interna'";
	$sql_inter_costo = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE suministro = 'Si' AND tipo_compra = 'Interna'";
} else {
	$sql_inter = "SELECT COUNT(gtia_sd.id) AS ctdad 
					FROM gtia_sd, gtia_proyectos 
					WHERE gtia_sd.suministro = 'Si' AND tipo_compra = 'Interna' AND 
						gtia_proyectos.id = gtia_sd.id_proyecto AND	gtia_proyectos.id_polo = ". $polo;
	$sql_inter_costo = "SELECT SUM(gtia_sd.costo) AS costo 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.suministro = 'Si' AND tipo_compra = 'Interna' AND 
							gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_inter = $adoMSSQL_SEMTI->Execute($sql_inter);
$qry_inter_costo = $adoMSSQL_SEMTI->Execute($sql_inter_costo);
$ctdad_inter = $qry_inter->fields[0];
$ctdad_inter_costo = $qry_inter_costo->fields[0];

// Compra Local
if ($polo == -1) {
	$sql_local = "SELECT COUNT(id) AS ctdad FROM gtia_sd WHERE suministro = 'Si' AND tipo_compra = 'Local'";
	$sql_local_costo = "SELECT SUM(costo) AS costo FROM gtia_sd WHERE suministro = 'Si' AND tipo_compra = 'Local'";
} else {
	$sql_local = "SELECT COUNT(gtia_sd.id) AS ctdad 
					FROM gtia_sd, gtia_proyectos 
					WHERE gtia_sd.suministro = 'Si' AND tipo_compra = 'Local' AND 
						gtia_proyectos.id = gtia_sd.id_proyecto AND	gtia_proyectos.id_polo = ". $polo;
	$sql_local_costo = "SELECT SUM(gtia_sd.costo) AS costo 
						FROM gtia_sd, gtia_proyectos 
						WHERE gtia_sd.suministro = 'Si' AND tipo_compra = 'Local' AND 
							gtia_proyectos.id = gtia_sd.id_proyecto AND gtia_proyectos.id_polo = ". $polo;
}
$qry_local = $adoMSSQL_SEMTI->Execute($sql_local);
$qry_local_costo = $adoMSSQL_SEMTI->Execute($sql_local_costo);
$ctdad_local = $qry_local->fields[0];
$ctdad_local_costo = $qry_local_costo->fields[0];
///////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////
//////////      GRAFICA COMPORTAMINEENTO DE SD       //////////
///////////////////////////////////////////////////////////////

//  Construir xAxix

$arMeses = array("", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

$count_mes = 0;
$xAxix_comportsd = '[';

$anio_actual = date('Y');
$mes_actual  = date('n');

for ($i = $mes_actual; $i <= 12; $i++) {

	$xAxix_comportsd .= "'" . $arMeses[$i] . ' ' . (date('Y') - 1) . "',";
	$count_mes++;
}
for ($j = 1; $j < $mes_actual + 1; $j++) {

	$count_mes++;
	if ($count_mes < 13) {
		$xAxix_comportsd .= "'" . $arMeses[$j] . ' ' . date('Y') . "',";
	} else {
		$xAxix_comportsd .= "'" . $arMeses[$j] . ' ' . date('Y') . "'";
	}
}        

$xAxix_comportsd .= ']';    

// Obtener los datos de las series

$serie_comportsd = '[';

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

$arr_xAxix = explode(',', substr(substr($xAxix_comportsd, 1), 0, -1));
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
		$sql_comportsd = "SELECT COUNT(id) AS total
							FROM gtia_sd
							WHERE estado != 'No Procede' AND 
								(fecha_reporte >= '$desde' AND fecha_reporte <= '$hasta')";
	} else {
		$sql_comportsd = "SELECT COUNT(gtia_sd.id) AS total
							FROM gtia_sd, gtia_proyectos
							WHERE gtia_sd.estado != 'No Procede' AND 
								(gtia_sd.fecha_reporte >= '$desde' AND gtia_sd.fecha_reporte <= '$hasta') AND 
								gtia_proyectos.id = gtia_sd.id_proyecto AND	gtia_proyectos.id_polo = ". $polo;
	}
						
	$qry_comportsd = $adoMSSQL_SEMTI->Execute($sql_comportsd);
	
	if ($i == 0) {
		$serie_comportsd .= $qry_comportsd->fields[0];
	} else {
		$serie_comportsd .= ',' . $qry_comportsd->fields[0];
	}

}

$serie_comportsd .= ']';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Garant&iacute;a | Portada</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<!-- jvectormap -->
	<link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="dist/css/controlpanel.min.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
		 folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
	
	<!--   GRAFICAS   -->
	<script type="text/javascript" src="../../../../js/highcharts423/js/jquery.1.8.2.min.js"></script>
	<script type="text/javascript">
		$(function() {
			
			// Main Chart
			$('#main_chart').highcharts({
			   chart: {
					marginTop: 20
				},
				title: false,
				subtitle: false,
				xAxis: {
					categories: <?php echo $xAxix; ?>,
					crosshair: true
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Cantidad'
					}
				},
				tooltip: {
					headerFormat: '<span style="font-size:12px; font-weight:bold;">{point.key}</span><table>',
					pointFormat: '<tr><td style="padding:0">{series.name}:&nbsp;&nbsp;</td>' +
							'<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
				},
				plotOptions: {
					column: {
						shadow: true,
						pointPadding: 0.2,
						dataLabels: {
							enabled: true
						}
					},
					line: {
						dataLabels: {
							enabled: true
						}
					},
					areaspline: {
						dataLabels: {
							enabled: true
						}
					}
				},
				series: [{
						type: 'areaspline',
						name: 'SD Pendientes',
						data: <?php echo $serie_sdpend; ?>,
						color: '#1674d3'
					}, {
						type: 'column',
						name: 'Ejecución del Presupuesto (MP)',
						data: <?php echo $serie_presup; ?>,
						color: '#04bb2b'
					}, {
						type: 'column',
						name: 'Habitaciones Fuera de Orden',
						data: <?php echo $serie_hfo; ?>,
						color: '#cd230e'
				}]
			});
			
			// SD Chart
			$('#sd_chart').highcharts({
				chart: {
					type: 'pie',
					options3d: {
						enabled: true,
						alpha: 45,
						beta: 0
					},
					marginTop: -30,
					marginBottom: 45
				},
				title: false,
				tooltip: {
					headerFormat: '<span style="font-size:12px; font-weight:bold;">{point.key}</span><table>',
					pointFormat: '<tr><td style="padding:0">{series.name}: <b>{point.percentage:.1f}%&nbsp;</td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						innerSize: 100,
						depth: 35,
						dataLabels: {
							enabled: false
						},
                        showInLegend: true
					}
				},
				series: [{
					type: 'pie',
					name: 'Porciento del Total',
					data: <?php echo $serie_sd; ?>
				}]
			});
			
			// Month SD Chart
			$('#monthsd_chart').highcharts({
				chart: {
					type: 'column',
					margin: 50,
					marginLeft:50,
					marginTop:20,
					marginRight:5,
					options3d: {
						enabled: true,
						alpha: 5,
						beta: 0,
						depth: 100
					}
				},
				title: false,
				subtitle: false,
				tooltip: {
					headerFormat: '<span style="font-size:12px; font-weight:bold;">{point.key}</span><table>',
					pointFormat: '<tr><td style="padding:0">{series.name}:&nbsp;&nbsp;</td>' +
							'<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
				},
				legend: false,
				plotOptions: {
					column: {
						depth: 25,
						dataLabels: {
							enabled: true
						}
					}
				},
				xAxis: {
					categories: <?php echo $xAxix_comportsd; ?>
				},
				yAxis: {
					title: {
						text: null
					}
				},
				series: [{
					name: 'Solicitudes de Defectación',
					data: <?php echo $serie_comportsd; ?>,
					color: '#32a716'
				}]
			});

		});
	</script>
    
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!--   GRAFICAS   -->	
<script src="../../../../js/highcharts423/js/highcharts.js"></script>
<script src="../../../../js/highcharts423/js/highcharts-3d.js"></script>
<script src="../../../../js/highcharts423/js/modules/canvas-tools.js"></script>

<div class="wrapper">

  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-blue">
            <span class="info-box-icon"><img src="../../../../resources/images/icons/boxinfo_sdpend.png"/></span>
            <div class="info-box-content">
              <span class="info-box-text">SD PENDIENTES</span>
              <span class="info-box-number"><?php echo $ctdad_sdpend; ?></span>
              <!-- The progress section is optional -->
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo number_format($sdpend_porciento,2); ?>%"></div>
              </div>
              <span class="progress-description">
                <?php echo number_format($sdpend_porciento,2); ?>% de SD (<?php echo $ctdad_sdtotal; ?>)
              </span>
            </div><!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon"><img src="../../../../resources/images/icons/boxinfo_hfo.png"/></span>
            <div class="info-box-content">
              <span class="info-box-text">HABIT. FUERA DE ORDEN</span>
              <span class="info-box-number"><?php echo $ctdad_hfo; ?></span>
              <!-- The progress section is optional -->
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo number_format($hfo_porciento,2); ?>%"></div>
              </div>
              <span class="progress-description">
                <?php echo number_format($hfo_porciento,2); ?>% de Habit. Defectadas (<?php echo $ctdad_habit; ?>)
              </span>
            </div><!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><img src="../../../../resources/images/icons/boxinfo_pgasto.png"/></span>
            <div class="info-box-content">
              <span class="info-box-text">EJECUCI&Oacute;N PRESUPUESTO</span>
              <span class="info-box-number"><strong>$</strong> <?php echo number_format($costo_gtia, 2, '.', ' '); ?></span>
              <!-- The progress section is optional -->
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo number_format($presupuesto_porciento,2); ?>%"></div>
              </div>
              <span class="progress-description">
			  	<?php echo number_format($presupuesto_porciento, 2, '.', ' '); ?> % del Presupuesto (<strong>$</strong> <?php echo (number_format($presupuesto, 2, '.', ' ')); ?>)
              </span>
            </div><!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Principales Indicadores</h3>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  <p class="text-center">
                    <strong>COMPORTAMIENTO DE LOS PRINCIPALES INDICADORES POR PROYECTO</strong>
                  </p>

                  <div id="main_chart" style="width: 100%; height: 300px; margin: 0 auto;"></div>
                  
				  <!-- /.chart-responsive -->
                </div>
                <!-- /.col -->
                
				
              </div>
              <!-- /.row -->
            </div>
            <!-- ./box-body -->
            
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    <!-- Main row -->
          
	<div class="row">
	  
	  <div class="col-md-4">
		<!-- COSTO DE GARANTIA -->
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title">Costo de Garant&iacute;a</h3>
				<div class="box-tools pull-right" style="margin-top: 3px">
				  <span data-toggle="tooltip" class="badge bg-yellow">$ <?php echo number_format($total_costo, 2, '.', ' '); ?></span>
				</div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<div class="table-responsive">
				  <table class="table no-margin">
					<thead>
					<tr>
					  <th width="55%">Tipo de SD</th>
					  <th width="20%" style="text-align:center">SD</th>
					  <th width="25%" style="text-align:right">Costo</th>
					</tr>
					</thead>
					<tbody>
					<tr>
					  <td>Constructiva</td>
					  <td align="center"><?php echo $ctdad_construct; ?></td>
					  <td align="right"><strong>$</strong> <?php echo number_format($ctdad_construct_costo, 2, '.', ' '); ?></td>
					</tr>
					<tr>
					  <td>AEH</td>
					  <td align="center"><?php echo $ctdad_aeh; ?></td>
					  <td align="right"><strong>$</strong> <?php echo number_format($ctdad_aeh_costo, 2, '.', ' '); ?></td>
					</tr>
					<tr>
					  <td>Suministro</td>
					  <td align="center"><?php echo $ctdad_sumin; ?></td>
					  <td align="right"><strong>$</strong> <?php echo number_format($ctdad_sumin_costo, 2, '.', ' '); ?></td>
					</tr>
					<tr>
					  <td>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;<i>Importaci&oacute;n</i></td>
					  <td align="center"><?php echo $ctdad_import; ?></td>
					  <td align="right"><strong>$</strong> <?php echo number_format($ctdad_import_costo, 2, '.', ' '); ?></td>
					</tr>
					<tr>
					  <td>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;<i>Compra Local</i></td>
					  <td align="center"><?php echo $ctdad_local; ?></td>
					  <td align="right"><strong>$</strong> <?php echo number_format($ctdad_local_costo, 2, '.', ' '); ?></td>
					</tr>
					<tr>
					  <td>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;<i>Compra Interna</i></td>
					  <td align="center"><?php echo $ctdad_inter; ?></td>
					  <td align="right"><strong>$</strong> <?php echo number_format($ctdad_inter_costo, 2, '.', ' '); ?></td>
					</tr>
					</tbody>
				  </table>
				</div>
				<!-- /.table-responsive -->  
			</div>
			<!-- /.box-body -->
		  
		</div>
		<!--/.costo de garantia -->
	  </div>
	  <!-- /.col -->

	  <div class="col-md-4">
		<!-- USERS LIST -->
		<div class="box box-danger">
			<div class="box-header with-border">
			  <h3 class="box-title">Defectos m&aacute;s Repetitivos</h3>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<div class="table-responsive">
				  <table class="table no-margin">
					<thead>
					<tr>
					  <th width="60%">Defecto</th>
					  <th width="25%" style="text-align:right">Costo</th>
					  <th width="15%" style="text-align:center">Ctdad</th>
					</tr>
					</thead>
					<tbody>
					<?php
							$count_problema = 0;
							$label_style    = '';
							foreach($array_defectos as $problema => $values)
							{
								$count_problema++;
								if($count_problema <= 6){
									if($count_problema <= 3){ $label_style = 'label label-danger'; }
									elseif($count_problema > 3){ $label_style = 'label label-warning'; }
							?>
							<tr>
								<td><?php echo $problema; ?></td>
								<td align="right"><strong>$</strong> <?php echo number_format($values[1], 2, '.', ' '); ?></td>
								<td align="center"><span class="<?php echo $label_style; ?>"><?php echo $values[0]; ?></span></td>
							</tr>
					<?php }} ?>
					</tbody>
				  </table>
				</div>
				<!-- /.table-responsive -->
			</div>
			<!-- /.box-body --> 
		</div>
		<!--/.box -->
		</div>
	  
		<div class="col-md-4">
			  
		  <div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">SD por Proyectos</h3>
				<div class="box-tools pull-right" style="margin-top: 3px">
				  <span data-toggle="tooltip" class="badge bg-blue">Total: <?php echo $ctdad_sdtotal; ?> SD</span>
				</div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
					<div id="sd_chart" style="width: 100%; height: 260px; margin: 0 auto;"></div>
					<!-- ./chart-responsive -->
					</div>
					<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
        
    <!-- /.col -->
    </div>
    <!-- /.row -->
		
	<div class="row">
        <!-- Left col -->
        <div class="col-md-12">
          <!-- MAP & BOX PANE -->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Comportamiento Mensual de las SD Imputables a la AEI</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="row">
                <div class="col-md-9 col-sm-8">
					<div class="pad">
						<div id="monthsd_chart" style="width: 100%; height: 350px; margin: 0 auto;"></div>	
					</div>
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-4">
                  <div class="pad box-pane-right bg-green" style="min-height: 280px">
                    <div class="description-block margin-bottom">
                      <div class="sparkbar pad" data-color="#fff"><img src="../../../../resources/images/icons/statistics-chart.png"/></div>
                      <h5 class="description-header"><?php echo $ctdad_sdfirm; ?></h5>
                      <span class="description-text">Firmadas</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block margin-bottom">
                      <div class="sparkbar pad" data-color="#fff"><img src="../../../../resources/images/icons/statistics-chart.png"/></div>
                      <h5 class="description-header"><?php echo $ctdad_sdpend; ?></h5>
                      <span class="description-text">Pendientes</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block">
                      <div class="sparkbar pad" data-color="#fff"><img src="../../../../resources/images/icons/statistics-chart.png"/></div>
                      <h5 class="description-header"><?php echo $ctdad_sdproc; ?></h5>
                      <span class="description-text">En Proceso</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
		  
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

</body>
</html>
