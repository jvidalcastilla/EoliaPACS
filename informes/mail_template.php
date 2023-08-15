<?php
include '../model/CInstitucion.php';



function getMailTemplate($paciente,$directLink, $conResultados=false)
{
$institucion = new CInstitucion();
$institucion->getInstitucion();
if ($conResultados){
$mail_body="
    
<html>
  <head>

    <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
    <title></title>
  </head>
  <body text='#000000' bgcolor='#FFFFFF'>
    <font size='3'> <p>".$paciente.",</p></font>
    <p>&nbsp;&nbsp;&nbsp; Los resultados de tu estudio ya están
      disponibles para que los retires. Podes consultarlos en nuestra
      web presionando <a href=".$directLink.">AQUI</a></p>
    <p>Saludos, <br>
    </p>
    <p>&nbsp; <br>
    </p>
    <div class='moz-signature' align='center'>
      <div class='moz-signature'><font size='2'> </font><font size='+1'><b>".$institucion->getNombre()."</b></font><font size='2'><br>
        </font><font size='2'>TEL:".$institucion->getTelefonos()."</font><br>
        <font size='2'>".$institucion->getDireccion()." - ".$institucion->localidad."</font></div>
    </div>
  </body>
</html>";
} else
{
    $mail_body="
    
<html>
  <head>

    <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
    <title></title>
  </head>
  <body text='#000000' bgcolor='#FFFFFF'>
    <font size='3'> <p>".$paciente.",</p></font>
    <p>&nbsp;&nbsp;&nbsp; Te enviamos tu estudio. Podes consultarlos en nuestra
      web presionando <a href=".$directLink.">AQUI</a></p>
    <p>Saludos, <br>
    </p>
    <p>&nbsp; <br>
    </p>
    <div class='moz-signature' align='center'>
      <div class='moz-signature'><font size='2'> </font><font size='+1'><b>".$institucion->getNombre()."</b></font><font size='2'><br>
        </font><font size='2'>TEL:".$institucion->getTelefonos()."</font><br>
        <font size='2'>".$institucion->getDireccion()." - ".$institucion->getLocalidad()."</font></div>
    </div>
  </body>
</html>";
}

return $mail_body;

}

function getMailTemplateRx($paciente)
{
$institucion = new CInstitucion();
$institucion->getInstitucion();
$mail_body="
<html>
  <head>
    <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
    <title></title>
  </head>
  <body text='#000000' bgcolor='#FFFFFF'>
    <font size='3'> <p> ".$paciente.",</p></font>
    <p>&nbsp;&nbsp;&nbsp;Te enviamos tu estudio. 
  
    <p>Saludos, <br>
    </p>
    <p>&nbsp; <br>
    </p>
    <div class='moz-signature' align='center'>
      <div class='moz-signature'><font size='2'> </font><font size='+1'>
        <b>".$institucion->getNombre()."</b>
            </font><font size='2'><br>
        </font><font size='2'>TEL:".$institucion->getTelefonos()."</font><br>
        <font size='2'>".$institucion->getDireccion()." - " .$institucion->getLocalidad()."</font></div>
    </div>
  </body>
</html>";

return $mail_body;

}
