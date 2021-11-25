<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//echo (var_dump($_POST));
$params= json_decode(urldecode($_POST['param']));
//echo (var_dump($params));

if ($params->action=='informar'){
    require './Informes/EmitirInforme.php';
}