<?php

header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

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

// Capturar la fecha del reporte
$fecha = date('d/m/Y');

// Recibir parametros del informe
$id_informe    = $_POST['id'];
$exist_estados = false;

// Obtener los Parametros Generales
$polo = ($_SESSION['polo_name'] != 'Todos') ? $_SESSION['polo_name'] : 'BBI SUCURSAL HABABA, CUBA';

// Crear grafica de estados

if(isset($_POST['estadoStore']) && $_POST['estadoStore'] != ''){
	
	$exist_estados = true;
	$estadosData = $_POST['estadoStore'];
	$records = json_decode(stripslashes($estadosData));
	foreach ($records as $record) {
	
		$indicador  = $cadenas->codificarBD_utf8($record->indicador);
		$noproceden = $record->noproceden;
		$poresolver = $record->poresolver;
		$reclamadas = $record->reclamadas;
		$firmadas   = $record->firmadas;
		$enproceso  = $record->enproceso;
		$total      = $record->total;
	}
	
	////////////////////////////////////////////////
	/////////      BEGIN pCHART 2.1.4      /////////
	////////////////////////////////////////////////
		
		// Include all the classes 
		include("../pChart214/class/pDraw.class.php"); 
		include("../pChart214/class/pImage.class.php"); 
		include("../pChart214/class/pData.class.php");
		include("../pChart214/class/pPie.class.php");
		
		///////////////////////////////////////////////
		///////  OBJETENER DATOS DE LA GRAFICA  ///////
		///////////////////////////////////////////////
		
		// Total de SD
		
		$porciento_noproceden = 0;
		$porciento_poresolver = 0;
		$porciento_reclamadas = 0;
		$porciento_firmadas   = 0;
		$porciento_enproceso  = 0;
		
		if($total > 0){
			
			// No Proceden
			if($noproceden > 0) $porciento_noproceden = number_format(($noproceden / $total) * 100,2);
			
			// Por Resolver
			if($poresolver > 0) $porciento_poresolver = number_format(($poresolver / $total) * 100,2);
			
			// Reclamadas
			if($reclamadas > 0) $porciento_reclamadas = number_format(($reclamadas / $total) * 100,2);
			
			// Firmadas
			if($firmadas > 0) $porciento_firmadas = number_format(($firmadas / $total) * 100,2);

			// En Proceso
			if($enproceso > 0) $porciento_enproceso = number_format(($enproceso / $total) * 100,2);
		}
		////////////////////////////////////////////////////
		////////////////////////////////////////////////////
		
		
		/* Create and populate the pData object */
		$MyData = new pData();   
		$MyData->addPoints(array($porciento_noproceden, $porciento_poresolver, $porciento_reclamadas, $porciento_firmadas, $porciento_enproceso),"ScoreA");
		$MyData->setSerieDescription("ScoreA","Application A");
	   
		/* Define the absissa serie */
		$MyData->addPoints(array("No Procede (".$porciento_noproceden." %)","Por Resolver (".$porciento_poresolver." %)","Reclamadas (".$porciento_reclamadas." %)","Firmadas (".$porciento_firmadas." %)","En Proceso (".$porciento_enproceso." %)"),"Labels");
		$MyData->setAbscissa("Labels");
	   
		/* Create the pChart object */
		$myPicture = new pImage(800,300,$MyData,TRUE);
		
		/* Add a border to the picture */
		//$myPicture->drawRectangle(0,0,799,299,array("R"=>115,"G"=>117,"B"=>118));
	   
		/* Set the default font properties */ 
		$myPicture->setFontProperties(array("FontName"=>"../pChart214/fonts/verdana.ttf","FontSize"=>9,"R"=>0,"G"=>0,"B"=>0));
		   
		/* Create the pPie object */ 
		$PieChart = new pPie($myPicture,$MyData);
		
		/* Define the slice color */
		$PieChart->setSliceColor(0,array("R"=>229,"G"=>16,"B"=>24));
		$PieChart->setSliceColor(1,array("R"=>98,"G"=>121,"B"=>152));
		$PieChart->setSliceColor(2,array("R"=>251,"G"=>115,"B"=>22));
		$PieChart->setSliceColor(3,array("R"=>68,"G"=>177,"B"=>34));
		$PieChart->setSliceColor(4,array("R"=>89,"G"=>164,"B"=>241));
	   
		/* Enable shadow computing */ 
		$myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>15));
	   
		/* Draw a splitted pie chart */ 
		$PieChart->draw3DPie(400,150,array("Radius"=>180,"DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"DataGapAngle"=>5,"DataGapRadius"=>10,"Border"=>TRUE));
	   
		/* Write the legend box */ 
		$myPicture->setFontProperties(array("FontName"=>"../pChart214/fonts/verdana.ttf","FontSize"=>9,"R"=>0,"G"=>0,"B"=>0));
		$PieChart->drawPieLegend(50,280,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
	   
		/* Render the picture (choose the best way) */
		//$myPicture->autoOutput("../pChart214/examples/pictures/example.draw3DPie.transparent.png");
		if(copy('../../resources/images/rendercharts/rendertemplate.png','../../resources/images/rendercharts/chartsdestados.png')){
			
		// Render the picture (choose the best way)
		//$MyPicture->Stroke();
		//$myPicture->autoOutput("../pChart214/examples/pictures/example.basic.png");
		//header("Content-Type: image/png");
		$myPicture->Render("../../resources/images/rendercharts/chartsdestados.png");
	   
	   }
		
	//////////////////////////////////////////////
	/////////      END pCHART 2.1.4      /////////
	//////////////////////////////////////////////
}

////////////////////////////////////////////
/////////       BEGIN MPDF 6       /////////
////////////////////////////////////////////

    // Incluir libreria PDF
    include("../MPDF6/mpdf.php");
    $mpdf = new mPDF('utf-8', 'LETTER-L', '', '', 6, 6, 32, 25, 5, 1, 'L');
    
    // Cargar CSS
    $stylesheet = file_get_contents('../MPDF6/examples/mpdfstyletables.css');
    $mpdf->WriteHTML($stylesheet, 1);
    
    // Obtener los datos iniciales del informe
    $sql_infoCodir = "SELECT
                        info_resumen.titulo,
                        info_resumen.proyecto,
                        info_resumen.zona,
                        info_resumen.desde,
                        info_resumen.hasta,
                        info_resumen.comentario_inicial,
                        info_resumen.comentario_final,
                        gtia_proyectos.imagen
                    FROM
                        info_resumen,
                        gtia_proyectos
                    WHERE
                        info_resumen.id = $id_informe AND
                        info_resumen.proyecto = gtia_proyectos.nombre";
    
    $qry_infoCodir  = $adoMSSQL_SEMTI->Execute($sql_infoCodir);
    
    $titulo         = $cadenas->utf8($qry_infoCodir->fields[0]);
    $proyecto       = $cadenas->utf8($qry_infoCodir->fields[1]);
    $zona           = $qry_infoCodir->fields[2];
    $desde          = $qry_infoCodir->fields[3];
    $hasta          = $qry_infoCodir->fields[4];
    $coment_inicial = $cadenas->utf8($qry_infoCodir->fields[5]);
    $coment_final   = $cadenas->utf8($qry_infoCodir->fields[6]);
    $imagen         = $qry_infoCodir->fields[7];
    
    if($desde == '1900-01-01'){ $desde = 'Inicio de la Garantía'; }
	if($hasta == '1900-01-01'){ $hasta = date('d-m-Y'); }
	
    //// ENCABEZADO Y PIE DE LA PAGINA 
    ///////////////////////////////////////////////////////////////////////////
        
    $header = '
               <table align="center" width="100%" style="border-bottom: 1px solid #D9DCE1;" cellspacing="0" cellpadding="0">
                <tr height="150">
					<td width="120"><img src="../../resources/images/logo/UCM.png"></td>
					<td align="center"><font class="asociacion">Asociación Econ&oacute;mica Internacional</font><br><font class="ucm-bouygues">UCM - BBI</font><br><font class="polo">'.strtoupper($polo).'</font></td>
					<td width="80"><img src="../../resources/images/logo/bouygues.png"></td>
                </tr>
               </table>
              ';
    
    $footer = '
               <br>&nbsp;
               <table align="center" width="100%" style="border-top: 1px solid #D9DCE1;" cellspacing="0" cellpadding="0">
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td class="pie-title" valign="top">Resumen de Garant&iacute;a | ' . $titulo . '</td>
						<td width="130" align="right" valign="top">P&aacute;gina: {PAGENO}&nbsp;</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td colspan="2" class="pie-content">
							Elaborado por (Nombre):&nbsp;______________________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cargo:&nbsp;_______________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Firma: ______________________
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
               </table>
              ';
    
    $footerE = '
                <br>&nbsp;
                <table align="center" width="100%" style="border-top: 1px solid #D9DCE1;" cellspacing="0" cellpadding="0">
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td class="pie-title" valign="top">Resumen de Garant&iacute;a | ' . $titulo . '</td>
						<td width="130" align="right" valign="top">P&aacute;gina: {PAGENO}&nbsp;</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td colspan="2" class="pie-content">
							Elaborado por (Nombre):&nbsp;______________________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cargo:&nbsp;_______________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Firma: ______________________
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
                </table>
               ';
    
    
    $mpdf->SetHTMLHeader($header);
    $mpdf->SetHTMLFooter($footer);
    $mpdf->SetHTMLFooter($footerE,'E');
    
    ////   FIN ENCABEZADO Y PIE DE LA PAGINA 
    
    // Primera Pagina
    $html = '
            <div class="imgbg" style="background-image: url(../../resources/images/proyectos/'.$imagen.');"></div>
            <div class="watermark_top">
                <table width="95%" height="80" align="left" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="120" valign="middle" align="right"><img src="../../resources/images/logo/UCM.png"/></td>
						<td align="center"><font class="asociacion">Asociación Econ&oacute;mica Internacional</font><br><font class="ucm-bouygues">UCM - BBI</font><br><font class="polo">'.strtoupper($polo).'</font></td>
						<td width="90" valign="middle"><img src="../../resources/images/logo/bouygues.png" style="margin-top: 5px;"/></td>
					</tr>
                </table>
            </div>
            <div class="watermark_bottom">
                <table width="90%" height="80" align="right" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td valign="middle" colspan="3" align="right"><div id="titulo">'.$titulo.'</div></td>
                    </tr>
                    <tr>
                        <td valign="middle" colspan="3" align="right">&nbsp;</td>
                    </tr>
                    <tr height="40">
                        <td align="right" class="fecha_document" style="border-right: #aaacad 1px solid; padding-right:20px;"><strong>Proyecto:</strong> ' . $proyecto . '</td>
                        <td width="150" align="center" class="fecha_document" style="border-right: #aaacad 1px solid;"><strong>Zonas:</strong> ' . $zona . '</td>
                        <td width="140" valign="middle" align="right" class="fecha_document"><strong>Fecha:</strong> ' . $fecha . '</td>
                    </tr>
                </table>
            </div>
            ';
    $mpdf->WriteHTML($html, 2);
    $mpdf->AddPage();
    
    /////////////////////////////////////////////////
    /////      INFORME RESUMEN DE GARANTIA      /////     
    /////////////////////////////////////////////////
    
    // Crear la Tabla Encabezado del reporte			
    $html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="title_document">' . $titulo . '</td>
                    <td width="200"><strong>DESDE:</strong> ' .$desde. '</td>
                    <td width="150"><strong>HASTA:</strong> ' .$hasta. '</td>
                </tr>
            </table>
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr height="30">
                    <td width="80" class="title_content">Proyecto:</td>
                    <td class="title_content"> ' . $proyecto . '</td>
                </tr>
                <tr height="30">
                    <td class="title_content">Zona(s):</td>
                    <td class="title_content"> ' . $zona . '</td>
                </tr>
            </table><br>';
    $mpdf->WriteHTML($html, 2);
    
    // Comentario inicial del informe			
    if($coment_inicial != ''){
        $html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>' .str_replace('\x0A','<br>',$coment_inicial). '</td>
                    </tr>
                </table>';
        $mpdf->WriteHTML($html, 2);
		$mpdf->AddPage();
    }
    
    /////////////////////////////////////////////////
    ///////           TABLA ESTADOS           ///////
    /////////////////////////////////////////////////
	if($exist_estados == true){
		
		$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="7" class="title_section">COMPORTAMIENTO DEL ESTADO DE LAS SD</td>
					</tr>
					 <tr>
						<td colspan="7">&nbsp;</td>
					</tr>
					<tr>
						<td class="enc_tabla" style="border-right:#ffffff 1px solid">INDICADOR</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">EN PROCESO</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">FIRMADAS</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">POR RESOLVER</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">RECLAMADAS</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">NO PROCEDEN</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">TOTAL</td>
					</tr>
					<tr>
						<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$indicador.'</td>
						<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$enproceso.'</td>
						<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$firmadas.'</td>
						<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$poresolver.'</td>
						<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$reclamadas.'</td>
						<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$noproceden.'</td>
						<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$total.'</td>
					</tr>
					<tr>
						<td style="border-top:#606163 1px solid;" colspan="7"></td>
					</tr>
				</table>';
		$mpdf->WriteHTML($html, 2);
		
		// Gráfica de comportamiento de los estados		
		$html = '<p><table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
				  <tr>
					<td colspan="2" align="center">
					<img src="../../resources/images/rendercharts/chartsdestados.png" border="0">
					</td>
				  </tr>
				  <tr>
					<td colspan="2" style="font-size:11px" align="center"><strong>Gr&aacute;fica No 1. Comportamiento del Estado de las SD respecto al Total</strong></td>
				  </tr>
				  <tr height="10">
					<td colspan="2" style="font-size:10px">&nbsp;</td>
				  </tr>
				</table></p>';    
		$mpdf->WriteHTML($html,2);
		$mpdf->AddPage();
	}
    
    ///////////////////////////////////////////////////
    ///////////      FIN TABLA ESTADOS      ///////////
    ///////////////////////////////////////////////////
	
	
	///////////////////////////////////////////////////////
    ///////           TABLA SD PENDIENTES           ///////
    ///////////////////////////////////////////////////////
	if(isset($_POST['sdpendStore']) && $_POST['sdpendStore'] != ''){
		
		$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="6" class="title_section">SD PENDIENTES POR RESOLVER</td>
					</tr>
					 <tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<tr>
						<td class="enc_tabla" style="border-right:#ffffff 1px solid">DESCRIPCI&Oacute;N</td>
						<td width="70" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">ZONA</td>
						<td width="200" class="enc_tabla" style="border-right:#ffffff 1px solid">OBJETOS</td>
						<td width="150" class="enc_tabla" style="border-right:#ffffff 1px solid">LOCALES</td>
						<td width="150" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">DEPARTAMENTOS</td>
						<td width="150" class="enc_tabla" style="text-align: justify; border-right:#ffffff 1px solid">COMENTARIO</td>
					</tr>';
		$mpdf->WriteHTML($html, 2);
		
		$sdpendData   = $_POST['sdpendStore'];
		$records      = json_decode(stripslashes($sdpendData));
		$count_sdpend = 0;
		$defecto      = '';
		
		foreach ($records as $record) {
		
			$count_sdpend++;
			$problema_sd = $cadenas->codificarBD_utf8($record->problema_sd);
			$descripcion = $cadenas->codificarBD_utf8($record->descripcion);
			$zonas       = $record->zonas;
			$objetos     = $cadenas->codificarBD_utf8($record->objetos);
			$locales     = $cadenas->codificarBD_utf8($record->locales);
			$dpto        = $cadenas->codificarBD_utf8($record->dpto);
			$comentario  = $cadenas->codificarBD_utf8($record->comentario);
				
			if($count_sdpend == 1 || $defecto != $problema_sd){
				$defecto = $problema_sd;						
				$html = '<tr>
							<td colspan="6" class="contenido" style="background: #5f6163; color: #ffffff; border-top:#606163 1px solid; border-right:#ffffff 1px solid; vertical-align: middle;"><strong>'.$defecto.'</strong></td>
						 </tr>
						 <tr>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$descripcion.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$zonas.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$objetos.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$locales.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$dpto.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#ffffff 1px solid; vertical-align: middle;">'.$comentario.'</td>
						 </tr>';
				$mpdf->WriteHTML($html, 2);
			}
			else{
				$html = '<tr>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$descripcion.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$zonas.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$objetos.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$locales.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$dpto.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#ffffff 1px solid; vertical-align: middle;">'.$comentario.'</td>
						 </tr>';
				$mpdf->WriteHTML($html, 2);
			}
		}
		
		$html = '<tr>
					<td style="border-top:#606163 1px solid;" colspan="6"></td>
				</tr>
				</table>';
		$mpdf->WriteHTML($html, 2);
		$mpdf->AddPage();
	}
    
    /////////////////////////////////////////////////////////
    ///////////      FIN TABLA SD PENDIENTES      ///////////
    /////////////////////////////////////////////////////////
	
	
	/////////////////////////////////////////////////////////////////
    ///////           TABLA INDICADORES PRINCIPALES           ///////
    /////////////////////////////////////////////////////////////////
	if(isset($_POST['pindicStore']) && $_POST['pindicStore'] != ''){
		
		$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="8" class="title_section">PRINCIPALES INDICADORES</td>
					</tr>
					 <tr>
						<td colspan="8">&nbsp;</td>
					</tr>
					<tr>
						<td class="enc_tabla" style="border-right:#ffffff 1px solid">INDICADOR</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">PERIODO ANT.</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">PERIODO ACT.</td>
						<td width="80" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">ACUMULADO</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">META</td>
						<td width="70" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">ESTADO</td>
						<td width="80" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">TENDENCIA</td>
						<td width="200" class="enc_tabla" style="border-right:#ffffff 1px solid">ACCIONES</td>
					</tr>';
		$mpdf->WriteHTML($html, 2);
		
		$pindicData   = $_POST['pindicStore'];
		$records      = json_decode(stripslashes($pindicData));
		$count_pindic = 0;
		
		foreach ($records as $record) {
		
			$count_pindic++;
			$indicador   = $cadenas->codificarBD_utf8($record->indicador);
			$periodo_ant = $record->periodo_ant;
			$periodo_act = $record->periodo_act;
			$acumulado   = $record->acumulado;
			$meta        = $record->meta;
			$estado      = $record->estado;
			$tendencia   = $record->tendencia;
			$acciones    = $cadenas->codificarBD_utf8($record->acciones);
			
			// Definir iconos de estado y tendencia
			if(strlen($estado) > 0 && $estado == 'Bien'){
				$icon_estado = '<img src="../../resources/images/icons/HansUp.png" border="0">';
				// Definir icono del estado
				if(strlen($tendencia) > 0 && $tendencia == 'asc'){
					$icon_tendencia = '<img src="../../resources/images/icons/green_asc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'desc'){
					$icon_tendencia = '<img src="../../resources/images/icons/green_desc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'const'){
					$icon_tendencia = '<img src="../../resources/images/icons/green_const.png" border="0">';
				}
			}
			elseif(strlen($estado) > 0 && $estado == 'Mal'){
				$icon_estado = '<img src="../../resources/images/icons/HansDown.png" border="0">';
				// Definir icono del estado
				if(strlen($tendencia) > 0 && $tendencia == 'asc'){
					$icon_tendencia = '<img src="../../resources/images/icons/red_asc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'desc'){
					$icon_tendencia = '<img src="../../resources/images/icons/red_desc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'const'){
					$icon_tendencia = '<img src="../../resources/images/icons/red_const.png" border="0">';
				}
			}
			else{ $icon_estado = ''; $icon_tendencia = ''; }
			
			if($count_pindic == 1) $colorline = '606163'; else $colorline = 'c0c1c1';
						
			////////////////////////////////////////////////////
						
			$html = '<tr>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$indicador.'</td>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$periodo_ant.'</td>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$periodo_act.'</td>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$acumulado.'</td>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$meta.'</td>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$icon_estado.'</td>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$icon_tendencia.'</td>
						<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#ffffff 1px solid; vertical-align: middle;">'.$acciones.'</td>
					 </tr>';
			$mpdf->WriteHTML($html, 2);
			
		}
		
		$html = '<tr>
					<td style="border-top:#606163 1px solid;" colspan="8"></td>
				</tr>
				</table>';
		$mpdf->WriteHTML($html, 2);
		$mpdf->AddPage();
	}
    
    //////////////////////////////////////////////////////////////////
    ///////////      FIN TABLA INDICADORES PRINCIPALES     ///////////
    //////////////////////////////////////////////////////////////////
	
	
	///////////////////////////////////////////////////////
    ///////           TABLA REPETITIVIDAD           ///////
    ///////////////////////////////////////////////////////
	if(isset($_POST['repetStore']) && $_POST['repetStore'] != ''){
		
		$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="3" class="title_section">PROBLEMAS M&Aacute;S REPETITIVOS</td>
					</tr>
					 <tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="enc_tabla" style="border-right:#ffffff 1px solid">DESCRIPCI&Oacute;N</td>
						<td width="70" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">CTDAD</td>
						<td width="500" class="enc_tabla" style="text-align: justify; border-right:#ffffff 1px solid">COMENTARIO</td>
					</tr>';
		$mpdf->WriteHTML($html, 2);
		
		$repetData   = $_POST['repetStore'];
		$records      = json_decode(stripslashes($repetData));
		$count_repet= 0;
		$defecto      = '';
		
		foreach ($records as $record) {
		
			$count_repet++;
			$problema_descripcion = $cadenas->codificarBD_utf8($record->problema_descripcion);
			$sd_descripcion       = $cadenas->codificarBD_utf8($record->sd_descripcion);
			$sd_ctdad             = $record->sd_ctdad;
			$comentario           = $cadenas->codificarBD_utf8($record->comentario);
				
			if($count_repet == 1 || $defecto != $problema_descripcion){
				$defecto = $problema_descripcion;						
				$html = '<tr>
							<td colspan="3" class="contenido" style="background: #5f6163; color: #ffffff; border-top:#606163 1px solid; border-right:#ffffff 1px solid; vertical-align: middle;"><strong>'.$defecto.'</strong></td>
						 </tr>
						 <tr>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$sd_descripcion.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$sd_ctdad.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#ffffff 1px solid; vertical-align: middle;">'.$comentario.'</td>
						 </tr>';
				$mpdf->WriteHTML($html, 2);
			}
			else{
				$html = '<tr>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$sd_descripcion.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$sd_ctdad.'</td>
							<td class="contenido" style="border-top:#606163 1px solid; border-right:#ffffff 1px solid; vertical-align: middle;">'.$comentario.'</td>
						 </tr>';
				$mpdf->WriteHTML($html, 2);
			}
		}
		
		$html = '<tr>
					<td style="border-top:#606163 1px solid;" colspan="3"></td>
				</tr>
				</table>';
		$mpdf->WriteHTML($html, 2);
		$mpdf->AddPage();
	}
    
    /////////////////////////////////////////////////////////
    ///////////      FIN TABLA REPETITIVIDAD      ///////////
    /////////////////////////////////////////////////////////
	
	
	/////////////////////////////////////////////
    ///////           TABLA HFO           ///////
    /////////////////////////////////////////////
	if(isset($_POST['hfoStore']) && $_POST['hfoStore'] != ''){
		
		$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="6" class="title_section">HABITACIONES FUERA DE ORDEN</td>
					</tr>
					 <tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<tr>
						<td width="150" class="enc_tabla" style="border-right:#ffffff 1px solid">SD</td>
						<td class="enc_tabla" style="border-right:#ffffff 1px solid">HABITACIONES</td>
						<td width="80" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">CTDAD HABITAC.</td>
						<td width="90" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">HABITAC. PENDIENTES</td>
						<td width="150" class="enc_tabla" style="border-right:#ffffff 1px solid">CAUSAS</td>
						<td width="150" class="enc_tabla" style="border-right:#ffffff 1px solid">COMENTARIO</td>
					</tr>';
		$mpdf->WriteHTML($html, 2);
		
		$storedata = $_POST['hfoStore'];
		$records   = json_decode(stripslashes($storedata));
		$count_hfo = 0;

		foreach ($records as $record) {
		
			$count_hfo++;
		
			$sd            = $record->sd;
			$habitaciones  = $cadenas->codificarBD_utf8($record->habitaciones);
			$ctdad_habit   = $record->ctdad_habit;
			$pendientes    = $record->pendientes;
			$problema      = $cadenas->codificarBD_utf8($record->problema);
			$observaciones = $cadenas->codificarBD_utf8($record->observaciones);
		
			if($count_hfo == 1) $colorline = '606163'; else $colorline = 'c0c1c1';
		
			$html = '  
				<tr>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$sd.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$habitaciones.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$ctdad_habit.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$pendientes.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$problema.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$observaciones.'</td>
				</tr>
			';
			$mpdf->WriteHTML($html,2);
		
		}
		
		$html = '<tr>
					<td style="border-top:#606163 1px solid;" colspan="6"></td>
				 </tr>
			</table><br>';
		$mpdf->WriteHTML($html, 2);
		$mpdf->AddPage();
	}
    
    ///////////////////////////////////////////////
    ///////////      FIN TABLA HFO      ///////////
    ///////////////////////////////////////////////
	
	
	////////////////////////////////////////////////////////////
    ///////           TABLA COMPORTAMIENTO HFO           ///////
    ////////////////////////////////////////////////////////////
	if(isset($_POST['comportHfoStore']) && $_POST['comportHfoStore'] != ''){
		$html = '<br>
				 <table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="6" class="title_section">COMPORTAMIENTO DE LAS HABITACIONES FUERA DE ORDEN</td>
					</tr>
					 <tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<tr>
						<td class="enc_tabla" style="border-right:#ffffff 1px solid">INDICADOR</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">DEMORA</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">CTDAD HFO</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">META</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">ESTADO</td>
						<td width="100" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">TENDENCIA</td>
					</tr>';
		$mpdf->WriteHTML($html, 2);
		
		$storedata = $_POST['comportHfoStore'];
		$records   = json_decode(stripslashes($storedata));
		$count_hfo = 0;

		foreach ($records as $record) {
		
			$count_hfo++;
		
			$indicador = $cadenas->codificarBD_utf8($record->indicador);
			$demora    = $record->demora;
			$ctdad     = $record->ctdad;
			$meta      = $record->meta;
			$estado    = $record->estado;
			$tendencia = $record->tendencia;
			
			// Definir iconos de estado y tendencia
			if(strlen($estado) > 0 && $estado == 'Bien'){
				$icon_estado = '<img src="../../resources/images/icons/HansUp.png" border="0">';
				// Definir icono del estado
				if(strlen($tendencia) > 0 && $tendencia == 'asc'){
					$icon_tendencia = '<img src="../../resources/images/icons/green_asc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'desc'){
					$icon_tendencia = '<img src="../../resources/images/icons/green_desc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'const'){
					$icon_tendencia = '<img src="../../resources/images/icons/green_const.png" border="0">';
				}
			}
			elseif(strlen($estado) > 0 && $estado == 'Mal'){
				$icon_estado = '<img src="../../resources/images/icons/HansDown.png" border="0">';
				// Definir icono del estado
				if(strlen($tendencia) > 0 && $tendencia == 'asc'){
					$icon_tendencia = '<img src="../../resources/images/icons/red_asc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'desc'){
					$icon_tendencia = '<img src="../../resources/images/icons/red_desc.png" border="0">';
				}
				elseif(strlen($tendencia) > 0 && $tendencia == 'const'){
					$icon_tendencia = '<img src="../../resources/images/icons/red_const.png" border="0">';
				}
			}
			else{ $icon_estado = ''; $icon_tendencia = ''; }
		
			if($count_hfo == 1) $colorline = '606163'; else $colorline = 'c0c1c1';
		
			$html = '  
				<tr>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;">'.$indicador.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$demora.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$ctdad.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$meta.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$icon_estado.'</td>
					<td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid; vertical-align: middle;" align="center">'.$icon_tendencia.'</td>
				</tr>
			';
			$mpdf->WriteHTML($html,2);
		
		}
		
		$html = '<tr>
					<td style="border-top:#606163 1px solid;" colspan="6"></td>
				 </tr>
			</table><br>';
		$mpdf->WriteHTML($html, 2);
	}
    
    //////////////////////////////////////////////////////////////
    ///////////      FIN TABLA COMPORTAMIENTO HFO      ///////////
    //////////////////////////////////////////////////////////////
        
    // Comentario final del informe			
    if($coment_final != ''){
        $mpdf->AddPage();
		$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>' .str_replace('\x0A','<br>',$coment_final). '</td>
                    </tr>
                </table>';
        $mpdf->WriteHTML($html, 2);
    }
	
	
    $mpdf->Output($titulo . ' ' . $fecha . '.pdf', 'I');
    exit;

////////////////////////////////////////////
/////////        END MPDF 6        /////////
////////////////////////////////////////////


