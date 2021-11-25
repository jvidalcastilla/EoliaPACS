<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "../model/Connections.php";
include "../model/CInforme.php";

function encodeToIso($string) {
     return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
}


$pacDni= urldecode($_GET['dni']);
$pacNombre=urldecode($_GET['paciente']);
$pacObraSocial=urldecode($_GET['obraSocial']);
$pacAfiliado=urldecode($_GET['afiliado']);
$solicitante=urldecode($_GET['solicitante']);
$informe= urldecode($_GET['informe']);
$usuario=urldecode($_GET['usuario']);
$fecha=urldecode($_GET['fecha']);
$studyInstance=urldecode($_GET['studyInstance']);

$firmante= strtolower(trim(urldecode($_GET['firmante'])));

if ($pacDni<>null){
    
    $informe= urldecode($informe);
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $informe= encodeToIso($informe);
    }
    
    //Agregar al informe la firma del informante 
    $aFile="./firmas/".$firmante.".txt";
    $firma="";
    if (file_exists($aFile)){
        $firma= file_get_contents($aFile);
        $informe=$informe.PHP_EOL.PHP_EOL.$firma;
    }
    /*
    $informe= str_replace("\n",PHP_EOL, $informe);
   
    $chequear= bin2hex(substr($informe,0,1));
    while (($chequear=='0d')||($chequear=='0a')||($chequear=='c2')||($chequear=='a0')||($chequear=='3c')){
       $informe=substr($informe,1);
       $chequear= bin2hex(substr($informe,0,1));
    } 
    
       */
    
    //Grabar el informe 
    $inf=new CInforme();
    $inf->setInforme($informe);
    $inf->setPac_afiliado($pacAfiliado);
    $inf->setPac_nombre($pacNombre);
    $inf->setPac_os($pacObraSocial);
    $inf->setPaciente_id($pacDni);
    $inf->setSolicitante_nom($solicitante);
    $inf->setStudyinstanceid($studyInstance);
    
    $result=$inf->saveInforme();
    
    
    if ($result>0){
        echo "OK:".$result;
        return;
    }
    if ($result<0){
        echo "ER:Error al grabar el informe ".$result;
        return;
    }
    
    
    echo $result;
    return $result;
}
else
{
        echo "-1";
        return -1;
    ;}
