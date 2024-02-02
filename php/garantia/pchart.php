<?php
//////////////////////////////////////////
/////////      pCHART 2.1.4      /////////
//////////////////////////////////////////

// Include all the classes 
 include("../pChart214/class/pDraw.class.php"); 
 include("../pChart214/class/pImage.class.php"); 
 include("../pChart214/class/pData.class.php");

 // Create your dataset object
 $myData = new pData(); 
 
 // Add data in your dataset
 $myData->addPoints(array(1,3,4,3,5));

 // Create a pChart object and associate your dataset
 $myPicture = new pImage(600,300,$myData);

 // Choose a nice font
 $myPicture->setFontProperties(array("FontName"=>"../pChart214/fonts/Forgotte.ttf","FontSize"=>11));

 // Define the boundaries of the graph area
 $myPicture->setGraphArea(50,50,550,250);

 // Draw the scale, keep everything automatic
 $myPicture->drawScale();

 // Draw the scale, keep everything automatic
 $myPicture->drawSplineChart();

 // Crear la imagen que será renderizada
 if(copy('../../resources/images/rendercharts/rendertemplate.png','../../resources/images/rendercharts/mychart.png')){
 
	 // Render the picture (choose the best way)
	 //$MyPicture->Stroke();
	 //$myPicture->autoOutput("../pChart214/examples/pictures/example.basic.png");
	 //header("Content-Type: image/png");
	 $myPicture->Render("../../resources/images/rendercharts/mychart.png");
 
 }

////////////////////////////////////////////////
////////////////////////////////////////////////
?>