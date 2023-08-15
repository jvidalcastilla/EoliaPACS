<?php
require("../PHPMailer-master/src/PHPMailer.php");
require("../PHPMailer-master/src/SMTP.php");
require("../PHPMailer-master/src/Exception.php");
require_once 'mail_template.php';

function sendMail($mailTo, $paciente,$directLink, $attach){
$institucion = new CInstitucion();
$institucion->getInstitucion();

$to = $mailTo;
$subject = $institucion->getNombre(). ' - '.$paciente.' Tu estudio esta disponible';

$mail=new PHPMailer\PHPMailer\PHPMailer();
$mail->CharSet = 'UTF-8';
$mail->IsSMTP();
$mail->Host       = 'medisur.ferozo.com';
$mail->SMTPSecure = 'tls';
$mail->Port       = 465;
$mail->SMTPDebug  = 1;
$mail->SMTPAuth   = true;
$mail->Username   = 'informes@medisur-rgl.com.ar';
$mail->Password   = 'LKSf@3R2nU';
$mail->SetFrom('informes@medisur-rgl.com.ar', $institucion->getNombre());
$mail->AddReplyTo('informes@medisur-rgl.com.ar',$institucion->getNombre());
$mail->Subject    = $subject;
$mail->MsgHTML($body);
$mail->AddAddress($to, $paciente);
if ($attach!=null){
    $mail->addAttachment($attach, "Informe ".$paciente, mime_content_type($attach));
    $body = getMailTemplate($paciente,$directLink, true);
} else{
    $body = getMailTemplate($paciente,$directLink, false);
}
$mail->send();

}


function    sendMailWithAttachment($mailTo, $paciente,$files){

if ($mailTo=="") {return false;}


$to = $mailTo;
$subject = 'Tu estudio en Medisur';
$body = getMailTemplateRx($paciente);
$AltBody='Te enviamos tu estudio realizado en Medisur';
$mail=new PHPMailer\PHPMailer\PHPMailer();
//$mail->SMTPDebug = 1; 
$mail->CharSet = "UTF-8";
$mail->IsSMTP();
$mail->Host = "medisur.ferozo.com";
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->SMTPAuth = true;
$mail->SMTPSecure = "ssl";
$mail->Username = "informes@medisur-rgl.com.ar";
$mail->Password = "LKSf@3R2nU";
$mail->Port = "465";
$mail->From="informes@medisur-rgl.com.ar";
$mail->FromName=$institucion->getNombre();
$mail->AddReplyTo('informes@medisur-rgl.com.ar',$institucion->getNombre());
$mail->SMTPKeepAlive = true;
$mail->Subject=$subject;
$mail->MsgHTML($body);
$mail->AddAddress($to, $to);
$enviar=false;
//echo $files;
foreach ($series as $unaSerie) {
                   
    $aFile=$unaSerie->fileName;
    
    if ($aFile.""<>"") {
        echo "Archivo:-".$aFile."-";
        $mail->addAttachment($aFile);
        $enviar=true;
    }
}
if ($enviar){
        $mail->send();
}
$mail=null;
return $enviar;
}

function sendMailWithAttachmentPACS($mailTo, $paciente,$files){

if ($mailTo=="") {return false;}


$to = $mailTo;
$subject = 'Tu estudio en Medisur';
$body = getMailTemplateRx($paciente);
$AltBody='Te enviamos tu estudio realizado en Medisur';
$mail=new PHPMailer\PHPMailer\PHPMailer();
//$mail->SMTPDebug = 1; 
$mail->CharSet = "UTF-8";
$mail->IsSMTP();
$mail->Host = "medisur.ferozo.com";
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->SMTPAuth = true;
$mail->SMTPSecure = "ssl";
$mail->Username = "informes@medisur-rgl.com.ar";
$mail->Password = "LKSf@3R2nU";
$mail->Port = "465";
$mail->From="informes@medisur-rgl.com.ar";
$mail->FromName=$institucion->getNombre();
$mail->AddReplyTo('informes@medisur-rgl.com.ar',$institucion->getNombre());
$mail->SMTPKeepAlive = true;
$mail->Subject=$subject;
$mail->MsgHTML($body);
$mail->AddAddress($to, $to);
$enviar=false;
//echo $files;
foreach ($files as $unaSerie) {
                   
    $aFile=$unaSerie;
    
    if ($aFile.""<>"") {
        echo "Archivo:-".$aFile."-";
        $mail->addAttachment($aFile);
        $enviar=true;
    }
}
if ($enviar){
        $mail->send();
}
$mail=null;
return $enviar;
}