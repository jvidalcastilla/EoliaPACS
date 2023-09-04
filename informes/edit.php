<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$p = $_GET['param'];
//urlencode(json_encode($data));
$p1 = urldecode($p);
$parametros = json_decode($p1);
//echo var_dump($parametros);


$action = $_GET['action'];

$operacion = "";
if ($action == "ED") {
    $operacion = "Editar el informe";
}
if ($action == "EE") {
    $operacion = "Eliminar el estudio";
}
if ($action == "EI") {
    $operacion = "Eliminar el informe";
}


set_include_path("../model/");
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['modulo'] = "PACSQuery";
require_once 'security_validation.php';
include_once '../menuPrincipal.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="logo_web.png" sizes="16x16">
        <title>PACS - Mantenimiento de estudios</title>

        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/theme.bootstrap_4.css">
        <link rel="stylesheet" href="../css/eoliapacs.css">

        <link rel="stylesheet" href="../fa/css/font-awesome.min.css">

        <script src="../js/jquery.min.js"></script>
        <script src="./PACSQuery.js"></script>
        <script src="../js/popper.min.js"></script>
        <script src="../js/bootstrap.bundle.min.js"></script>
        <script src="../js/bootstrap.js" type="text/javascript"></script>



        <style>
            form_filtro{
                float:left;

            }
            .btn_mail{
                padding-bottom: -1;
                padding-top: -1;
                margin: -1;
                border: none;
                background: transparent;
                align-self: center;

            }
            firma {
                font-size: x-small;
                text-align: center;
                font-style: italic;
            }
            email_dest{
                font-weight:  bold;
            }

            .tablesorter-pager .btn-group-sm .btn {
                font-size: 1em; /* make pager arrows more visible */
                font-weight: bold;

            }


            td{

                padding-top: 0;
                padding-bottom: 0;
                margin-top: 0;
                margin-bottom: 0;
            }

        </style>
    </head>
    <body>

        <div class='row pt-4'>
            <div class='col text-center'>

                <h2 class='text-white'><?php echo $parametros->name; ?></h2>
                <div >
                    <p class='text-white '>  Fecha de estudio: <?php echo $parametros->fecha ?></p>
                <p class='text-white'> ¿Est&aacute; seguro que desea <b><?php echo $operacion ?> </b> del paciente ?</p>
                </div>
            </div>
        </div>
        <div class='row pt-4'>
            <div class='col text-right'>
                
                    <button type="button" onclick="history.back()" class='btn btn-sm btn-secondary'><i class='fa fa-times'></i>&nbsp;Cancelar</button>
                
            </div>
            <div class='col text-left'>
                <button class='btn btn-sm btn-primary'><i class='fa fa-check'></i>&nbsp;Aceptar</button>
            </div>
        </div>


    </body>
