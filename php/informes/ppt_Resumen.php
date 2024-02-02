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
	
	// Recibir parametros del informe
	$id_informe    = $_POST['id'];
	$exist_estados = false;
	
	// Obtener los Parametros Generales
    $polo = ($_SESSION['polo_name'] != 'Todos') ? $_SESSION['polo_name'] : 'BBI SUCURSAL HABABA, CUBA';
	
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
			$MyData->addPoints(array("No Procede (".$porciento_noproceden." %)", "Por Resolver (".$porciento_poresolver." %)", "Reclamadas (".$porciento_reclamadas." %)", "Firmadas (".$porciento_firmadas." %)", "En Proceso (".$porciento_enproceso." %)"),"Labels");
			$MyData->setAbscissa("Labels");
		   
			/* Create the pChart object */
			$myPicture = new pImage(800,300,$MyData,TRUE);
			
			/* Add a border to the picture */
			$myPicture->drawRectangle(0,0,799,299,array("R"=>0,"G"=>0,"B"=>0));
			
			/* Draw a solid background */
			$Settings = array("R"=>255, "G"=>255, "B"=>255);
			$myPicture->drawFilledRectangle(1,1,798,298,$Settings);
		   
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
			$PieChart->drawPieLegend(30,280,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
		   
			/* Render the picture (choose the best way) */
			if(copy('../../resources/images/rendercharts/rendertemplate.png','../../resources/images/rendercharts/chartsdestados.png')){
				
			$myPicture->Render("../../resources/images/rendercharts/chartsdestados.png");
		   
			}
			
		//////////////////////////////////////////////
		/////////      END pCHART 2.1.4      /////////
		//////////////////////////////////////////////
	}
	
	
	/////////////////////////////////////////////////
	/////////        PhpPresentation        /////////
	/////////////////////////////////////////////////

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
    
    if($desde == '1900-01-01'){ $desde = 'Inicio del Proyecto'; }
	if($hasta == '1900-01-01'){ $hasta = 'Hoy'; }
    
    include_once 'ppt_headerResumen.php';
    
    use PhpOffice\PhpPresentation\PhpPresentation;
    use PhpOffice\PhpPresentation\Style\Alignment;
    use PhpOffice\PhpPresentation\Slide\Background\Color;
    use PhpOffice\PhpPresentation\Style\Color as StyleColor;
    use PhpOffice\PhpPresentation\Slide\Background\Image;
    use PhpOffice\PhpPresentation\Style\Border;
    use PhpOffice\PhpPresentation\Style\Fill;
    use PhpOffice\PhpPresentation\Shape\Drawing;
    use PhpOffice\PhpPresentation\Slide\Transition;
    
    // Create new PHPPresentation object
    //echo date('H:i:s') . ' Create new PHPPresentation object' . EOL;
    $objPHPPresentation = new PhpPresentation();
    
    // Set properties
    //echo date('H:i:s') . ' Set properties'.EOL;
    $objPHPPresentation->getProperties()->setCreator('PHPOffice')
                                      ->setLastModifiedBy('PHPPresentation SEMTI')
                                      ->setTitle('Informe Garantia Resumen')
                                      ->setSubject('Informe de Garantia Resumen')
                                      ->setDescription('Informe de Garantia Resumen')
                                      ->setKeywords('office 2007 openxml libreoffice odt php')
                                      ->setCategory('Informes Garantia');
    
    // Transitions
    $oTransition = new Transition();
    $oTransition->setSpeed('slow');
    $oTransition->setTransitionType(Transition::TRANSITION_BLINDS_VERTICAL);
    
    // Slide > Background > Image
    $oBkgImage = new Image();
    $oBkgImage->setPath('../../resources/images/proyectos/'.$imagen);
    
    // PORTADA
    $currentSlide = $objPHPPresentation->getActiveSlide();
    $currentSlide->setTransition($oTransition);
    $currentSlide->setBackground($oBkgImage);
    
    // Creating Top Layer
    $shape = new Drawing();
    $shape->setName('UCM logo')
          ->setDescription('UCM logo')
          ->setPath('../../resources/images/logo/ppt_top.png')
          ->setHeight(90)
          ->setWidth(600)
          ->setOffsetX(362)
          ->setOffsetY(30);
    $currentSlide->addShape($shape);
    
    // Creating Bottom Layer
    // Table
    $shape = $currentSlide->createTableShape(3);
    $shape->setOffsetX(0);
    $shape->setOffsetY(520);
    $shape->getBorder()->setColor(new StyleColor(StyleColor::COLOR_RED))->setDashStyle(Border::DASH_SOLID)->setLineStyle(Border::LINE_SINGLE);
    
    // Add row
    $row = $shape->createRow();
    $row->setHeight(50);
    $row->getFill()->setFillType(Fill::FILL_SOLID)
                   ->setRotation(90)
                   ->setStartColor(new StyleColor('FFFFFFFF'))
                   ->setEndColor(new StyleColor('FFFFFFFF'));
    
    // Cell #1
    $cell = $row->nextCell();
    $cell->setWidth(50);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
    
	// Cell #2
    $cell = $row->nextCell();
    $cell->setWidth(380);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT )->setMarginRight(10);
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_BOTTOM );
    $cell->createTextRun(strtoupper($polo).' - '.date('d/m/Y'))->getFont()->setBold(false)->setSize(14);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
     
    // Cell #3
    $cell = $row->nextCell();
    $cell->setWidth(270);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT )->setMarginRight(10);
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_BOTTOM );
    $cell->createTextRun($proyecto)->getFont()->setBold(true)->setSize(16);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
   
    // Add row
    $row = $shape->createRow();
    $row->setHeight(60);
    $row->getFill()->setFillType(Fill::FILL_SOLID)
                   ->setRotation(90)
                   ->setStartColor(new StyleColor('FFFFFFFF'))
                   ->setEndColor(new StyleColor('FFFFFFFF'));
    
    // Cell #1
    $cell = $row->nextCell();
    $cell->setWidth(50);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
    
    // Cell #2
    $cell = $row->nextCell();
    $cell->setWidth(650);
    $cell->setColSpan(2);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT )->setMarginRight(10);
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_TOP );
    $cell->createTextRun($titulo)->getFont()->setName('Arial')->setBold(true)->setSize(22);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
    
    //////////////////////////////////////////////////////////
    
    
    // SLIDE COMENTARIO INICIAL
	if(strlen($coment_inicial) > 1){
	   
		$oSlide1 = $objPHPPresentation->createSlide();
		$oSlide1->setTransition($oTransition);
	
		// Slide > Background > Color
		//$oBkgColor = new Color();
		//$oBkgColor->setColor(new StyleColor(StyleColor::COLOR_WHITE));
		$oSlide1->setBackground($oBkgImage);
	
		// Creating Top Layer
		$shape = new Drawing();
		$shape->setName('UCM logo')
			  ->setDescription('UCM logo')
			  ->setPath('../../resources/images/logo/ppt_top.png')
			  ->setHeight(90)
			  ->setWidth(600)
			  ->setOffsetX(362)
			  ->setOffsetY(30);
		$oSlide1->addShape($shape);
	
		// Crear la Tabla Superior del reporte   
		$shape = $oSlide1->createTableShape(2);
        $shape->setOffsetX(30);
        $shape->setOffsetY(180);

        // Add row
        $row = $shape->createRow();
        $row->setHeight(50);
        $row->getFill()->setFillType(Fill::FILL_SOLID)
                       ->setRotation(90)
                       ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
                       ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));

        // Cell #1
        $cell = $row->nextCell();
        $cell->setWidth(730);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
        $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

        // Cell #2
        $cell = $row->nextCell();
        $cell->setWidth(170);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->createTextRun('Fecha: '.date('d/m/Y'))->getFont()->setBold(FALSE)->setSize(14);
        $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	
		//////////////////////////////////////////////////////////
	
		// Agregar comentario inicial del reporte  
		$shape = $oSlide1->createTableShape(1);
		$shape->setOffsetX(30);
		$shape->setOffsetY(250);
	
		// Add row
		$row = $shape->createRow();
		$row->setHeight(300);
		$row->getFill()->setFillType(Fill::FILL_SOLID)
					   ->setRotation(90)
					   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
					   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
		$row->getFill()->setFillType(Fill::FILL_SOLID)
					   ->setRotation(90)
					   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
					   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
	
		// Cell #1
		$cell = $row->nextCell();
		$cell->setWidth(900);
		$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
		$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_TOP );
		$cell->createTextRun($coment_inicial)->getFont()->setBold(FALSE)->setSize(14);
		$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
		$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
		$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
		$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	}
	
	//////////////////////////////////////////////////

	//////////////////////////////////////////////
    ///////        SECCION ESTADOS          //////
    //////////////////////////////////////////////
    
    if($exist_estados == true){
        
        // Create slide
        $oSlide = $objPHPPresentation->createSlide();
        $oSlide->setTransition($oTransition);
        $oSlide->setBackground($oBkgImage);

        $shape = new Drawing();
        $shape->setName('UCM logo')
              ->setDescription('UCM logo')
              ->setPath('../../resources/images/logo/ppt_top.png')
              ->setHeight(90)
              ->setWidth(600)
              ->setOffsetX(362)
              ->setOffsetY(30);
        $oSlide->addShape($shape);

        
        // Crear la Tabla Superior del reporte 

        $shape = $oSlide->createTableShape(2);
        $shape->setOffsetX(30);
        $shape->setOffsetY(180);

        // Add row
        $row = $shape->createRow();
        $row->setHeight(50);
        $row->getFill()->setFillType(Fill::FILL_SOLID)
                       ->setRotation(90)
                       ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
                       ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));

        // Cell #1
        $cell = $row->nextCell();
        $cell->setWidth(730);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
        $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

        // Cell #2
        $cell = $row->nextCell();
        $cell->setWidth(170);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->createTextRun('Fecha: '.date('d/m/Y'))->getFont()->setBold(FALSE)->setSize(14);
        $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

        //////////////////////////////////////////////////////////

        // Crear Tabla
        $shape = $oSlide->createTableShape(7);
        $shape->setOffsetX(30);
        $shape->setOffsetY(250);

        // Encabezado
        $row = $shape->createRow();
        $row->setHeight(40);
        $row->getFill()->setFillType(Fill::FILL_SOLID)
                       ->setRotation(90)
                       ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
                       ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
        
		$cell = $row->nextCell();
        $cell->setWidth(210)->createTextRun('INDICADOR')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        
		$cell = $row->nextCell();
		$cell->setWidth(120)->createTextRun('EN PROCESO')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->setWidth(120)->createTextRun('FIRMADAS')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->setWidth(120)->createTextRun('POR RESOLVER')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->setWidth(120)->createTextRun('RECLAMADAS')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->setWidth(120)->createTextRun('NO PROCEDEN')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->setWidth(100)->createTextRun('TOTAL')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER);
        
        // Add row
        $row = $shape->createRow();
        $row->setHeight(25);
        $row->getFill()->setFillType(Fill::FILL_SOLID)
                       ->setRotation(90)
                       ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
                       ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
        
		$cell = $row->nextCell();
        $cell->createTextRun('Solicitudes de Defectación')->getFont()->setSize(12);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        
		$cell = $row->nextCell();
        $cell->createTextRun($enproceso)->getFont()->setSize(12);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );

		$cell = $row->nextCell();
        $cell->createTextRun($firmadas)->getFont()->setSize(12);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->createTextRun($poresolver)->getFont()->setSize(12);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->createTextRun($reclamadas)->getFont()->setSize(12);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->createTextRun($noproceden)->getFont()->setSize(12);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
		$cell = $row->nextCell();
        $cell->createTextRun($total)->getFont()->setSize(12);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        
        // Grafica
        $shape = new Drawing();
        $shape->setName('EstadosPastel')
              ->setDescription('Grafica de Estados')
              ->setPath('../../resources/images/rendercharts/chartsdestados.png')
              ->setHeight(300)
              ->setWidth(800)
              ->setOffsetX(80)
              ->setOffsetY(340);
        $oSlide->addShape($shape);
		
		// Pie de pagina
		$shape = $oSlide->createTableShape(2);
		$shape->setOffsetX(30);
		$shape->setOffsetY(660);
			
		// Add row
		$row = $shape->createRow();
		$row->setHeight(40);
		$row->getFill()->setFillType(Fill::FILL_SOLID)
					   ->setRotation(90)
					   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
					   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));

		// Cell #1
		$cell = $row->nextCell();
		//$cell->setWidth(690);
		$cell->setWidth(730)->createTextRun('COMPORTAMIENTO DEL ESTADO DE LAS SD')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
		$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
		$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
  
		// Cell #2
		$cell = $row->nextCell();
		$cell->setWidth(170)->createTextRun('Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
		$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
		$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            
    }
    
    /////////////////////////////////////////////
    ///////      FIN SECCION ESTADOS      ///////
    /////////////////////////////////////////////
	
	
	////////////////////////////////////////////////////
    ///////        SECCION SD PENDIENTES          //////
    ////////////////////////////////////////////////////
    
    if(isset($_POST['sdpendStore']) && $_POST['sdpendStore'] != ''){
        
        $No         = 0;
		$sdpendData = $_POST['sdpendStore'];
		$records    = json_decode(stripslashes($sdpendData));
		$total      = count($records);
		$defecto    = '';
    
		$problema_sd   = array();
		$descripcion   = array();
		$zonas         = array();
		$objetos       = array();
		$locales       = array();
		$departamentos = array();
		$comentario    = array();
		
		foreach ($records as $record) {
		
			$No++;
		
			$problema_sd[]   = $cadenas->codificarBD_utf8($record->problema_sd);
			$descripcion[]   = $cadenas->codificarBD_utf8($record->descripcion);
			$zonas[]         = $record->zonas;
			$objetos[]       = $cadenas->codificarBD_utf8($record->objetos);
			$locales[]       = $cadenas->codificarBD_utf8($record->locales);
			$departamentos[] = $cadenas->codificarBD_utf8($record->dpto);
			$comentario[]    = $cadenas->codificarBD_utf8($record->comentario);
		
		}
		//////////////////////////////////////////////////////////
		
		// Crear paginas de presentacion
		$reg_pagina = 4;
		$paginas = intval($No / $reg_pagina);
		$resto = $No % $reg_pagina;
		if($resto > 0){ $paginas += 1; }
		
		for($i=0;$i<$paginas;$i++){
									
			// Create slide
			$oSlide = $objPHPPresentation->createSlide();
			$oSlide->setTransition($oTransition);
			$oSlide->setBackground($oBkgImage);
	
			$shape = new Drawing();
			$shape->setName('UCM logo')
				  ->setDescription('UCM logo')
				  ->setPath('../../resources/images/logo/ppt_top.png')
				  ->setHeight(90)
				  ->setWidth(600)
				  ->setOffsetX(362)
				  ->setOffsetY(30);
			$oSlide->addShape($shape);
	
			
			// Crear la Tabla Superior del reporte 
	
			$shape = $oSlide->createTableShape(2);
			$shape->setOffsetX(30);
			$shape->setOffsetY(180);
	
			// Add row
			$row = $shape->createRow();
			$row->setHeight(50);
			$row->getFill()->setFillType(Fill::FILL_SOLID)
						   ->setRotation(90)
						   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
						   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
	
			// Cell #1
			$cell = $row->nextCell();
			$cell->setWidth(730);
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
			$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	
			// Cell #2
			$cell = $row->nextCell();
			$cell->setWidth(170);
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->createTextRun('Fecha: '.date('d/m/Y'))->getFont()->setBold(FALSE)->setSize(14);
			$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	
			//////////////////////////////////////////////////////////
	
			// Crear Tabla
			$shape = $oSlide->createTableShape(6);
			$shape->setOffsetX(30);
			$shape->setOffsetY(250);
	
			// Encabezado
			$row = $shape->createRow();
			$row->setHeight(40);
			$row->getFill()->setFillType(Fill::FILL_SOLID)
						   ->setRotation(90)
						   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
						   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
			$cell = $row->nextCell();
			$cell->setWidth(280)->createTextRun('DESCRIPCIÓN')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell = $row->nextCell();
			$cell->setWidth(60)->createTextRun('ZONA')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
			$cell = $row->nextCell();
			$cell->setWidth(160)->createTextRun('OBJETOS')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell = $row->nextCell();
			$cell->setWidth(120)->createTextRun('LOCALES')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell = $row->nextCell();
			$cell->setWidth(120)->createTextRun('DPTOS')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
			$cell = $row->nextCell();
			$cell->setWidth(160)->createTextRun('COMENTARIO')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER);
        
			$start = $i * $reg_pagina;
            $limit = $start + $reg_pagina;
            if($limit >= $total) $limit = $total;
    
            for($j=$start;$j<$limit;$j++){
		
				if($defecto != $problema_sd[$j]){
				
					$defecto = $problema_sd[$j];
					$objetosread = $objetos[$j];
					if(strlen($objetosread) > 30){
						$array_objetos = explode(',',$objetosread);
						$ctdad_objetos = count($array_objetos);
						$objetosread = substr($objetosread,0,30).'...('.$ctdad_objetos.')';
					}
					$localesread = $locales[$j];
					if(strlen($localesread) > 30){
						$array_locales = explode(',',$localesread);
						$ctdad_locales = count($array_locales);
						$localesread = substr($localesread,0,30).'...('.$ctdad_locales.')';
					}
					
			
					// Add row
					$row = $shape->createRow();
					$row->setHeight(25);
					$row->getFill()->setFillType(Fill::FILL_SOLID)
								   ->setRotation(90)
								   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
								   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
					$cell = $row->nextCell();
					$cell->setColSpan(6);
					$cell->createTextRun(strtoupper($problema_sd[$j]))->getFont()->setBold(true)->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
					
					// Add row
					$row = $shape->createRow();
					$row->setHeight(25);
					$row->getFill()->setFillType(Fill::FILL_SOLID)
								   ->setRotation(90)
								   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
								   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
					$cell = $row->nextCell();
					$cell->createTextRun($descripcion[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
					$cell = $row->nextCell();
					$cell->createTextRun($zonas[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($objetosread)->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($localesread)->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($departamentos[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($comentario[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				}
				else{
					
					// Add row
					$row = $shape->createRow();
					$row->setHeight(25);
					$row->getFill()->setFillType(Fill::FILL_SOLID)
								   ->setRotation(90)
								   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
								   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
					$cell = $row->nextCell();
					$cell->createTextRun($descripcion[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
					$cell = $row->nextCell();
					$cell->createTextRun($zonas[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($objetos[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($locales[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($departamentos[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($comentario[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				}
        
			}
		
			// Numero pagina
			//$numpag_sdpend = $i + 2;
			
			// Pie de pagina
			$shape = $oSlide->createTableShape(2);
			$shape->setOffsetX(30);
			$shape->setOffsetY(660);
				
			// Add row
			$row = $shape->createRow();
			$row->setHeight(40);
			$row->getFill()->setFillType(Fill::FILL_SOLID)
						   ->setRotation(90)
						   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
						   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
	
			// Cell #1
			$cell = $row->nextCell();
			//$cell->setWidth(690);
			$cell->setWidth(730)->createTextRun('SD PENDIENTES POR RESOLVER')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
	  
			// Cell #2
			$cell = $row->nextCell();
			$cell->setWidth(170)->createTextRun('Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		}
            
    }
    
    ///////////////////////////////////////////////////
    ///////      FIN SECCION SD PENDIENTES      ///////
    ///////////////////////////////////////////////////
    
    
	//////////////////////////////////////////////////////////////
    ///////        SECCION INDICADORES PRINCIPALES          //////
    //////////////////////////////////////////////////////////////
    
    if(isset($_POST['pindicStore']) && $_POST['pindicStore'] != ''){
        
		$No        = 0;
		$storedata = $_POST['pindicStore'];
		$records   = json_decode($cadenas->codificarBD_utf8(stripslashes($storedata)));
		$total     = count($records);
		
		$indicador   = array();
		$periodo_ant = array();
		$periodo_act = array();
		$acumulado   = array();
		$meta        = array();
		$estado      = array();
		$tendencia   = array();
		$acciones    = array();
		
		foreach ($records as $record) {
		
			$No++;
		
			$indicador[]   = $cadenas->codificarBD_utf8($record->indicador);
			$periodo_ant[] = $record->periodo_ant;
			$periodo_act[] = $record->periodo_act;
			$acumulado[]   = $record->acumulado;
			$meta[]        = $record->meta;
			$estado[]      = $record->estado;
			$tendencia[]   = $record->tendencia;
			$acciones[]    = $cadenas->codificarBD_utf8($record->acciones);
		
		}
		//////////////////////////////////////////////////////////
		
		// Crear paginas de presentacion
		$reg_pagina = 1;
		$paginas = intval($No / $reg_pagina);
		$resto = $No % $reg_pagina;
		if($resto > 0){ $paginas += 1; }
		
		for($i=0;$i<$paginas;$i++){
		
				// Create slide
				$oSlide = $objPHPPresentation->createSlide();
				$oSlide->setTransition($oTransition);
				$oSlide->setBackground($oBkgImage);
		
				$shape = new Drawing();
				$shape->setName('UCM logo')
					  ->setDescription('UCM logo')
					  ->setPath('../../resources/images/logo/ppt_top.png')
					  ->setHeight(90)
					  ->setWidth(600)
					  ->setOffsetX(362)
					  ->setOffsetY(30);
				$oSlide->addShape($shape);
		
				
				///////////////////////////////////////////////
				///   Crear la Tabla Superior del reporte   ///
				///////////////////////////////////////////////
				$shape = $oSlide->createTableShape(2);
				$shape->setOffsetX(30);
				$shape->setOffsetY(180);
		
				// Add row
				$row = $shape->createRow();
				$row->setHeight(50);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
		
				// Cell #1
				$cell = $row->nextCell();
				$cell->setWidth(630);
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
				$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
		
				// Cell #2
				$cell = $row->nextCell();
				$cell->setWidth(270);
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell->createTextRun('PRINCIPALES INDICADORES')->getFont()->setBold(TRUE)->setSize(16);
				$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
		
				//////////////////////////////////////////////////////////
		
				// Crear Tabla
				$shape = $oSlide->createTableShape(7);
				$shape->setOffsetX(30);
				$shape->setOffsetY(250);
		
				// Encabezado
				$row = $shape->createRow();
				$row->setHeight(40);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('PER. ANT.')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('PER. ACT.')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('ACUM')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(120)->createTextRun('META')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(90)->createTextRun('ESTADO')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(90)->createTextRun('TEND.')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(300)->createTextRun('ACCIONES')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		
				$start = $i * $reg_pagina;
				$limit = $start + $reg_pagina;
				if($limit >= $total) $limit = $total;
		
				for($j=$start;$j<$limit;$j++){
		
						// Definir iconos de estado y tendencia
						if($estado[$j] === 'Bien'){
							$estadotext = 'Bien :)';
							if($tendencia[$j] == 'asc'){
								$tendenciatext = 'Aumentar';
							}
							elseif($tendencia[$j] == 'desc'){
								$tendenciatext = 'Disminuir';
							}
							elseif($tendencia[$j] == 'const'){
								$tendenciatext = 'Constante';
							}
						}
						elseif($estado[$j] === 'Mal'){
							$estadotext = 'Mal :(';
							if($tendencia[$j] == 'asc'){
								$tendenciatext = 'Aumentar';
							}
							elseif($tendencia[$j] == 'desc'){
								$tendenciatext = 'Disminuir';
							}
							elseif($tendencia[$j] == 'const'){
								$tendenciatext = 'Constante';
							}
						}
						else{ $estadotext = ' '; $tendenciatext = ' '; }
						
						// Add row
						$row = $shape->createRow();
						$row->setHeight(25);
						$row->getFill()->setFillType(Fill::FILL_SOLID)
									   ->setRotation(90)
									   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
									   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
						$cell = $row->nextCell();
						//if(strlen($habitaciones[$j]) > 85) $habitaciones[$j] = substr ($habitaciones[$j],0,85)."...";
						$cell->createTextRun($periodo_ant[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						//$row->nextCell()->createTextRun($habitaciones[$j])->getFont()->setSize(14);
						$cell = $row->nextCell();
						$cell->createTextRun($periodo_act[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						//$row->nextCell()->createTextRun($ctdad_habit[$j])->getFont()->setSize(14);
						$cell = $row->nextCell();
						$cell->createTextRun($acumulado[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						$cell = $row->nextCell();
						$cell->createTextRun($meta[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						$cell = $row->nextCell();
						$cell->createTextRun($estadotext)->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						$cell = $row->nextCell();
						$cell->createTextRun($tendenciatext)->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						//$row->nextCell()->createTextRun($problema[$j])->getFont()->setSize(12);
						if($acciones[$j] == '') $acciones[$j] = " ";
						$cell = $row->nextCell();
						$cell->createTextRun($acciones[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				}
				
				$shape = $oSlide->createTableShape(2);
				$shape->setOffsetX(30);
				$shape->setOffsetY(660);
		
				// Add row
				$row = $shape->createRow();
				$row->setHeight(40);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
		
				// Cell #1
				$cell = $row->nextCell();
				//$cell->setWidth(690);
				$cell->setWidth(730)->createTextRun($indicador[$j-1])->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		  
				// Cell #2
				$cell = $row->nextCell();
				$cell->setWidth(170)->createTextRun('Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		}
	}
    
    /////////////////////////////////////////////////////////////
    ///////      FIN SECCION INDICADORES PRINCIPALES      ///////
    /////////////////////////////////////////////////////////////
	
	
	////////////////////////////////////////////////////
    ///////        SECCION REPETITIVIDAD          //////
    ////////////////////////////////////////////////////
    
    if(isset($_POST['repetStore']) && $_POST['repetStore'] != ''){
        
        $No         = 0;
		$repetData  = $_POST['repetStore'];
		$records    = json_decode(stripslashes($repetData));
		$total      = count($records);
		$defecto    = '';
    
		$problema_descripcion = array();
		$sd_descripcion       = array();
		$sd_ctdad             = array();
		$comentario           = array();
		
		foreach ($records as $record) {
		
			$No++;
		
			$problema_descripcion[] = $cadenas->codificarBD_utf8($record->problema_descripcion);
			$sd_descripcion[]       = $cadenas->codificarBD_utf8($record->sd_descripcion);
			$sd_ctdad[]             = $record->sd_ctdad;
			$comentario[]           = $cadenas->codificarBD_utf8($record->comentario);
		
		}
		//////////////////////////////////////////////////////////
		
		// Crear paginas de presentacion
		$reg_pagina = 4;
		$paginas = intval($No / $reg_pagina);
		$resto = $No % $reg_pagina;
		if($resto > 0){ $paginas += 1; }
		
		for($i=0;$i<$paginas;$i++){
									
			// Create slide
			$oSlide = $objPHPPresentation->createSlide();
			$oSlide->setTransition($oTransition);
			$oSlide->setBackground($oBkgImage);
	
			$shape = new Drawing();
			$shape->setName('UCM logo')
				  ->setDescription('UCM logo')
				  ->setPath('../../resources/images/logo/ppt_top.png')
				  ->setHeight(90)
				  ->setWidth(600)
				  ->setOffsetX(362)
				  ->setOffsetY(30);
			$oSlide->addShape($shape);
	
			
			// Crear la Tabla Superior del reporte 
	
			$shape = $oSlide->createTableShape(2);
			$shape->setOffsetX(30);
			$shape->setOffsetY(180);
	
			// Add row
			$row = $shape->createRow();
			$row->setHeight(50);
			$row->getFill()->setFillType(Fill::FILL_SOLID)
						   ->setRotation(90)
						   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
						   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
	
			// Cell #1
			$cell = $row->nextCell();
			$cell->setWidth(650);
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
			$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	
			// Cell #2
			$cell = $row->nextCell();
			$cell->setWidth(250);
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->createTextRun('Fecha: '.date('d/m/Y'))->getFont()->setBold(FALSE)->setSize(14);
			$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
			$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	
			//////////////////////////////////////////////////////////
	
			// Crear Tabla
			$shape = $oSlide->createTableShape(3);
			$shape->setOffsetX(30);
			$shape->setOffsetY(250);
	
			// Encabezado
			$row = $shape->createRow();
			$row->setHeight(40);
			$row->getFill()->setFillType(Fill::FILL_SOLID)
						   ->setRotation(90)
						   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
						   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
			$cell = $row->nextCell();
			$cell->setWidth(360)->createTextRun('DESCRIPCIÓN')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell = $row->nextCell();
			$cell->setWidth(80)->createTextRun('CTDAD')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
			$cell = $row->nextCell();
			$cell->setWidth(460)->createTextRun('COMENTARIO')->getFont()->setBold(true)->setSize(12)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER);
        
			$start = $i * $reg_pagina;
            $limit = $start + $reg_pagina;
            if($limit >= $total) $limit = $total;
    
            for($j=$start;$j<$limit;$j++){
		
				if($defecto != $problema_descripcion[$j]){
				
					$defecto = $problema_descripcion[$j];					
			
					// Add row
					$row = $shape->createRow();
					$row->setHeight(25);
					$row->getFill()->setFillType(Fill::FILL_SOLID)
								   ->setRotation(90)
								   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
								   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
					$cell = $row->nextCell();
					$cell->setColSpan(6);
					$cell->createTextRun(strtoupper($problema_descripcion[$j]))->getFont()->setBold(true)->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
					
					// Add row
					$row = $shape->createRow();
					$row->setHeight(25);
					$row->getFill()->setFillType(Fill::FILL_SOLID)
								   ->setRotation(90)
								   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
								   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
					$cell = $row->nextCell();
					$cell->createTextRun($sd_descripcion[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
					$cell = $row->nextCell();
					$cell->createTextRun($sd_ctdad[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($comentario[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				}
				else{
					
					// Add row
					$row = $shape->createRow();
					$row->setHeight(25);
					$row->getFill()->setFillType(Fill::FILL_SOLID)
								   ->setRotation(90)
								   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
								   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
					$cell = $row->nextCell();
					$cell->createTextRun($sd_descripcion[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
					$cell = $row->nextCell();
					$cell->createTextRun($sd_ctdad[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
					$cell = $row->nextCell();
					$cell->createTextRun($comentario[$j])->getFont()->setSize(12);
					$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				}
        
			}
		
			// Numero pagina
			//$numpag_sdpend = $i + 2;
			
			// Pie de pagina
			$shape = $oSlide->createTableShape(2);
			$shape->setOffsetX(30);
			$shape->setOffsetY(660);
				
			// Add row
			$row = $shape->createRow();
			$row->setHeight(40);
			$row->getFill()->setFillType(Fill::FILL_SOLID)
						   ->setRotation(90)
						   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
						   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
	
			// Cell #1
			$cell = $row->nextCell();
			//$cell->setWidth(690);
			$cell->setWidth(730)->createTextRun('PROBLEMAS REPETITIVOS')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
	  
			// Cell #2
			$cell = $row->nextCell();
			$cell->setWidth(170)->createTextRun('Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
			$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
			$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		}
            
    }
    
    ///////////////////////////////////////////////////
    ///////      FIN SECCION REPETITIVIDAD      ///////
    ///////////////////////////////////////////////////
	
	
	//////////////////////////////////////////////////////////////////
    ///////        SECCION HABITACIONES FUERA DE ORDEN          //////
    //////////////////////////////////////////////////////////////////
    
    if(isset($_POST['hfoStore']) && $_POST['hfoStore'] != ''){
        
		$No        = 0;
		$storedata = $_POST['hfoStore'];
		$records   = json_decode($cadenas->codificarBD_utf8(stripslashes($storedata)));
		$total     = count($records);
		
		$sd            = array();
		$habitaciones  = array();
		$ctdad_habit   = array();
		$pendientes    = array();
		$problema      = array();
		$observaciones = array();
		
		foreach ($records as $record) {
		
			$No++;
		
			$sd[]            = $record->sd;
			$habitaciones[]  = $record->habitaciones;
			$ctdad_habit[]   = $record->ctdad_habit;
			$pendientes[]    = $record->pendientes;
			$problema[]      = $record->problema;
			$observaciones[] = $record->observaciones;
		
		}
		//////////////////////////////////////////////////////////
		
		// Crear paginas de presentacion
		$reg_pagina = 1;
		$paginas = intval($No / $reg_pagina);
		$resto = $No % $reg_pagina;
		if($resto > 0){ $paginas += 1; }
		
		for($i=0;$i<$paginas;$i++){
		
				// Create slide
				$oSlide = $objPHPPresentation->createSlide();
				$oSlide->setTransition($oTransition);
				$oSlide->setBackground($oBkgImage);
		
				$shape = new Drawing();
				$shape->setName('UCM logo')
					  ->setDescription('UCM logo')
					  ->setPath('../../resources/images/logo/ppt_top.png')
					  ->setHeight(90)
					  ->setWidth(600)
					  ->setOffsetX(362)
					  ->setOffsetY(30);
				$oSlide->addShape($shape);
		
				
				///////////////////////////////////////////////
				///   Crear la Tabla Superior del reporte   ///
				///////////////////////////////////////////////
				$shape = $oSlide->createTableShape(2);
				$shape->setOffsetX(30);
				$shape->setOffsetY(180);
		
				// Add row
				$row = $shape->createRow();
				$row->setHeight(50);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
		
				// Cell #1
				$cell = $row->nextCell();
				$cell->setWidth(650);
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
				$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
		
				// Cell #2
				$cell = $row->nextCell();
				$cell->setWidth(250);
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell->createTextRun('HABIT. FUERA DE ORDEN')->getFont()->setBold(TRUE)->setSize(16);
				$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
		
				//////////////////////////////////////////////////////////
		
				// Crear Tabla
				$shape = $oSlide->createTableShape(6);
				$shape->setOffsetX(30);
				$shape->setOffsetY(250);
		
				// Encabezado
				$row = $shape->createRow();
				$row->setHeight(40);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('SD')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(290)->createTextRun('HABITACIONES')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(80)->createTextRun('CTDAD')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('PENDTES')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(140)->createTextRun('CAUSA')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(190)->createTextRun('COMENTARIO')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		
				$start = $i * $reg_pagina;
				$limit = $start + $reg_pagina;
				if($limit >= $total) $limit = $total;
		
				for($j=$start;$j<$limit;$j++){
		
						// Add row
						$row = $shape->createRow();
						$row->setHeight(25);
						$row->getFill()->setFillType(Fill::FILL_SOLID)
									   ->setRotation(90)
									   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
									   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
						$cell = $row->nextCell();
						$cell->createTextRun($sd[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
						$cell = $row->nextCell();
						//if(strlen($habitaciones[$j]) > 85) $habitaciones[$j] = substr ($habitaciones[$j],0,85)."...";
						$cell->createTextRun($habitaciones[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
						//$row->nextCell()->createTextRun($habitaciones[$j])->getFont()->setSize(14);
						$cell = $row->nextCell();
						$cell->createTextRun($ctdad_habit[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						//$row->nextCell()->createTextRun($ctdad_habit[$j])->getFont()->setSize(14);
						$cell = $row->nextCell();
						$cell->createTextRun($pendientes[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						$cell = $row->nextCell();
						$cell->createTextRun($problema[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
						//$row->nextCell()->createTextRun($problema[$j])->getFont()->setSize(12);
						if($observaciones[$j] == '') $observaciones[$j] = " ";
						$cell = $row->nextCell();
						$cell->createTextRun($observaciones[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				}
				
				$shape = $oSlide->createTableShape(2);
				$shape->setOffsetX(30);
				$shape->setOffsetY(660);
		
				// Add row
				$row = $shape->createRow();
				$row->setHeight(40);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
		
				// Cell #1
				$cell = $row->nextCell();
				//$cell->setWidth(690);
				$cell->setWidth(730)->createTextRun($problema[$j-1])->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		  
				// Cell #2
				$cell = $row->nextCell();
				$cell->setWidth(170)->createTextRun('Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		}
	}
    
    /////////////////////////////////////////////////////////////////
    ///////      FIN SECCION HABITACIONES FUERA DE ORDEN      ///////
    /////////////////////////////////////////////////////////////////
	
	
	/////////////////////////////////////////////////////////
    ///////        SECCION COMPORTAMIENTO HFO          //////
    /////////////////////////////////////////////////////////
    
    if(isset($_POST['comportHfoStore']) && $_POST['comportHfoStore'] != ''){
        
		$No        = 0;
		$storedata = $_POST['comportHfoStore'];
		$records   = json_decode($cadenas->codificarBD_utf8(stripslashes($storedata)));
		$total     = count($records);
		
		$indicador = array();
		$demora    = array();
		$ctdad     = array();
		$meta      = array();
		$estado    = array();
		$tendencia = array();
		
		foreach ($records as $record) {
		
			$No++;
		
			$indicador[] = $cadenas->codificarBD_utf8($record->indicador);
			$demora[]    = $record->demora;
			$ctdad[]     = $record->ctdad;
			$meta[]      = $record->meta;
			$estado[]    = $record->estado;
			$tendencia[] = $record->tendencia;
		
		}
		//////////////////////////////////////////////////////////
		
		/*// Crear paginas de presentacion
		$reg_pagina = 1;
		$paginas = intval($No / $reg_pagina);
		$resto = $No % $reg_pagina;
		if($resto > 0){ $paginas += 1; }
		
		for($i=0;$i<$paginas;$i++){*/
		
				// Create slide
				$oSlide = $objPHPPresentation->createSlide();
				$oSlide->setTransition($oTransition);
				$oSlide->setBackground($oBkgImage);
		
				$shape = new Drawing();
				$shape->setName('UCM logo')
					  ->setDescription('UCM logo')
					  ->setPath('../../resources/images/logo/ppt_top.png')
					  ->setHeight(90)
					  ->setWidth(600)
					  ->setOffsetX(362)
					  ->setOffsetY(30);
				$oSlide->addShape($shape);
		
				
				///////////////////////////////////////////////
				///   Crear la Tabla Superior del reporte   ///
				///////////////////////////////////////////////
				$shape = $oSlide->createTableShape(2);
				$shape->setOffsetX(30);
				$shape->setOffsetY(180);
		
				// Add row
				$row = $shape->createRow();
				$row->setHeight(50);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
		
				// Cell #1
				$cell = $row->nextCell();
				$cell->setWidth(630);
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
				$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
		
				// Cell #2
				$cell = $row->nextCell();
				$cell->setWidth(270);
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell->createTextRun('Fecha: '.date('d/m/Y'))->getFont()->setBold(FALSE)->setSize(16);
				$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
				$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
		
				//////////////////////////////////////////////////////////
		
				// Crear Tabla
				$shape = $oSlide->createTableShape(6);
				$shape->setOffsetX(30);
				$shape->setOffsetY(250);
		
				// Encabezado
				$row = $shape->createRow();
				$row->setHeight(40);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
				$cell = $row->nextCell();
				$cell->setWidth(370)->createTextRun('INDICADOR')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('DEMORA')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(110)->createTextRun('CTDAD')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(120)->createTextRun('META')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('ESTADO')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
				$cell = $row->nextCell();
				$cell->setWidth(100)->createTextRun('TEND.')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		
				//$start = $i * $reg_pagina;
				//$limit = $start + $reg_pagina;
				//if($limit >= $total) $limit = $total;
		
				for($j=0;$j<2;$j++){
		
						// Definir iconos de estado y tendencia
						if($estado[$j] === 'Bien'){
							$estadotext = 'Bien :)';
							if($tendencia[$j] == 'asc'){
								$tendenciatext = 'Aumentar';
							}
							elseif($tendencia[$j] == 'desc'){
								$tendenciatext = 'Disminuir';
							}
							elseif($tendencia[$j] == 'const'){
								$tendenciatext = 'Constante';
							}
						}
						elseif($estado[$j] === 'Mal'){
							$estadotext = 'Mal :(';
							if($tendencia[$j] == 'asc'){
								$tendenciatext = 'Aumentar';
							}
							elseif($tendencia[$j] == 'desc'){
								$tendenciatext = 'Disminuir';
							}
							elseif($tendencia[$j] == 'const'){
								$tendenciatext = 'Constante';
							}
						}
						else{ $estadotext = ' '; $tendenciatext = ' '; }
						
						// Add row
						$row = $shape->createRow();
						$row->setHeight(25);
						$row->getFill()->setFillType(Fill::FILL_SOLID)
									   ->setRotation(90)
									   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
									   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
						$cell = $row->nextCell();
						//if(strlen($habitaciones[$j]) > 85) $habitaciones[$j] = substr ($habitaciones[$j],0,85)."...";
						$cell->createTextRun($indicador[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
						//$row->nextCell()->createTextRun($habitaciones[$j])->getFont()->setSize(14);
						$cell = $row->nextCell();
						$cell->createTextRun($demora[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						//$row->nextCell()->createTextRun($ctdad_habit[$j])->getFont()->setSize(14);
						$cell = $row->nextCell();
						$cell->createTextRun($ctdad[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						$cell = $row->nextCell();
						$cell->createTextRun($meta[$j])->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						$cell = $row->nextCell();
						$cell->createTextRun($estadotext)->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						$cell = $row->nextCell();
						$cell->createTextRun($tendenciatext)->getFont()->setSize(12);
						$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
						//$row->nextCell()->createTextRun($problema[$j])->getFont()->setSize(12);
						//if($acciones[$j] == '') $acciones[$j] = " ";
						//$cell = $row->nextCell();
						//$cell->createTextRun($acciones[$j])->getFont()->setSize(12);
						//$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				}
				
				$shape = $oSlide->createTableShape(2);
				$shape->setOffsetX(30);
				$shape->setOffsetY(660);
		
				// Add row
				$row = $shape->createRow();
				$row->setHeight(40);
				$row->getFill()->setFillType(Fill::FILL_SOLID)
							   ->setRotation(90)
							   ->setStartColor(new StyleColor(StyleColor::COLOR_BLACK))
							   ->setEndColor(new StyleColor(StyleColor::COLOR_BLACK));
		
				// Cell #1
				$cell = $row->nextCell();
				//$cell->setWidth(690);
				$cell->setWidth(730)->createTextRun('COMPORTAMIENTO HABITACIONES FUERA DE ORDEN')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		  
				// Cell #2
				$cell = $row->nextCell();
				$cell->setWidth(170)->createTextRun('Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
				$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
				$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
		//}
	}
    
    ////////////////////////////////////////////////////////
    ///////      FIN SECCION COMPORTAMIENTO HFO      ///////
    ////////////////////////////////////////////////////////
	
	
	// SLIDE COMENTARIO FINAL
	if(strlen($coment_final) > 1){
	   
		$oSlide1 = $objPHPPresentation->createSlide();
		$oSlide1->setTransition($oTransition);
	
		// Slide > Background > Color
		//$oBkgColor = new Color();
		//$oBkgColor->setColor(new StyleColor(StyleColor::COLOR_WHITE));
		$oSlide1->setBackground($oBkgImage);
	
		// Creating Top Layer
		$shape = new Drawing();
		$shape->setName('UCM logo')
			  ->setDescription('UCM logo')
			  ->setPath('../../resources/images/logo/ppt_top.png')
			  ->setHeight(90)
			  ->setWidth(600)
			  ->setOffsetX(362)
			  ->setOffsetY(30);
		$oSlide1->addShape($shape);
	
		// Crear la Tabla Superior del reporte   
		$shape = $oSlide1->createTableShape(2);
        $shape->setOffsetX(30);
        $shape->setOffsetY(180);

        // Add row
        $row = $shape->createRow();
        $row->setHeight(50);
        $row->getFill()->setFillType(Fill::FILL_SOLID)
                       ->setRotation(90)
                       ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
                       ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));

        // Cell #1
        $cell = $row->nextCell();
        $cell->setWidth(730);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
        $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

        // Cell #2
        $cell = $row->nextCell();
        $cell->setWidth(170);
        $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
        $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
        $cell->createTextRun('Fecha: '.date('d/m/Y'))->getFont()->setBold(FALSE)->setSize(14);
        $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	
		//////////////////////////////////////////////////////////
	
		// Agregar comentario inicial del reporte  
		$shape = $oSlide1->createTableShape(1);
		$shape->setOffsetX(30);
		$shape->setOffsetY(250);
	
		// Add row
		$row = $shape->createRow();
		$row->setHeight(300);
		$row->getFill()->setFillType(Fill::FILL_SOLID)
					   ->setRotation(90)
					   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
					   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
		$row->getFill()->setFillType(Fill::FILL_SOLID)
					   ->setRotation(90)
					   ->setStartColor(new StyleColor(StyleColor::COLOR_WHITE))
					   ->setEndColor(new StyleColor(StyleColor::COLOR_WHITE));
	
		// Cell #1
		$cell = $row->nextCell();
		$cell->setWidth(900);
		$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
		$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_TOP );
		$cell->createTextRun($coment_final)->getFont()->setBold(FALSE)->setSize(14);
		$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
		$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
		$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
		$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
	}
	
	//////////////////////////////////////////////////
    
    
    echo write($objPHPPresentation, 'InformeResumen', $writers);
		
	/////////////////////////////////////////////////////
	/////////        FIN PhpPresentation        /////////
	/////////////////////////////////////////////////////