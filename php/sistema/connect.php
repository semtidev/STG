<?php	
///////////////////////  LIBRERIA ADODB5  /////////////////////////
include_once("../adodb519/adodb.inc.php");
//$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;


/////////////  CONEXIONES ADO A SGBD  ////////////
$adoMYSQL_SEMTI = ADONewConnection('mysqlt');
$adoMSSQL_SEMTI = ADONewConnection('odbc_mssql');


/////////////////////////////////////////////////////
////////////////    CLASE CONNEX     ////////////////
/////////////////////////////////////////////////////
class Connect
{
	/////////////////////////////////////////
	//////////////  Atributos  //////////////
	/////////////////////////////////////////
	
	// Variables MySQL
	var $my_host =  'localhost';
	var $my_user =  'root';
	var $my_pass =  'webmaster';
	var $my_db   =  'cayococo';
        
    // Variables MSSQL
	var $sql_dsn  = 'Driver={SQL Server};Server=localhost;Database=Garantia;';
	var $sql_user = 'sa';
	var $sql_pass = 'webmaster';
		
	/////////////////////////////////////////
	///////////  Implementacion  ////////////
	/////////////////////////////////////////
	
	// CONEXION MYSQL
	function connMYSQL_SEMTI()
	{
	   return $GLOBALS["adoMYSQL_SEMTI"]->PConnect($this->my_host,$this->my_user,$this->my_pass,$this->my_db);   
	}
        
    // CONEXION MSSQL
	function connMSSQL_SEMTI()
	{
	   return $GLOBALS["adoMSSQL_SEMTI"]->PConnect($this->sql_dsn,$this->sql_user,$this->sql_pass);   
	}
	/////////////////////////////////////////
	
}

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////	

?>