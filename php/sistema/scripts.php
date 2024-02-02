<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once("connect.php");
$connect = new Connect();

// Llamar la funcion que conecta a la BD
$connect->connMSSQL_SEMTI();

// Incluir la clase de tratamiento de cadenas
include_once("cadenas.php");
$cadenas = new Cadenas();


/*
 *  Quitar la palabra habitacion del listado de SD
 * 
  $adoMYSQL_SEMTI->Execute('SET NAMES UTF8');

  $sql = "SELECT * FROM gtia_sd WHERE local LIKE'%Habitación%'";
  $qry = $adoMYSQL_SEMTI->Execute($sql);

  while ($result = $qry->FetchRow()){

  $habit_array = explode(' ',$result['local']);

  if(count($habit_array) == 2){
  $newvalue = $habit_array[1];
  if($adoMYSQL_SEMTI->Execute("UPDATE gtia_sd SET local = '$newvalue' WHERE id = ".$result['id'])){
  echo $result['id'].", ".$newvalue."<br/>";
  }
  }
  }
 */



/*
 *  Importar para la tabla de SD_PARTES las habitaciones individuales
 *
  $adoMYSQL_SEMTI->Execute('SET NAMES UTF8');

  $sql   = "SELECT * FROM gtia_sd";
  $qry   = $adoMYSQL_SEMTI->Execute($sql);
  $count = 0;

  while ($result = $qry->FetchRow()){

  $local  = $result['local'];
  $objeto = $result['objeto'];

  if(is_numeric($local)){

  $count++;
  $id_sd = $result['id'];

  $sql_local = "SELECT cco_partes.id AS id_local FROM cco_objetos, cco_partes WHERE cco_partes.nombre = '$local' AND cco_partes.id_objeto = cco_objetos.id AND cco_objetos.nombre = '$objeto'";
  $qry_local = $adoMYSQL_SEMTI->Execute($sql_local);
  $tot_local = $qry_local->RecordCount();

  if($tot_local > 0){

  $res_local = $qry_local->FetchRow();
  $id_local  = $res_local['id_local'];

  $adoMYSQL_SEMTI->Execute("INSERT INTO gtia_sd_partes(id_parte,id_sd) VALUES($id_local,$id_sd)");
  $adoMYSQL_SEMTI->Execute("UPDATE gtia_sd SET modificado = 'ScriptHabit' WHERE id = $id_sd");
  }
  else{
  echo $result['id'].", ".$result['objeto'].", ".$local."<br/>";
  }
  }
  }
  echo "TOTAL: ".$count."<br/>";
 * 
 */


/*
 *  Contador de SD
 *
  $sql   = "SELECT * FROM gtia_sd";
  $qry   = $adoMYSQL_SEMTI->Execute($sql);
  $count = 0;

  while ($result = $qry->FetchRow()){
  $count++;
  }

  echo "TOTAL: ".$count;
 * 
 */


/*
 *  Importar las habitaciones de un BW
 *
  $string_sd = "19212, 23312";
  $array_sd  = explode(", ", $string_sd);
  $id_sd     = 1303;
  $contador  = 0;

  for($i = 0; $i < count($array_sd); $i++){

  $qry_local = $adoMYSQL_SEMTI->Execute("SELECT id FROM cco_partes WHERE nombre = '".$array_sd[$i]."'");
  $res_local = $qry_local->FetchRow();
  $id_local  = $res_local['id'];
  if($adoMYSQL_SEMTI->Execute("INSERT INTO gtia_sd_partes(id_parte,id_sd) VALUES($id_local,$id_sd)")){
  $contador++;
  }
  else{
  echo $array_sd[$i]."<br/>";
  }
  }
  echo "Total: ".$contador;
 * 
 */


/*
 *  Importar BW
 *
  $contador = 0;
  for($i=993; $i < 997; $i++){
  if($adoMYSQL_SEMTI->Execute("INSERT INTO gtia_sd_objetos(id_objeto,id_sd) VALUES(31,$i)")){
  $contador++;
  }
  else{
  echo $i."<br/>";
  }
  }
  echo "Total: ".$contador;
 * 
 */


