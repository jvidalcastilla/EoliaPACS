<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$mailTo= filter_var($_GET['mailto'],FILTER_VALIDATE_EMAIL);
$paciente= filter_var($_GET['paciente'],FILTER_SANITIZE_STRING);
$files= filter_var($_GET['files']);


include '../informes/sendResultByMail.php';

if ($paciente=="undefined"){$paciente="Hola";}
$archivos= json_decode($files);
return sendMailWithAttachmentPACS($mailTo, $paciente,$archivos);
