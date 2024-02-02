<?php

header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");

// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();

// Incluir la clase de conexion
include_once 'connect.php';
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once 'cadenas.php';
$cadenas = new Cadenas();

// Capturar la fecha del reporte
$fecha = date('d/m/Y');

// Incluir libreria PDF
include '../MPDF6/mpdf.php';
$mpdf = new mPDF('L', 'LETTER', '', '', 8, 8, 30, 25, 5, 3); // 'L','LETTER','','',8,8,30,25,5,3  de los numeros 'margen left, margen righ, margen top, margen botton, 

// LOAD a stylesheet
$stylesheet = file_get_contents('../MPDF6/examples/mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no

////////////////////////////////////////////////
//// ENCABEZADO Y PIE DE LA PAGINA /////////////
////////////////////////////////////////////////

$header = '
            <table align="center" width="100%" height="150" style="border-bottom: 1px solid #000000;" cellspacing="0" cellpadding="0">
            <tr height="150">
               <td width="120"><img src="../../resources/images/logo/logo.png" align="top"></td>
               <td align="right">
                    <img src="../../resources/images/logo/UCM.png" align="top" width:"90" height="50">&nbsp;
                    <img src="../../resources/images/logo/bouygues.png" align="top" width:"60" height="50">
               </td>
            </tr>
            </table>
          ';

$footer = '
            <br>&nbsp;
            <table align="center" width="100%" style="border-top: 1px solid #000000;" cellspacing="0" cellpadding="0">
             <tr><td colspan="2">&nbsp;</td></tr>
             <tr>
                <td class="pie-title">Informe Garant&iacute;a</td>
                <td align="right" valign="middle">P&aacute;gina: {PAGENO}</td>
             </tr>
             <tr>
               <td colspan="2">Generado por:&nbsp;' . $cadenas->utf8($_SESSION['nombre'] . '&nbsp;' . $_SESSION['apellidos']) . '</td>
             </tr>
             <tr><td colspan="2">&nbsp;</td></tr>
            </table>
          ';

$footerE = '
            <br>&nbsp;
            <table align="center" width="100%" style="border-top: 1px solid #000000;" cellspacing="0" cellpadding="0">
             <tr><td colspan="2">&nbsp;</td></tr>
             <tr>
                <td class="pie-title">Informe Garant&iacute;a</td>
                <td align="right" valign="middle">P&aacute;gina: {PAGENO}</td>
             </tr>
             <tr>
               <td colspan="2">Generado por:&nbsp;' . $cadenas->utf8($_SESSION['nombre'] . '&nbsp;' . $_SESSION['apellidos']) . '</td>
             </tr>
             <tr><td colspan="2">&nbsp;</td></tr>
            </table>
          ';

$mpdf->SetHTMLHeader($header);
$mpdf->SetHTMLFooter($footer);
$mpdf->SetHTMLFooter($footerE, 'E');

////////////////////////////////////////////////
////   FIN ENCABEZADO Y PIE DE LA PAGINA    ////
////////////////////////////////////////////////


////////////////////////////////////////////////
//////////////      LISTADO      ///////////////
////////////////////////////////////////////////

// Obtener los parametros del Reporte
$sql = "SELECT nombre FROM gtia_dptos ORDER BY nombre ASC";
$query = $adoMSSQL_SEMTI->Execute($sql);

// Crear la Tabla del reporte			
$html = '
<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td class="title_document">Departamentos Responsables de las SD</td>
    <td width="200" align="right">Fecha: '.$fecha.'</td>
  </tr>
</table>
<table id="header_tabla" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td class="enc_tabla" width="40" align="center">No</td>
    <td class="enc_tabla">Nombre</td>
  </tr>';
$mpdf->WriteHTML($html, 2);

if ($query->RecordCount() >= 1) {

    $counter = 0;

    while (!$query->EOF) {

        $counter++;
        if($counter%2 != 0) $bg = '#FFFFFF'; else $bg = '#F1F1F1';
        $html = '  
                <tr bgcolor="'.$bg.'">
              	<td class="contenido" style="border-top:#999 1px solid;" align="center">' . $counter . '</td>
                  <td class="contenido" style="border-top:#999 1px solid;">' . utf8_encode($query->fields[0]) . '</td>
                </tr>
              ';
        $mpdf->WriteHTML($html, 2);
        $query->MoveNext();
    }
}

$html = ' 
  <tr>
    <td></td><td></td>
  </tr>
</table>
';
$mpdf->WriteHTML($html, 2);

/////////////////////////////////////////////
///////////      FIN LISTADO      ///////////
/////////////////////////////////////////////


$mpdf->Output('Dptos Garantia.pdf', 'I');
exit;
