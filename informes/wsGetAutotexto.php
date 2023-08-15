<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "../model/Connections.php";
include "Autotexto.php";

function encodeToIso($string) {
    return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
}

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

$inf = new HISAutotexto();
$informe="";
if ($id <> null) {
    //Grabar el informe 
    $inf->getById($id);
    $informe=$inf->texto;
}

echo $informe;


    
 
