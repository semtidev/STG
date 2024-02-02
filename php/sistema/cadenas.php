<?php

/////////////////////////////////////////////////////////////////////
///////////////////  CLASE DE MANEJO DE CADENAS  ////////////////////
/////////////////////////////////////////////////////////////////////

class Cadenas {

    /////////////////////////////////////////
    //////////////  Atributos  //////////////
    /////////////////////////////////////////
    
    
    /////////////////////////////////////////
    ///////////  Implementacion  ////////////
    /////////////////////////////////////////
    // Función que converte un string a ISO-8859-1 (LATIN1)
    function latin1($txt) {
        $encoding = mb_detect_encoding($txt, 'ASCII,UTF-8,ISO-8859-1');
        if ($encoding == "UTF-8") {
            $txt = utf8_decode($txt);
        }
        return $txt;
    }

    // Función que converte un string a UTF-8
    function utf8($txt) {
        $encoding = mb_detect_encoding($txt, 'ASCII,UTF-8,ISO-8859-1');
        if ($encoding == "ISO-8859-1") {
            $txt = utf8_encode($txt);
        }
        return $txt;
    }

    // Funcion para convertir a utf8 un arreglo
    function utf8_array($array) {
        array_walk_recursive($array, function(&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

    function codificarBD_utf8($cadena) {

        $no_permitidas = array("u00e1", "u00e9", "u00ed", "u00f3", "u00fa", "u00c1", "u00c9", "u00cd", "u00d3", "u00da", "u00f1", "u00d1", "\/");

        $permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ", "/");

        $texto = str_replace($no_permitidas, $permitidas, $cadena);

        return $texto;
    }

    // Funcion que combierte la fecha del Calendario a fecha de la BD
    function fecha_db($fecha_calendario) {

        $dia = substr(substr(strstr($fecha_calendario, " "), 1), 0, 2);
        $ayer = date("d/m/Y", strtotime("-1 days"));

        $meses["Enero"] = '01';
        $meses["Febrero"] = '02';
        $meses["Marzo"] = '03';
        $meses["Abril"] = '04';
        $meses["Mayo"] = '05';
        $meses["Junio"] = '06';
        $meses["Julio"] = '07';
        $meses["Agosto"] = '08';
        $meses["Septiembre"] = '09';
        $meses["Octubre"] = '10';
        $meses["Noviembre"] = '11';
        $meses["Diciembre"] = '12';
        $fecha_mes_ano = strlen(substr(strstr($fecha_calendario, " "), 4));
        $pos = $fecha_mes_ano - 5;
        $mes = substr(substr(strstr($fecha_calendario, " "), 4), 0, $pos);
        $mes_db = $meses[$mes];

        $ano = substr($fecha_calendario, -4);

        $fecha_db = $dia . "/" . $mes_db . "/" . $ano;

        return $fecha_db;
    }

    // Funcion que combierte la fecha del Calendario a fecha SQlSERVER
    function fecha_mssql($fecha) {

        $items = split("/", $fecha);

        $fecha_db = $items[2] . "-" . $items[1] . "-" . $items[0];

        return $fecha_db;
    }

    // Funcion para restar y sumar dias a una fecha
    function operacion_fecha($fecha, $dias) {

        list ($dia, $mes, $ano) = explode("/", $fecha);

        if (!checkdate($mes, $dia, $ano)) {
            return false;
        }

        $dia = $dia + $dias;

        $fecha = date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $ano));

        return $fecha;
    }
    
    
    // Diferencia de Dias entre dos fechas
    function dias_entre_fechas($desde,$hasta){
        
        $inicio = $desde;
        $fin = ($hasta != '' && $hasta != null) ? $hasta : date('Y-m-d');

        $start = new DateTime($inicio);
        $end = new DateTime($fin);

        //de lo contrario, se excluye la fecha de finalización (¿error?)
        $end->modify('+1 day');

        $interval = $end->diff($start);

        // total dias
        $days = $interval->days;

        // crea un período de fecha iterable (P1D equivale a 1 día)
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        // almacenado como matriz, por lo que puede agregar más de una fecha feriada
        $holidays = array('2012-09-07');

        foreach($period as $dt) {
            $curr = $dt->format('D');

            // obtiene si es Domingo
            if($curr == 'Sun') {
                $days--;
            }elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                $days--;
            }
        }
        
        $diferencia = $days;
        return $diferencia;
        
    }
    
    
    // Sanear String
    function sanear_string($string) {

        $string = trim($string);

        $string = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
                array("\\", "¨", "º", "-", "~",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "`", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":",
            ".", " "), '', $string
        );


        return $string;
    }

    /////////////////////////////////////////////

    function mes_en($mes) {

        $months = Array();
        $months["01"] = "Anu";
        $months["02"] = "Feb";
        $months["03"] = "Mar";
        $months["04"] = "Apr";
        $months["05"] = "May";
        $months["06"] = "Jun";
        $months["07"] = "Jul";
        $months["08"] = "Aug";
        $months["09"] = "Sep";
        $months["10"] = "Oct";
        $months["11"] = "Nov";
        $months["12"] = "Dec";

        return $months[$mes];
    }

    // La fecha es en formato yyyy-mm-dd
    function diaSemana_en($fecha) {

        $dias = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");

        return $dias[date("w", strtotime($fecha))];
    }
    
    // Convertir fecha ExtJS en formato MSSQL
    function fecha_extjs_mssql($fecha_extjs) {

        $fecha = explode(' ',$fecha_extjs);

        $months["Jan"] = "01";
        $months["Feb"] = "02";
        $months["Mar"] = "03";
        $months["Apr"] = "04";
        $months["May"] = "05";
        $months["Jun"] = "06";
        $months["Jul"] = "07";
        $months["Aug"] = "08";
        $months["Sep"] = "09";
        $months["Oct"] = "10";
        $months["Nov"] = "11";
        $months["Dec"] = "12";
        
        $fecha_mssql = $fecha[3].'-'.$months[$fecha[1]].'-'.$fecha[2];

        return $fecha_mssql;
    }
    

    /////////////////////////////////////////////
    ///////////  Getters && Setters  ////////////
    /////////////////////////////////////////////
}

////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
?>