/*
 *    Traer los objetos al listado de SD
  /
  $count = 0;
  $sql   = "SELECT id FROM gtia_sd";
  $qry   = $adoMYSQL_SEMTI->Execute($sql);

  while ($result = $qry->FetchRow()){

  $id_sd  = $result['id'];

  // Importar los objetos
  $sql_object   = "SELECT cco_objetos.nombre AS objeto FROM gtia_sd_objetos,cco_objetos WHERE gtia_sd_objetos.id_sd = $id_sd AND gtia_sd_objetos.id_objeto = cco_objetos.id";
  $qry_object   = $adoMYSQL_SEMTI->Execute($sql_object);
  $ctdad_object = 0;
  $objeto_local = "";

  if($qry_object->RecordCount() > 0){

  $count++;
  while($result_object = $qry_object->FetchRow()){

  $ctdad_object++;
  if($ctdad_object == 1){
  $objeto_local = $result_object['objeto'];
  }
  else{
  $objeto_local .= ", ".$result_object['objeto'];
  }
  }

  $sd_objetos[] = $id_sd."*".$objeto_local;
  }
  }

  for($i = 0; $i < count($sd_objetos); $i++){

  $array_content = explode('*', $sd_objetos[$i]);
  $id  = $array_content[0];
  $obj = $array_content[1];
  $adoMYSQL_SEMTI->Execute("UPDATE gtia_sd SET objeto_local = '$obj' WHERE id = $id");
  }

  echo "TOTAL SD CON OBJETOS: ".  count($sd_objetos);
 */


/*
 *    Traer los locales al listado de SD
  /
  $count = 0;
  $sql   = "SELECT id,objeto_local FROM gtia_sd";
  $qry   = $adoMYSQL_SEMTI->Execute($sql);

  while ($result = $qry->FetchRow()){

  $id_sd  = $result['id'];
  $object = $result['objeto_local'];

  // Importar los locales
  $sql_part   = "SELECT cco_objetos.nombre AS objeto, cco_partes.nombre AS parte FROM gtia_sd_partes,cco_partes,cco_objetos WHERE gtia_sd_partes.id_sd = $id_sd AND gtia_sd_partes.id_parte = cco_partes.id AND cco_partes.id_objeto = cco_objetos.id";
  $qry_part   = $adoMYSQL_SEMTI->Execute($sql_part);
  $ctdad_partes = 0;
  $objeto_local = "";

  if($qry_part->RecordCount() > 0){

  $count++;
  while($result_part = $qry_part->FetchRow()){

  $ctdad_partes++;
  if($ctdad_partes == 1 && strlen($object)== 0){
  $objeto_local = $result_part['objeto']." (".$result_part['parte'].")";
  }
  else{
  $objeto_local .= ", ".$result_part['objeto']." (".$result_part['parte'].")";
  }
  }

  $sd_objetos[] = $id_sd."*".$object.$objeto_local;
  }
  }

  for($i = 0; $i < count($sd_objetos); $i++){

  $array_content = explode('*', $sd_objetos[$i]);
  $id  = $array_content[0];
  $obj = $array_content[1];
  //echo $id." * ".$obj."<br>";
  $adoMYSQL_SEMTI->Execute("UPDATE gtia_sd SET objeto_local = '$obj' WHERE id = $id");
  }

  echo "TOTAL SD CON LOCALES: ".  count($sd_objetos);
 */


