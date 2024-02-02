<?php
	// Incluir la clase de conexion
	include_once("../sistema/connect.php");
	$connect = new Connect();
	
	// Llamar la funcion que conecta a la BD
	$connect->connMYSQL_SEMTI();
	
	// Incluir la clase de tratamiento de cadenas
	include_once("../sistema/cadenas.php");
	$cadenas = new Cadenas();
		
	// Total de SD
	$qry_total = $adoMYSQL_SEMTI->Execute("SELECT id FROM gtia_sd");
	$total_sd  = $qry_total->RecordCount();
	
	$porciento_noproceden = 0;
	$porciento_poresolver = 0;
	$porciento_reclamadas = 0;
	$porciento_firmadas   = 0;
	
	if($total_sd > 0){
		
		// No Proceden
		$qry_NP = $adoMYSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'NP'");
		$tot_NP = $qry_NP->RecordCount();
		if($tot_NP > 0) $porciento_noproceden = ($tot_NP / $total_sd) * 100;
		
		// Por Resolver
		$qry_PR = $adoMYSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'PR'");
		$tot_PR = $qry_PR->RecordCount();
		if($tot_PR > 0) $porciento_poresolver = ($tot_PR / $total_sd) * 100;
		
		// Reclamadas
		$qry_R = $adoMYSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'R'");
		$tot_R = $qry_R->RecordCount();
		if($tot_R > 0) $porciento_reclamadas = ($tot_R / $total_sd) * 100;
		
		// Firmadas
		$qry_F = $adoMYSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'F'");
		$tot_F = $qry_F->RecordCount();
		if($tot_F > 0) $porciento_firmadas = ($tot_F / $total_sd) * 100;
	}
	
	// Construir el JSON
	echo '{"success": true, "chartsdpendientes": {PorResolver:'.number_format($porciento_poresolver,2).', NoProceden:'.number_format($porciento_noproceden,2).', Reclamadas:'.number_format($porciento_reclamadas,2).', Firmadas:'.number_format($porciento_firmadas,2).', indicador:"Estados"}}';
		
?>