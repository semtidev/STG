<?php
	// Inicializar la sesion activa
	session_name('semtiGarantiaSession');
	session_start();
	
	// Incluir la clase de conexion
	include_once("../sistema/connect.php");
	$connect = new Connect();
	
	// Llamar la funcion que conecta a la BD
	$connect->connMSSQL_SEMTI();
	
	// Incluir la clase de tratamiento de cadenas
	include_once("../sistema/cadenas.php");
	$cadenas = new Cadenas();
	
	////////////////////////////////////////////////
	/////////      BEGIN pCHART 2.1.4      /////////
	////////////////////////////////////////////////

		// Include all the classes 
		include("../pChart214/class/pDraw.class.php"); 
		include("../pChart214/class/pImage.class.php"); 
		include("../pChart214/class/pData.class.php");
		
		///////////////////////////////////////////////
		///////  OBJETENER DATOS DE LA GRAFICA  ///////
		///////////////////////////////////////////////
		
		// Total de SD
		$qry_total = $adoMSSQL_SEMTI->Execute("SELECT id FROM gtia_sd");
		$total_sd  = $qry_total->RecordCount();
		
		$porciento_noproceden = 0;
		$porciento_poresolver = 0;
		$porciento_reclamadas = 0;
		$porciento_firmadas   = 0;
		
		if($total_sd > 0){
			
			// No Proceden
			$qry_NP = $adoMSSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'No Procede'");
			$tot_NP = $qry_NP->RecordCount();
			if($tot_NP > 0) $porciento_noproceden = number_format(($tot_NP / $total_sd) * 100,2);
			
			// Por Resolver
			$qry_PR = $adoMSSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'Por Resolver'");
			$tot_PR = $qry_PR->RecordCount();
			if($tot_PR > 0) $porciento_poresolver = number_format(($tot_PR / $total_sd) * 100,2);
			
			// Reclamadas
			$qry_R = $adoMSSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'Reclamada'");
			$tot_R = $qry_R->RecordCount();
			if($tot_R > 0) $porciento_reclamadas = number_format(($tot_R / $total_sd) * 100,2);
			
			// Firmadas
			$qry_F = $adoMSSQL_SEMTI->Execute("SELECT id FROM gtia_sd WHERE estado = 'Firmada'");
			$tot_F = $qry_F->RecordCount();
			if($tot_F > 0) $porciento_firmadas = number_format(($tot_F / $total_sd) * 100,2);
		}
		////////////////////////////////////////////////////
		////////////////////////////////////////////////////
		
		
		/* Create and populate the pData object */
		$MyData = new pData();  
		$MyData->addPoints(array($porciento_noproceden,$porciento_poresolver,$porciento_reclamadas,$porciento_firmadas),"Porciento");
		$MyData->setAxisName(0,"% del Total de SD");
		$MyData->addPoints(array("NoProcede","PorResolver","Reclamadas","Firmadas"),"Months");
		$MyData->setSerieDescription("Months","Month");
		$MyData->setAbscissa("Months");
		
		/* Create the pChart object */
		$myPicture = new pImage(600,300,$MyData);
		
		/* Turn of Antialiasing */
		$myPicture->Antialias = FALSE;
		
		/* Add a border to the picture */
		$myPicture->drawRectangle(0,0,599,299,array("R"=>0,"G"=>0,"B"=>0));
		
		/* Set the default font */
		$myPicture->setFontProperties(array("FontName"=>"../pChart214/fonts/verdana.ttf","FontSize"=>10));
		
		/* Define the chart area */
		$myPicture->setGraphArea(60,40,550,270);
		
		/* Draw the scale */
		$scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
		$myPicture->drawScale($scaleSettings);
		
		/* Write the chart legend */
		//$myPicture->drawLegend(500,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
		
		/* Turn on shadow computing */ 
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		
		/* Draw the chart */
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$settings = array("Gradient"=>TRUE,"GradientMode"=>GRADIENT_EFFECT_CAN,"DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255,"DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
		$settings2 = array("DisplayValues"=>TRUE);
		$myPicture->drawBarChart($settings2);
		
		// Crear la imagen que será renderizada
		if(copy('../../resources/images/rendercharts/rendertemplate.png','../../resources/images/rendercharts/chartsdpendientes.png')){
		
		 // Render the picture (choose the best way)
		 //$MyPicture->Stroke();
		 //$myPicture->autoOutput("../pChart214/examples/pictures/example.basic.png");
		 //header("Content-Type: image/png");
		 $myPicture->Render("../../resources/images/rendercharts/chartsdpendientes.png");
		
		}
		
	//////////////////////////////////////////////
	/////////      END pCHART 2.1.4      /////////
	//////////////////////////////////////////////
	
	
	////////////////////////////////////////////
	/////////       BEGIN MPDF 6       /////////
	////////////////////////////////////////////
	
		// Capturar la fecha del reporte
		$fecha = date('d/m/Y');
		$hoybd = date('Y-m-d');
		
		// Incluir libreria PDF
		include("../MPDF6/mpdf.php");
		$mpdf = new mPDF('utf-8', 'LETTER-L','','',6,6,35,25,5,3,'L'); 
		
		// Cargar CSS
		$stylesheet = file_get_contents('../MPDF6/examples/mpdfstyletables.css');
		$mpdf->WriteHTML($stylesheet,1);
		
		
		////////////////////////////////////////////////
		//// ENCABEZADO Y PIE DE LA PAGINA /////////////
		////////////////////////////////////////////////
		
		$mesCpl = $m_ano[intval(date('m', $fechaComparacionA))]; // date('M', $fechaComparacionA);
		$anoCPL = date('Y', $fechaComparacionA);
		//$mesCpl = date('M', $fechaComparacionA);
		
		$header = '
		<table align="center" width="100%" style="border-bottom: 1px solid #000000;" cellspacing="0" cellpadding="0">
		 <tr height="150">
				<td width="120"><img src="../../resources/images/logo/UCM.png"></td>
				<td align="center"><font class="asociacion">Asociación Econ&oacute;mica Internacional</font><br><font class="ucm-bouygues">UCM - BOUYGUES</font></td>
				<td width="80"><img src="../../resources/images/logo/bouygues.png"></td>
		 </tr>
		</table>
		';
		
		$footer = '
		<br>&nbsp;
		<table align="center" width="100%" style="border-top: 1px solid #000000;" cellspacing="0" cellpadding="0">
		 <tr><td colspan="2">&nbsp;</td></tr>
		 <tr>
			<td class="pie-title">REPORTE CCO GARANT&Iacute;A</td>
			<td align="right" valign="middle">P&aacute;gina: {PAGENO}</td>
		 </tr>
		 <tr>
		   <td colspan="2">Generado por:&nbsp;'.$cadenas->utf8($_SESSION['nombre'].'&nbsp;'.$_SESSION['apellidos']).'</td>
		 </tr>
		 <tr><td colspan="2">&nbsp;</td></tr>
		</table>
		
		';
		
		$footerE = '
		<br>&nbsp;
		<table align="center" width="100%" style="border-top: 1px solid #000000;" cellspacing="0" cellpadding="0">
		 <tr><td colspan="2">&nbsp;</td></tr>
		 <tr>
			<td class="pie-title">REPORTE CCO GARANT&Iacute;A</td>
			<td align="right" valign="middle">P&aacute;gina: {PAGENO}</td>
		 </tr>
		 <tr>
		   <td colspan="2">Generado por:&nbsp;'.$cadenas->utf8($_SESSION['nombre'].'&nbsp;'.$_SESSION['apellidos']).'</td>
		 </tr>
		 <tr><td colspan="2">&nbsp;</td></tr>
		</table>
		';
		
		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);
		$mpdf->SetHTMLFooter($footerE,'E');
		
		////////////////////////////////////////////////
		////   FIN ENCABEZADO Y PIE DE LA PAGINA    ////
		////////////////////////////////////////////////
		
		
		////////////////////////////////////////////////
		//////////////      LISTADO      ///////////////
		////////////////////////////////////////////////
		
		// Crear la Tabla del reporte			
		$html = '
		<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
		  <tr>
			<td class="title_document">Solicitudes de Defectaci&oacute;n Pendientes por Resolver</td><td width="150" class="fecha_document"><strong>Fecha:</strong> '.$fecha.'</td>
		  </tr>
		  <tr height="40">
		  	<td colspan="2" style="font-size:20px">&nbsp;</td>
		  </tr>
		  <tr>
		  	<td colspan="2" align="center">
			<img src="../../resources/images/rendercharts/chartsdpendientes.png" border="0" style="margin-bottom:10px">
			</td>
		  </tr>
		  <tr>
		  	<td colspan="2" style="font-size:11px" align="center"><strong>Gr&aacute;fica de Comportamiento seg&uacute;n el Total de SD</strong></td>
		  </tr>
		  <tr height="10">
		  	<td colspan="2" style="font-size:10px">&nbsp;</td>
		  </tr>
		</table>';
		
		$mpdf->WriteHTML($html,2);
		
		// Insertar una nueva pagina al PDF
		//$mpdf->AddPage();

		$html = '
		<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="40" class="enc_tabla" align="center" style="border-right:#ffffff 1px solid">No</td>
			<td class="enc_tabla" style="border-right:#ffffff 1px solid">Descripci&oacute;n</td>
			<td width="90" class="enc_tabla" style="border-right:#ffffff 1px solid">Proyecto</td>
			<td width="90" class="enc_tabla" style="border-right:#ffffff 1px solid">Objeto</td>
			<td width="80" class="enc_tabla" style="border-right:#ffffff 1px solid">Locaci&oacute;n</td>
			<td width="75" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Dpto</td>
			<td width="70" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Fecha Reporte</td>
			<td width="55" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Demora (D&iacute;as)</td>
			<td width="60" class="enc_tabla" style="text-align:right; border-right:#ffffff 1px solid">Presup.</td>
			<td width="50" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Estado</td>
			<td width="40" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Con.</td>
			<td width="40" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Sum.</td>
			<td width="40" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">AEH?</td>
			<td width="80" class="enc_tabla" style="text-align:left">Comentario</td>
		  </tr>';
		$mpdf->WriteHTML($html,2);
		
		
		////////////////////////////////////////////////////////
		///////    CONTRUIR EL CONTENIDO DE LA TABLA     ///////
		////////////////////////////////////////////////////////
		
		
		$sql = "SELECT gtia_sd.descripcion AS descripcion,gtia_sd.parte AS parte,gtia_sd.locacion AS locacion,gtia_sd.id_dpto AS id_dpto,gtia_sd.fecha_reporte AS fecha_reporte,gtia_sd.fecha_solucion AS fecha_solucion,gtia_sd.presupuesto AS presupuesto,gtia_sd.estado AS estado,gtia_sd.constructiva AS constructiva,gtia_sd.suministro AS suministro,gtia_sd.afecta_explotacion AS afecta_explotacion,gtia_sd.comentario AS comentario,cco_objetos.nombre AS objeto,cco_zonas.nombre AS zona,cco_proyectos.nombre AS proyecto FROM gtia_sd,cco_objetos,cco_zonas,cco_proyectos WHERE gtia_sd.estado = 'PR' AND gtia_sd.id_objeto = cco_objetos.id AND cco_objetos.id_zona = cco_zonas.id AND cco_zonas.id_proyecto = cco_proyectos.id ORDER BY gtia_sd.descripcion ASC";
		
		$qry = $adoMYSQL_SEMTI->Execute($sql);
		
		if($qry->RecordCount() >= 1){ 
		
			while($result = $qry->FetchRow()){
						
				if($result['constructiva'] == 1) $constructiva = 'Si'; else $constructiva = 'No';
				if($result['suministro'] == 1) $suministro = 'Si'; else $suministro = 'No';
				if($result['afecta_explotacion'] == 1) $afecta_explotacion = 'Si'; else $afecta_explotacion = 'No';
				
				// DPTO
				$qry_dpto  = $adoMYSQL_SEMTI->Execute("SELECT nombre FROM cco_dptos WHERE id = ".$result['id_dpto']);
				$res_dpto  = $qry_dpto->FetchRow();
				$name_dpto = $res_dpto['nombre'];
				
				// FECHA
				$arr_fecha_reporte  = explode('-',$result['fecha_reporte']);
				$fecha_reporte      = $arr_fecha_reporte[2].'/'.$arr_fecha_reporte[1].'/'.$arr_fecha_reporte[0];
				
				// CALCULAR DEMORA
				$timestamp_Reporte  = mktime(0,0,0,$arr_fecha_reporte[1],$arr_fecha_reporte[2],$arr_fecha_reporte[0]);
				if(count($arr_fecha_solucion) != 3){
					$arr_fecha_solucion = explode('-',date('Y-m-d'));
				}
				$timestamp_Solucion = mktime(0,0,0,$arr_fecha_solucion[1],$arr_fecha_solucion[2],$arr_fecha_solucion[0]);
				$segundos_demora    = $timestamp_Solucion - $timestamp_Reporte;
				$calculo_demora     = $segundos_demora / (60 * 60 * 24);
				
				$No++;
				
				// Construir variables Proyecto y Objeto
				$proyecto = $result['proyecto'].', Zona '.$result['zona'];
				if($result['parte'] == '') $objeto = $result['objeto']; else $objeto = $result['objeto'].', '.$result['parte'];
				
				if($No == 1) $colorline = '606163'; else $colorline = 'c0c1c1';
				
				$html = '  
				  <tr>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$No.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($result['descripcion']).'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($proyecto).'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($objeto).'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($result['locacion']).'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.utf8_encode($name_dpto).'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($fecha_reporte).'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$calculo_demora.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="right">'.$result['presupuesto'].'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$result['estado'].'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$constructiva.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$suministro.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$afecta_explotacion.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid;">'.utf8_encode($result['comentario']).'</td>
				  </tr>
				';
				$mpdf->WriteHTML($html,2);
			} 
		}
		
		//////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////
		
		$html = '
		  <tr>
			<td style="border-top:#606163 1px solid;" colspan="14"></td>
		  </tr>
		</table><br>
		';
		$mpdf->WriteHTML($html,2);
				
		/////////////////////////////////////////////
		///////////      FIN LISTADO      ///////////
		/////////////////////////////////////////////
		
		
		$mpdf->Output('SD Pendientes por Resolver '.$fecha.'.pdf','I');
		exit;
	
	////////////////////////////////////////////
	/////////        END MPDF 6        /////////
	////////////////////////////////////////////
?>