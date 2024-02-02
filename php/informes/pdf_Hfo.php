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

// Incluir libreria PDF
include("../MPDF6/mpdf.php");
$mpdf = new mPDF('utf-8', 'LETTER-L', '', '', 6, 6, 32, 25, 5, 3, 'L');

// Cargar CSS
$stylesheet = file_get_contents('../MPDF6/examples/mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet, 1);

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

///////////////////////////////////////////////////
/////          LISTADO     
///////////////////////////////////////////////////

// Crear la Tabla del reporte			
$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="title_document">' . $titulo . '</td>';
    if ($desde != '1900-01-01' && $desde != '' && $desde != null) {
        $html .= '<td width="150"><strong>DESDE:</strong> ' .$desde. '</td>';
    }
    if ($hasta != '1900-01-01' && $hasta != '' && $hasta != null) {                
        $html .= '<td width="150"><strong>HASTA:</strong> ' .$hasta. '</td>';
    }
$html .= '</tr>
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
            <tr height="30">
                <td class="title_content">Bungalows:</td>
                <td class="title_content"> ' . $objeto . '</td>
            </tr>
        </table>';
$mpdf->WriteHTML($html, 2);

        
$html = '<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="100" class="enc_tabla" style="border-right:#ffffff 1px solid">SD</td>
                <td class="enc_tabla" style="border-right:#ffffff 1px solid">Habitaciones</td>
                <td width="80" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">Ctdad Habitac.</td>
                <td width="80" class="enc_tabla" style="text-align: center; border-right:#ffffff 1px solid">Habitac. Pendientes</td>
                <td width="150" class="enc_tabla" style="border-right:#ffffff 1px solid">Causa</td>
                <td width="250" class="enc_tabla" style="border-right:#ffffff 1px solid">Comentario</td>
            </tr>';
$mpdf->WriteHTML($html, 2);

///////////////////////////////////////////////////
///////        Construir el Listado          //////
///////////////////////////////////////////////////
$No = 0;
$records = json_decode(stripslashes($storedata));
foreach ($records as $record) {

    $No++;

    $sd            = $record->sd;
    $habitaciones  = $record->habitaciones;
    $ctdad_habit   = $record->ctdad_habit;
    $pendientes    = $record->pendientes;
    $problema      = $cadenas->codificarBD_utf8($record->problema);
    $observaciones = $cadenas->codificarBD_utf8($record->observaciones);

    if($No == 1) $colorline = '606163'; else $colorline = 'c0c1c1';

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
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

$html = '
        <tr>
            <td style="border-top:#606163 1px solid;" colspan="6"></td>
        </tr>
    </table><br>
    ';
$mpdf->WriteHTML($html, 2);

/////////////////////////////////////////////
///////////      FIN LISTADO      ///////////
/////////////////////////////////////////////



$mpdf->Output($titulo . ' ' . $fecha . '.pdf', 'I');
exit;

