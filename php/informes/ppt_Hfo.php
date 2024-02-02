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
    $id_informe = $_GET['id'];
    $storedata  = $_GET['datastore'];
    
    // Obtener los Parametros Generales
    $polo = ($_SESSION['polo_name'] != 'Todos') ? $_SESSION['polo_name'] : 'BBI SUCURSAL HABABA, CUBA';
    
    // Obtener los datos iniciales del informe
    $sql_infoHfo = "SELECT
                        info_hfo.titulo,
                        info_hfo.proyecto,
                        info_hfo.zona,
                        info_hfo.objeto,
                        info_hfo.desde,
                        info_hfo.hasta,
                        gtia_proyectos.imagen
                    FROM
                        info_hfo,
                        gtia_proyectos
                    WHERE
                        info_hfo.id = $id_informe AND
                        info_hfo.proyecto = gtia_proyectos.nombre";
    
    $qry_infoHfo = $adoMSSQL_SEMTI->Execute($sql_infoHfo);
    
    $titulo   = $qry_infoHfo->fields[0];
    $proyecto = $qry_infoHfo->fields[1];
    $zona     = $qry_infoHfo->fields[2];
    $objeto   = $qry_infoHfo->fields[3];
    $desde    = $qry_infoHfo->fields[4];
    $hasta    = $qry_infoHfo->fields[5];
    $imagen   = $qry_infoHfo->fields[6];
    
    include_once 'ppt_headerHfo.php';
    
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
                                      ->setTitle('Informe Garantia HFO')
                                      ->setSubject('Informe de HFO')
                                      ->setDescription('Informe de HFO')
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
    
    
    //////////////////////////////////////////////////////////
    ///////        Construir el Listado de HFO          //////
    //////////////////////////////////////////////////////////
    $No = 0;    
    
    $records = json_decode($cadenas->codificarBD_utf8(stripslashes($storedata)));
    $total   = count($records);
    
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
    
        if($No == 1){ $colorline = '606163'; }
        else{ $colorline = 'c0c1c1'; }
    
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
            $shape->setOffsetX(50);
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
            $cell->setWidth(690);
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
            $shape->setOffsetX(50);
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
            $cell->setWidth(250)->createTextRun('HABITACIONES')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
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
                    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                    //$row->nextCell()->createTextRun($problema[$j])->getFont()->setSize(12);
                    if($observaciones[$j] == '') $observaciones[$j] = " ";
                    $cell = $row->nextCell();
                    $cell->createTextRun($observaciones[$j])->getFont()->setSize(12);
                    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
            }
            
            // Numero pagina
            $numpag = $i + 1;
                        
            $shape = $oSlide->createTableShape(2);
            $shape->setOffsetX(50);
            $shape->setOffsetY(650);
    
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
            $cell->setWidth(690)->createTextRun('Página '.$numpag.' | '.$problema[$j-1])->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
      
            // Cell #2
            $cell = $row->nextCell();
            $cell->setWidth(170)->createTextRun('CCO Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
    }
    ////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////
    
    
    /////////////////////////////////////////////
    ///////////      FIN LISTADO      ///////////
    /////////////////////////////////////////////
    
    echo write($objPHPPresentation, 'InformeHfo', $writers);
    
    /////////////////////////////////////////////////////
	/////////        FIN PhpPresentation        /////////
	/////////////////////////////////////////////////////