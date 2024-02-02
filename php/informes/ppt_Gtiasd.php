<?php
// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir la clase de tratamiento de cadenas
include_once("../sistema/cadenas.php");
$cadenas = new Cadenas();

$polo = ($_SESSION['polo_name'] != 'Todos') ? $_SESSION['polo_name'] : 'BBI SUCURSAL HABABA, CUBA';

include_once 'ppt_headerGtiasd.php';

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
                                  ->setTitle('Informe Garantia')
                                  ->setSubject('Informe de SD')
                                  ->setDescription('Informe de SD')
                                  ->setKeywords('office 2007 openxml libreoffice odt php')
                                  ->setCategory('Informes Garantia');

// Params Send
$titulo = str_replace('+', ' ', $cadenas->utf8($_POST['titulo']));
$imagen = (string) str_replace('+', ' ', $cadenas->utf8($_POST['imagen']));
$total_registros = $_POST['total_registros'];

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
$shape->setOffsetY(500);
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
$cell->setWidth(30);
$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

// Cell #2
$cell = $row->nextCell();
$cell->setWidth(200);
$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_BOTTOM );
$cell->createTextRun('Fecha: '.date('d/m/Y'))->getFont()->setBold(false)->setSize(14);
$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

// Cell #3
$cell = $row->nextCell();
$cell->setWidth(450);
$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT )->setMarginRight(10);
$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_BOTTOM );
$cell->createTextRun($polo)->getFont()->setBold(false)->setSize(16);
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
$cell->setWidth(30);
$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

// Cell #2
$cell = $row->nextCell();
$cell->setWidth(650);
$cell->setColSpan(2);
$cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT )->setMarginRight(10);
$cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_TOP );
$cell->createTextRun($titulo)->getFont()->setName('Arial')->setBold(true)->setSize(22);
$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);


////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////


$No = 0;
$condiciones = 0;
$validar_demora = true;

// Capturar los condiciones enviados
$comentario_inicio = str_replace('+', ' ', $cadenas->utf8($_POST['comentario_inicio']));
$comentario_final  = str_replace('+', ' ', $cadenas->utf8($_POST['comentario_final']));


// SLIDE COMENTARIO INICIAL
if(strlen($comentario_inicio) > 1){
   
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
    $shape = $oSlide1->createTableShape(3);
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
    $cell->setWidth(540);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
    $cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

    // Cell #2
    $cell = $row->nextCell();
    $cell->setWidth(150);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
    $cell->createTextRun('Total: '.$total_registros)->getFont()->setBold(FALSE)->setSize(14);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
    
    // Cell #3
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
    $shape->setOffsetX(50);
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
    $cell->setWidth(860);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_TOP );
    $cell->createTextRun($comentario_inicio)->getFont()->setBold(FALSE)->setSize(14);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
}

//////////////////////////////////////////////////
        