/*
 *    Traer la zona al listado de SD
  /
  $sql   = "SELECT id FROM gtia_sd";
  $qry   = $adoMYSQL_SEMTI->Execute($sql);

  while ($result = $qry->FetchRow()){

  $id_sd  = $result['id'];

  // Importar las zonas de los objetos
  $sql_zonas = "SELECT cco_zonas.nombre AS zona FROM gtia_sd_objetos,cco_objetos,cco_zonas WHERE gtia_sd_objetos.id_sd = $id_sd AND gtia_sd_objetos.id_objeto = cco_objetos.id AND cco_objetos.id_zona = cco_zonas.id";
  $qry_zonas = $adoMYSQL_SEMTI->Execute($sql_zonas);

  if($qry_zonas->RecordCount() > 0){

  $result_zonas = $qry_zonas->FetchRow();
  $sd_zonas[] = $id_sd."*".$result_zonas['zona'];
  }
  else{

  $sql_zonas = "SELECT cco_zonas.nombre AS zona FROM gtia_sd_partes,cco_partes,cco_objetos,cco_zonas WHERE gtia_sd_partes.id_sd = $id_sd AND gtia_sd_partes.id_parte = cco_partes.id AND cco_partes.id_objeto = cco_objetos.id AND cco_objetos.id_zona = cco_zonas.id";
  $qry_zonas = $adoMYSQL_SEMTI->Execute($sql_zonas);

  if($qry_zonas->RecordCount() > 0){

  $result_zonas = $qry_zonas->FetchRow();
  $sd_zonas[] = $id_sd."*".$result_zonas['zona'];
  }
  }
  }

  for($i = 0; $i < count($sd_zonas); $i++){

  $array_content = explode('*', $sd_zonas[$i]);
  $id   = $array_content[0];
  $zona = $array_content[1];
  //echo $id." * ".$zona."<br>";
  $adoMYSQL_SEMTI->Execute("UPDATE gtia_sd SET zona = '$zona' WHERE id = $id");
  }

  echo "TOTAL SD CON LOCALES: ".  count($sd_zonas);
 * 
 */


/*
 *    Traer el proyecto al listado de SD
  /
  $sql   = "SELECT id FROM gtia_sd";
  $qry   = $adoMYSQL_SEMTI->Execute($sql);

  while ($result = $qry->FetchRow()){

  $id_sd  = $result['id'];

  // Importar las zonas de los objetos
  $sql_proyecto = "SELECT cco_proyectos.nombre AS proyecto FROM gtia_sd_objetos,cco_objetos,cco_zonas,cco_proyectos WHERE gtia_sd_objetos.id_sd = $id_sd AND gtia_sd_objetos.id_objeto = cco_objetos.id AND cco_objetos.id_zona = cco_zonas.id AND cco_zonas.id_proyecto = cco_proyectos.id";
  $qry_proyecto = $adoMYSQL_SEMTI->Execute($sql_proyecto);

  if($qry_proyecto->RecordCount() > 0){

  $result_proyecto = $qry_proyecto->FetchRow();
  $sd_proyecto[] = $id_sd."*".$result_proyecto['proyecto'];
  }
  else{

  $sql_proyecto = "SELECT cco_proyectos.nombre AS proyecto FROM gtia_sd_partes,cco_partes,cco_objetos,cco_zonas,cco_proyectos WHERE gtia_sd_partes.id_sd = $id_sd AND gtia_sd_partes.id_parte = cco_partes.id AND cco_partes.id_objeto = cco_objetos.id AND cco_objetos.id_zona = cco_zonas.id AND cco_zonas.id_proyecto = cco_proyectos.id";
  $qry_proyecto = $adoMYSQL_SEMTI->Execute($sql_proyecto);

  if($qry_proyecto->RecordCount() > 0){

  $result_proyecto = $qry_proyecto->FetchRow();
  $sd_proyecto[] = $id_sd."*".$result_proyecto['proyecto'];
  }
  }
  }

  for($i = 0; $i < count($sd_proyecto); $i++){

  $array_content = explode('*', $sd_proyecto[$i]);
  $id       = $array_content[0];
  $proyecto = $array_content[1];
  //echo $id." * ".$proyecto."<br>";
  $adoMYSQL_SEMTI->Execute("UPDATE gtia_sd SET proyecto = '$proyecto' WHERE id = $id");
  }

  echo "TOTAL SD CON LOCALES: ".  count($sd_proyecto);
 * 
 */


