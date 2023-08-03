<?php

//require_once  './security_validation.php';
const DB_RP_NAME="Factu";  //factu_062019
const DB_RP_SJB_NAME="Factu_SJB";  //factu_062019
//const DB_RP_NAME="Factu_062019";  //factu_062019

function obtenerDireccionIP()
{
    if (!empty($_SERVER ['HTTP_CLIENT_IP'] ))
      $ip=$_SERVER ['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER ['HTTP_X_FORWARDED_FOR'] ))
      $ip=$_SERVER ['HTTP_X_FORWARDED_FOR'];
    else
      $ip=$_SERVER ['REMOTE_ADDR'];

    
    return "127.0.0.1";
    //return $ip;
}


function Qs($Text) { return(str_replace("'","''",$Text)); }

function SanearTXT($Text) { 
   return(empty($Text)?'NULL':("'".Qs($Text)."'")); 
    return $Text;

}

function formatLocaleString($string){

if (getCharsetEncoding()=="ISO-8859-1")    {
    return utf8_decode($string);
}
else
{
    return $string;
}

}



function getCharsetEncoding()
{    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return "ISO-8859-1";
    }
    return "UTF-8";

}



function getMedicalConn()
{
    
//$dbconn = pg_connect("host=192.168.0.190 port=5432 dbname=pacseolia user=pacseolia password=PACSEolia");
$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=pacseolia user=pacseolia password=pacseolia");
pg_set_client_encoding($dbconn, "UTF-8");
return $dbconn;
    
}



function migrate_getPACSServerURL()
{
    return "http://192.168.0.190:8042";
   // return "http://181.27.61.31:8042";
    
}

function getPACSViewerIP()
{
    $ipCliente = obtenerDireccionIP();
    
     if((substr($ipCliente, 0, 8 ) == "192.168.") OR ($ipCliente=="::1"))
    {   
        return "192.168.0.190";
    }else{
        return "181.118.115.80";
    }
      
}


function getPACSServerInternalURL()
{
       return "http://192.168.0.190";
   }
      


function getPACSServerURL()
{
    $ipCliente = obtenerDireccionIP();
    if((substr($ipCliente, 0, 8 ) == "192.168.") OR ($ipCliente=="::1"))
    {
    //   return "http://127.0.0.1";
    }else{
       //return "http://181.118.115.80:8042";
       // return "http://181.118.115.80:".getPACSServerPort();
   }
   
   
   return "127.0.0.1:8042";
   }
      


function getPACSUserPassword()
{
    //return "tomografia:medisur2019";
    return "";
}

function getPACSServerIP()
{
    $ipCliente = obtenerDireccionIP();
 /*   if((substr($ipCliente, 0, 8 ) == "192.168.") OR ($ipCliente=="::1"))
    {   	
    	return "192.168.0.190";
     }
   else{ 
    	return "181.118.115.80";
    }
*/
    return "127.0.0.1";
}

function getPACSExternalPort(){
        return "8015";
    }


function getPACSServerPort()
{
    return 8042;
    $ipCliente = obtenerDireccionIP();
    if((substr($ipCliente, 0, 8 ) == "192.168.") OR ($ipCliente=="::1"))
    {
        return "80";
     }
   else{
        return "8015";
    }

    
}

?>