/////////////////////////////////////////////////////////
///////        Construir el Listado de SD          //////
/////////////////////////////////////////////////////////
if(isset($_POST['sdstore'])){
    

    $records = json_decode($cadenas->codificarBD_utf8(stripslashes($_POST['sdstore'])));
    $total   = count($records);

    $numero             = array();
    $descripcion        = array();
    $proyecto           = array();
    $objeto             = array();
    $dpto               = array();
    $fecha_rep          = array();
    $fecha_sol          = array();
    $demora             = array();
    $estado             = array();
    $contructiva        = array();
    $suministro         = array();
    $afecta_explotacion = array();

    foreach ($records as $record) {

        $No++;

        $numero[]          = $record->numero;
        $descripcion[]     = $record->descripcion;
        $proyecto[]        = $record->proyecto;
        $objeto[]          = $record->objeto;
        $dpto[]            = $record->dpto;
        $array_fecha_rep   = explode('-',substr($record->fecha_reporte,0,10));
        $fecha_rep[]       = $array_fecha_rep[2].'/'.$array_fecha_rep[1].'/'.$array_fecha_rep[0];
        $array_fecha_sol   = explode('-',substr($record->fecha_solucion,0,10));
        $demora[]          = $record->demora; 
        $estado[]          = $record->estado;

        if(count($array_fecha_sol) > 1){ $fecha_sol[] = $array_fecha_sol[2].'/'.$array_fecha_sol[1].'/'.$array_fecha_sol[0]; }
        else{ $fecha_sol[] = ''; }

        if($record->constructiva == 1){ $contructiva[] = 'Si'; }
        else{ $contructiva[] = 'No'; }

        if($record->suministro == 1){ $suministro[] = 'Si'; }
        else{ $suministro[] = 'No'; }

        if($record->afecta_explotacion == 1){ $afecta_explotacion[] = 'Si'; }
        else{ $afecta_explotacion[] = 'No'; }

        if($No == 1){ $colorline = '606163'; }
        else{ $colorline = 'c0c1c1'; }

    }
    //////////////////////////////////////////////////////////

    // Crear paginas de presentacion
    $reg_pagina = 5;
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
            $shape = $oSlide->createTableShape(3);
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
            $cell->setWidth(540);
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
            $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

            // Cell #2
            $cell = $row->nextCell();
            $cell->setWidth(150);
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $cell->createTextRun('Total: '.$total_registros)->getFont()->setBold(FALSE)->setSize(14);
            $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

            // Cell #3
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
            $shape = $oSlide->createTableShape(5);
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
            $cell->setWidth(70)->createTextRun('NO')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $cell = $row->nextCell();
            $cell->setWidth(300)->createTextRun('DESCRIPCIÓN')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $cell = $row->nextCell();
            $cell->setWidth(150)->createTextRun('PROYECTO')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $cell = $row->nextCell();
            $cell->setWidth(190)->createTextRun('OBJETO')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $cell = $row->nextCell();
            $cell->setWidth(150)->createTextRun('DEPARTAMENTO')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
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
                    $cell->createTextRun($numero[$j])->getFont()->setSize(14);
                    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
                    $row->nextCell()->createTextRun($descripcion[$j])->getFont()->setSize(14);
                    $row->nextCell()->createTextRun($proyecto[$j])->getFont()->setSize(14);
                    if(strlen($objeto[$j]) > 15) $objeto[$j] = substr ($objeto[$j],0,15)."...";
                    $row->nextCell()->createTextRun($objeto[$j])->getFont()->setSize(14);
                    $row->nextCell()->createTextRun($dpto[$j])->getFont()->setSize(14);
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
            $cell->setWidth(690)->createTextRun('Página '.$numpag)->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            //$cell->createTextRun('Página '.$numpag)->getFont()->setBold(TRUE)->setSize(20);
            //$cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
            //$cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
            //$cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
            //$cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
      
            // Cell #2
            $cell = $row->nextCell();
            $cell->setWidth(170)->createTextRun('CCO Garantía')->getFont()->setBold(true)->setSize(14)->setColor(new StyleColor('FFFFFFFF'));
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            /*$cell->setWidth(170);
            $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
            $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $cell->createTextRun('CCO Garantía')->getFont()->setBold(FALSE)->setSize(14);
            $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
            $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);*/
    }
}
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////


// SLIDE COMENTARIO FINAL
if(strlen($comentario_final) > 1){
   
    $oSlide_end = $objPHPPresentation->createSlide();
    $oSlide_end->setTransition($oTransition);

    // Slide > Background > Color
    //$oBkgColor = new Color();
    //$oBkgColor->setColor(new StyleColor(StyleColor::COLOR_WHITE));
    $oSlide_end->setBackground($oBkgImage);

    // Creating Top Layer
    $shape = new Drawing();
    $shape->setName('UCM logo')
          ->setDescription('UCM logo')
          ->setPath('../../resources/images/logo/ppt_top.png')
          ->setHeight(90)
          ->setWidth(600)
          ->setOffsetX(362)
          ->setOffsetY(30);
    $oSlide_end->addShape($shape);

    // Crear la Tabla Superior del reporte   
    $shape = $oSlide_end->createTableShape(3);
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
    $cell->setWidth(540);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
    $cell->createTextRun($titulo)->getFont()->setBold(TRUE)->setSize(20);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);

    // Cell #2
    $cell = $row->nextCell();
    $cell->setWidth(150);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
    $cell->createTextRun('Total: '.$total_registros)->getFont()->setBold(FALSE)->setSize(14);
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

    // Agregar comentario final del reporte  
    $shape = $oSlide_end->createTableShape(1);
    $shape->setOffsetX(50);
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
    $cell->setWidth(860);
    $cell->getParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_JUSTIFY );
    $cell->getParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_TOP );
    $cell->createTextRun($comentario_final)->getFont()->setBold(FALSE)->setSize(14);
    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
}

//////////////////////////////////////////////////

/////////////////////////////////////////////
///////////      FIN LISTADO      ///////////
/////////////////////////////////////////////


echo write($objPHPPresentation, 'InformeSD', $writers);