/*
 *  Asignar SD AEH
 *
$count   = 0;
$sql_aeh = "SELECT * FROM sd_aeh";
$qry_aeh = $adoMYSQL_SEMTI->Execute($sql_aeh);
while($res_aeh = $qry_aeh->FetchRow()){
    $numero = $res_aeh['numero'];
    if($adoMYSQL_SEMTI->Execute("UPDATE gtia_sd SET afecta_explotacion = 1 WHERE numero = $numero")){
       $count++; 
    }
}
echo $count;
 * 
 */

 
 /*
 *  Asignar DPTO a SD
 *
$count   = 0;
$sql = "SELECT id FROM gtia_sd WHERE id_dpto = 4";
$qry = $adoMSSQL_SEMTI->Execute($sql);
while(!$qry->EOF){
    $count++;
    $id_sd = $qry->fields[0];
    $adoMSSQL_SEMTI->Execute("INSERT INTO gtia_sd_dpto(id_sd,id_dpto) VALUES($id_sd,4)");
    $qry->MoveNext();
}
echo $count;
 * 
 */
 
 //$pass = md5('webmaster');
 //$adoMSSQL_SEMTI->Execute("UPDATE syst_usuarios SET contrasena = '$pass' WHERE id_usuario = 18");
 
 
 /* Ordenar vectores  
    $arrayasociativo = array("Juan"=>"29","Pedro"=>"18","Eduardo"=>"26");
     
    //orden ascendente
    asort($arrayasociativo);
    var_export($arrayasociativo);
     
    //orden descendente
    arsort($arrayasociativo);
    var_export($arrayasociativo);*/
 
 /*
 *  Poner estado en la tabla gtia_sd_partes
 *
$count = 0;
$sql = "SELECT id FROM gtia_sd WHERE estado = 'No Procede'";
$qry = $adoMSSQL_SEMTI->Execute($sql);
while(!$qry->EOF){
    $id_sd = $qry->fields[0];
    $sql_local = "SELECT id_sd FROM gtia_sd_partes WHERE id_sd = $id_sd AND estado IS NULL";
    $qry_local = $adoMSSQL_SEMTI->Execute($sql_local);
    if($qry_local->RecordCount() > 0){
        
        if($adoMSSQL_SEMTI->Execute("UPDATE gtia_sd_partes SET estado = 'No Procede' WHERE id_sd = $id_sd AND estado IS NULL"))
        $count++;
    }    
    $qry->MoveNext();
}
echo $count;
 * 
 */
 
 
/*/  Señalar habitaciones en la tabla gtia_partes

$count = 0;
$sql = "SELECT id,nombre FROM gtia_partes";
$qry = $adoMSSQL_SEMTI->Execute($sql);
while(!$qry->EOF){
    $id_parte = $qry->fields[0];
    if(is_numeric($qry->fields[1])){
        $adoMSSQL_SEMTI->Execute("UPDATE gtia_partes SET habit = 1 WHERE id = $id_parte");
        $count++;
    }else{
        $adoMSSQL_SEMTI->Execute("UPDATE gtia_partes SET habit = 0 WHERE id = $id_parte");
    }
    $qry->MoveNext();
}
echo $count;*/

//  IMAGENES
//-----------------------------
/*function imagecreatefromimg ($flotaImage) {
	$tipo = getimagesize($flotaImage);
	switch ($tipo['mime']) {
		case "image/jpeg":
		$finalImage = imagecreatefromjpeg($flotaImage);
		break;
		case "image/png":
		$finalImage = imagecreatefrompng($flotaImage);
		break;
		case "image/gif":
		$finalImage = imagecreatefromgif($flotaImage);
		break;
		default:
		die("Tipo de imagen no válida.");
		break;
	}
	return $finalImage;
}
$imagen = imagecreatefromimg($_FILES['imagen']['tmp_name']);
$directorioImagen = 'imagenes/'.$_FILES['imagen']['name'].'.jpg';
imagejpeg($imagen, $directorioImagen); //guardamos la imagen en el directorio imagenes.
imagedestroy($imagen);*/
