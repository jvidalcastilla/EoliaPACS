<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once '../model/CInstitucion.php';
$study=filter_var($_GET['Study'],FILTER_SANITIZE_STRING);
$Paciente=filter_var($_GET['nom_pac'],FILTER_SANITIZE_STRING);

date_default_timezone_set('America/Argentina/Buenos_Aires');
$institucion= new CInstitucion();
$institucion ->getInstitucion();
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="libro.png" sizes="16x16">
    <title><?php echo $institucion->getNombre(); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>        
    <link rel="stylesheet" href="../css/eoliapacs.css">
</head>

<body>
    
<style>
    .impreso {
        font-size: x-small;
    }
   titulo {
                font-family: "Times New Roman", Times, serif;
                font-size: xx-large;
                font-weight: bold;
                margin: 15px;        
            }
    margen {
        margin: 20px;        
    }
    
    
</style>
    

<script>
function imprimir(){
        window.print();
}
</script>

<script src="../js/qrcode.min.js"></script>
<div class="card p-5">
  <div class="container">
      <div class=" border-secondary mb-3">
          <div class="row">
          <div class="col-4">
              <img src="../logo_infomes.jpg" height="120" alt="<?php echo $institucion->getNombre(); ?>">
          </div>
          <div class="col-8">
            &nbsp;<titulo><?php echo $institucion->getNombre(); ?></titulo></p>
            <margen><?php echo $institucion->getDireccion(); ?><BR></margen>
            <margen>Tel: <?php echo $institucion->getTelefonos(); ?><BR></margen>
            <margen>E-Mail: <?php echo $institucion->getEmail(); ?><BR></margen>
          </div>
          </div>
  </div>
  <hr>
  <div class="card-body">
    <blockquote class="blockquote mb-0">
        
       <div class="row">
           <div class="col-4">
               <div id="qrcode"></div>
            </div>
           <div class="col-8">
               <p><?php echo $Paciente;?> 
               <p>Escane&aacute; el c&oacute;digo QR y acced&eacute; a tus estudios on-line, cuando y desde donde quieras.</p>
               <div class="impreso">Impreso <?php echo Date('d-m-Y H:i');?></div>
           </div>
        </div>   
        <footer class="blockquote-footer"></footer>
    </blockquote>
  </div>
</div>
    <hr>
<div id="cmdImprimir" class="container d-print-none d-flex justify-content-center ">
    
    <button class="btn btn-lg btn-primary" accesskey="i"  id="btn_imprimir" onclick="imprimir()"> <i class="fa fa-print"></i>&nbsp;Imprimir</button>
</div>


<script type="text/javascript">
    
    var parts = window.location.search.substr(1).split("&");
    var $_GET = {};
    for (var i = 0; i < parts.length; i++) {
        var temp = parts[i].split("=");
        $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
    }
    
    
    new QRCode(document.getElementById("qrcode"),
        {text: "<?php echo $institucion->getUrl_qr()."?study=".$study;?>",
        width: 128,
	height: 128,
	colorDark : "#000000",
	colorLight : "#ffffff",
	correctLevel : QRCode.CorrectLevel.H
    });
</script>
</body>