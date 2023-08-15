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

$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
$codigo = filter_var($_POST['codigo'], FILTER_SANITIZE_STRING);
$texto = filter_var($_POST['texto'], FILTER_SANITIZE_STRING);
$usuario = filter_var($_POST['usuario'], FILTER_SANITIZE_STRING);

$inf = new HISAutotexto();

if ($id <> null) {
    //Grabar el informe 
    if ($id >0 ) {
        $inf->getById($id);
    } else {
        $id=null;
    }
}else {
        $id=null;
    }

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $texto = encodeToIso($texto);
}

$result=$inf->save($id, $texto, $codigo, $usuario);

if ($result > 0) {
    echo "OK:" . $result;
    return;
}
if ($result < 0) {
    echo "ER:Error al grabar el informe " . $result;
    return;
}
    
 
