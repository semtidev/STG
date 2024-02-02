<?php

header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir la clase de tratamiento de cadenas
include_once("../sistema/cadenas.php");
$cadenas = new Cadenas();

// Capturar la fecha del reporte
$fecha = date('d/m/Y');
$hoybd = date('Y-m-d');

// Incluir libreria PDF
include("../MPDF6/mpdf.php");
$mpdf = new mPDF('utf-8', 'LETTER-L', '', '', 6, 6, 32, 25, 5, 3, 'L');

// Cargar CSS
$stylesheet = file_get_contents('../MPDF6/examples/mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet, 1);

// Obtener los Parametros Generales
$polo   = ($_SESSION['polo_name'] != 'Todos') ? $_SESSION['polo_name'] : 'BBI SUCURSAL HABABA, CUBA';
$titulo = str_replace('+', ' ', $cadenas->utf8($_POST['titulo']));
$imagen = (string) str_replace('+', ' ', $cadenas->utf8($_POST['imagen']));


//// ENCABEZADO Y PIE DE LA PAGINA 
///////////////////////////////////////////////////////////////////////////

//$header = null;

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
                   <td class="pie-title" valign="top">Informe Garant&iacute;a | ' . $titulo . '</td>
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
                   <td class="pie-title" valign="top">Informe Garant&iacute;a | ' . $titulo . '</td>
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

//$mpdf->mirrorMargins = 1;	// Use different Odd/Even headers and footers and mirror margins

$mpdf->SetHTMLHeader($header);
//$mpdf->SetHTMLHeader($headerE,'E');
$mpdf->SetHTMLFooter($footer);
$mpdf->SetHTMLFooter($footerE,'E');

////   FIN ENCABEZADO Y PIE DE LA PAGINA 

// PORTADA
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
                    <td valign="middle" colspan="2" align="right"><div id="titulo">'.$titulo.'</div></td>
                </tr>
                <tr>
                    <td valign="middle" colspan="2" align="right">&nbsp;</td>
                </tr>
                <tr height="40">
                    <td align="right" class="fecha_document" style="border-right: #aaacad 1px solid; padding-right:20px;"><strong>Fecha:</strong> ' . $fecha . '</td>
                    <td width="160" valign="middle" align="right" class="garantia">Informe Garant&iacute;a</td>
                </tr>
            </table>
        </div>
        ';
$mpdf->WriteHTML($html, 2);
$mpdf->AddPage();

// FIN PORTADA


//  LISTADO     

$No = 0;
$condiciones = 0;
$validar_demora = true;

// Capturar los condiciones enviados
$comentario_inicio = str_replace('+', ' ', $cadenas->utf8($_POST['comentario_inicio']));
$comentario_final  = str_replace('+', ' ', $cadenas->utf8($_POST['comentario_final']));
$total_registros   = $_POST['total_registros'];

// Crear la Tabla del reporte			
$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
	  <tr>
	  	<td class="title_document">' . $titulo . '</td>'
             . '<td width="120" class="fecha_document"><strong>Total SD:</strong> ' . $total_registros . '</td>'
             . '<td width="130" class="fecha_document"><strong>Fecha:</strong> ' . $fecha . '</td>
	  </tr>
	</table>';
$mpdf->WriteHTML($html, 2);

if(strlen($comentario_inicio) > 1){
    
    $html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                      <td class="contenido" align="justify">'.$comentario_inicio.'</td>
                </tr>
            </table>';
    $mpdf->WriteHTML($html, 2);
}
        
if(isset($_POST['sdstore'])){
    
    $html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                    <td width="60" class="enc_tabla" align="center" style="border-right:#ffffff 1px solid">No</td>
                    <td class="enc_tabla" style="border-right:#ffffff 1px solid">Descripci&oacute;n</td>
                    <td width="150" class="enc_tabla" style="border-right:#ffffff 1px solid">Proyecto</td>
                    <td width="150" class="enc_tabla" style="border-right:#ffffff 1px solid">Objeto(s)</td>
                    <td width="100" class="enc_tabla" style="border-right:#ffffff 1px solid">Dpto</td>
                    <td width="70" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Fecha Reporte</td>
                    <td width="70" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Fecha Soluci&oacute;n</td>
                    <td width="55" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Demora (D&iacute;as)</td>
                    <td width="50" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Estado</td>
                    <td width="40" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Con.</td>
                    <td width="40" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">Sum.</td>
                    <td width="40" class="enc_tabla" style="text-align:center; border-right:#ffffff 1px solid">AEH</td>
              </tr>';
    $mpdf->WriteHTML($html, 2);

    /////////////////////////////////////////////////////////
    ///////        Construir el Listado de SD          //////
    /////////////////////////////////////////////////////////
    $records = json_decode(stripslashes($_POST['sdstore']));
    foreach ($records as $record) {

        $No++;

        $numero          = $record->numero;
        $descripcion     = $cadenas->codificarBD_utf8($record->descripcion);
        $proyecto        = $cadenas->codificarBD_utf8($record->proyecto);
        $objeto          = $cadenas->codificarBD_utf8($record->objeto);
        $dpto            = $cadenas->codificarBD_utf8($record->dpto);
        $array_fecha_rep = explode('-',substr($record->fecha_reporte,0,10));
        $fecha_rep       = $array_fecha_rep[2].'/'.$array_fecha_rep[1].'/'.$array_fecha_rep[0];
        $array_fecha_sol = explode('-',substr($record->fecha_solucion,0,10));
        if(count($array_fecha_sol) > 1){ 
            $fecha_sol   = $array_fecha_sol[2].'/'.$array_fecha_sol[1].'/'.$array_fecha_sol[0]; 
        }
        else{ 
            $fecha_sol   = ''; 
        }
        $demora          = $record->demora; 
        $estado          = $record->estado;
        if($record->constructiva == 1){
            $contructiva = 'Si';
        }
        else{
            $contructiva = 'No';
        }
        if($record->suministro == 1){
            $suministro = 'Si';
        }
        else{
            $suministro = 'No';
        }
        if($record->afecta_explotacion == 1){
            $afecta_explotacion = 'Si';
        }
        else{
            $afecta_explotacion = 'No';
        }

        if($No == 1) $colorline = '606163'; else $colorline = 'c0c1c1';

        $html = '  
            <tr>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$numero.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.$descripcion.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($proyecto).'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($objeto).'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.utf8_encode($dpto).'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.$fecha_rep.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;">'.$fecha_sol.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$demora.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$estado.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$contructiva.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid; border-right:#e5e6e7 1px solid;" align="center">'.$suministro.'</td>
                  <td class="contenido" style="border-top:#'.$colorline.' 1px solid;" align="center">'.$afecta_explotacion.'</td>
            </tr>
        ';
        $mpdf->WriteHTML($html,2);

    }
    //////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////

    $html = '
              <tr>
                    <td style="border-top:#606163 1px solid;" colspan="12"></td>
              </tr>
            </table><br>
            ';
    $mpdf->WriteHTML($html, 2);

    /////////////////////////////////////////////
    ///////////      FIN LISTADO      ///////////
    /////////////////////////////////////////////
}

if(strlen($comentario_final) > 1){
    
    $html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                      <td class="contenido" align="justify">'.$comentario_final.'</td>
                </tr>
            </table>';
    $mpdf->WriteHTML($html, 2);
}

$mpdf->Output($titulo . ' ' . $fecha . '.pdf', 'I');
exit;

