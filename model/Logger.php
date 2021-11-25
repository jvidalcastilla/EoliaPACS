<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once "Connections.php";

function getDireccionIPLogger()
{
    if (!empty($_SERVER ['HTTP_CLIENT_IP'] ))
      $ip=$_SERVER ['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER ['HTTP_X_FORWARDED_FOR'] ))
      $ip=$_SERVER ['HTTP_X_FORWARDED_FOR'];
    else
      $ip=$_SERVER ['REMOTE_ADDR'];

    return $ip;
}


function LogAcceso($usuario,$modulo, $descripcion){

    $conMedic= getMedicalConn();
    $ip= getDireccionIPLogger();
    $sql="insert into log_acceso(fecha_hora, codigo, modulo, descripcion, ip_origen) values (current_timestamp,$1,$2,$3,$4)";
    $params=array($usuario, $modulo,$descripcion,$ip);
    $result= pg_query_params($conMedic,$sql,$params);
    
    return true;

      
    
}
        