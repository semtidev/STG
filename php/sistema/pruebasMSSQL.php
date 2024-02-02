<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'connect.php';

$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Consultas comunes
/* $sql = "SELECT * FROM syst_usuarios ORDER BY nombre ASC"; 

  // Ejecutar la consulta en la BD
  if($query = $adoMSSQL_SEMTI->Execute($sql)){
  while (!$query->EOF) {
  print $query->fields[0].' '.$query->fields[1].'<BR>';
  $query->MoveNext();
  }

  $query->Close(); # opcional
  } */

// Consultas a Procedimientos almacenados
/*$PageNumber = 1;
$PageSize = 5;

$stmt = $adoMSSQL_SEMTI->PrepareSP('Paginado_Usuarios'); 
# ¡Observa que el nombre de parametro no tiene @ al principio!
$adoMSSQL_SEMTI->InParameter($stmt,$PageNumber,'PageNumber');
$adoMSSQL_SEMTI->InParameter($stmt,$PageSize,'PageSize');
# El valor de salida en mssql, RETVAL, es un nombre fijo 
//$db->OutParameter($stmt,$ret,'RETVAL');
$query = $adoMSSQL_SEMTI->Execute($stmt);
while(!$query->EOF){
    echo $query->fields[1].'<br>';
    $query->MoveNext();
}
$cargo = 'erf';*/



//$query = $adoMSSQL_SEMTI->Execute('Syst_usuarios_Login admin');
//while(!$query->EOF){
    //echo $query->fields[6];
    //$query->MoveNext();
//}



// Inicializar la codificacion utf8 en la BD
//$GLOBALS["adoMSSQL_SEMTI"]->Execute('SET NAMES UTF8');
$qry = $adoMSSQL_SEMTI->Execute("SELECT * FROM gtia_sd WHERE descripcion LIKE '%filtracion%' COLLATE Modern_Spanish_CI_AI");

echo $qry->RecordCount();
