<?php
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$params= json_decode(urldecode($_POST['param']));

//echo var_dump($params);

$nombre = $params->name;


$params= json_decode(urldecode($_POST['param']));

$_SESSION['modulo'] = "EmitirInformes";
require_once '../model/security_validation.php';
include_once '../model/Connections.php'; 
include_once 'Autotexto.php';
include_once '../model/TextFunctions.php';
//include_once '../IntegracionVisualMedica/getUrlPatientPortal.php';
include_once '../model/PACSHelper.php';
include_once '../model/CInforme.php';

$usuario = "";
$nom_paciente = urldecode($params->name);
$studyInstance=urldecode($params->uid);
$dniPaciente = urldecode($params->patientId);
$solicitante= urldecode($params->referringPhysicianName);
$solicitante= str_replace("^"," ", $solicitante);


$direccionPaciente = "";
$diagnosticos = "";
$autoTexto = "";
$ultimaAccion = "";
$edadPaciente = "";
$limpiar = false;
$accessionNumber = "";
$pacObraSocial="";
$afiliado="";

$usuario = $_SESSION['user_id'];
$fecha_estudio = $params->fecha;


$estudio= "";


$codigoProf = "";
if (isset($_POST['codigo'])) {
    $codigoProf = $_POST['codigo'];
}


$firmante = "";
if (isset($_GET['firmante'])) {
    $firmante = trim($_GET['firmante']);
}

$dniPaciente = $params->patientId;




$informe = "";
if (isset($_POST['informe'])) {
    $informe = $_POST['informe'];
    $_SESSION['informe'] = $informe;
} elseif (isset($_SESSION['informe'])) {
    $informe = $_SESSION['informe'];
}



if (isset($_POST['editor1'])) {
    $informe = $_POST['editor1'];
} 

//Si no hay datos en el campo de informe lo tomo de la base de datos si es que existe

    $unInforme=new CInforme();
    $unInforme->getInformeByStudy($studyInstance);
    $informe=$unInforme->getInforme();
    $afiliado=$unInforme->getPac_afiliado();
    $pacObraSocial=$unInforme->getPac_os();
    if ($unInforme->getPac_nombre()!=""){
        $nom_paciente=$unInforme->getPac_nombre();
    }
    if ($unInforme->getSolicitante_nom()!=""){
        $solicitante=$unInforme->getSolicitante_nom();
    }
    
    
    


$accessionNumber = "";
$urlEstudio = "";
if (isset($_POST['accessionNumber'])) {
    $accessionNumber = $_POST['accessionNumber'];
    $_SESSION['accessionNumber'] = $accessionNumber;
    //$urlEstudio = getUrlAcceso($accessionNumber);
} elseif (isset($_SESSION['accessionNumber'])) {
    $accessionNumber = $_SESSION['accessionNumber'];
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <html lang="es" xml:lang="es">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="favicon.png" sizes="16x16">
        <title>Emitir informe</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../fa/css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/eoliapacs.css">
        <link rel="stylesheet" href="./EmitirInforme.css">
        <script src="../js/jquery.min.js"></script>
        <script src="../js/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/ckeditor/ckeditor.js"></script>
        <script src="./EmitirInforme.js"></script>
     
    </head>
    <body>


    
<nav class="navbar navbar-expand-sm sticky-top">
        <img class="navbar-brand" src="../img/logo_web.png" height="35" alt="EoliaPACS">
            <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-card-heading" fill="white" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M14.5 3h-13a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
  <path fill-rule="evenodd" d="M3 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
  <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-1z"/>
</svg>  
            
            <a class="navbar-brand" href="#">&nbsp;Emisi&oacute;n de informes</a>
            <div class="nav-item justify-content-sm-end">
        <div class="flex-sm-fill text-md-right nav-link">
        <span class="badge badge-secondary" id="cod_usuario" name="cod_usuario">
        <?php 
        if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
        echo $_SESSION['user_id'];?>
      </span>
      
            
            
            
    </div>
</nav>

        <!--- Encabezado de impresion ---->

        <div id="header_m"   style="visibility: hidden; display: none" >

            <div class="row">
                <div class="col-1">
                    <img class="mr-3" src="logo.png" alt="LogoMedisur.png" width="150" height="165">
                </div>
                <div class="col-7">
                    </BR>
                    <titulo> MEDISUR S.A.<BR></titulo>
                    <margen>Maipu 555 - Rio Gallegos<BR></margen>
                    <margen>Tel: (2966) 42-3000<BR></margen>
                    <margen>E-Mail: rx@medisur-rgl.com.ar<BR></margen>
                    <titulo-xs>Turnos por WhatsApp:2966-693267 - Lun-Vie 8 a 15hs.</titulo-xs>

                </div>
                <div class="col  align-right"> 
                </div>
            </div>            
            <br>
            <!----<center><h5>Servicio de Diagn&oacutestico por im&aacute;genes</H5></center> ---->
            <div class="card ml-2 mr-5">
                <div class="card-text padtit">Paciente: <B><tipo_pac> <?php echo $nom_paciente . '  ' . $dniPaciente; ?></tipo_pac></B></div>
                <div class="card-text padtit">Afiliado / Obra social:<B>  &nbsp;<?php echo $nro_afiliado . " " . $obrasocial; ?></B>  </div>
                <div class="card-text padtit" id="lblProfesional"></div>
                <div class="card-text padtit" id="lblFecha"></div>
            </div>
            </br>            
            <div id="print_helper">
           
            </div>


        </div>



        <!--- FIN Encabezado de impresion ---->


        <!----- Buscador --->
        <div class="modal fade" id="buscadorPacModal" tabindex="-1" role="dialog" aria-labelledby="buscadorPacModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="buscadorPacModalLabel">Buscar paciente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>

                            <div class="form-group row">
                                <div class="col-sm-4"><label for="recipient-name" class="col-form-label">Apellido y nombre:</label></div>
                                <div class="col-sm-4"><input type="text" class="form-control" id="bApeNombre" name ="bApeNombre" onkeypress="javascript:buscar_pac(event);"></div>
                                <div class="col-sm-4"><input type="text" style="display: none;"></div>
                                <div class="col-sm-2"><button type="button" class="btn btn-primary fa fa-search" name="bPacBuscar" onclick="buscarPaciente()" id="bPacBuscar"> Buscar</button></div>
                            </div>

                            <div class="form-group">
                                <table class="table table-striped table-responsive table-sm table-bordered" id="tblPacientes">
                                    <head>
                                    <tr><th></th><th>Paciente</th><th>DNI</th><th>Obra Social</th><th>Direccion</th></tr>
                                    </head>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"  data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!----- FIN Buscador --->


        <div class="container">
            <div class="card bg-light" id="filtros">
                <form action="" method="post" name="FormEntrar" id="FormEntrar">
                    <div class="row  d-print-none">
                        <div class="col-2 pl-2 ml-0">
                            <label for="dniPaciente" class="col-sm-10">Historia Clinica:</label>
                        </div>
                        <div class="col-2">
                            <input type="number" class="form-control form-control-sm" required value ="<?php echo $dniPaciente ?>" id="dniPaciente" name="dniPaciente"  placeholder="Historia Clinica">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-sm btn-primary" name="btnDni" id="ingresoDniPac" type="post"> <i class="fa fa-refresh"></i>&nbsp;Buscar</button>

                        </div>
                        <div  class="col-2">
                            <!--<button type="button"  id="btn_porApellido" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#buscadorPacModal"><i class="fa fa-search"></i>Por Apellido</button>-->
                        </div>
                        <div class="col-3">
                            <!--<button class="btn btn-sm btn-primary" accesskey="g" name="grabarInforme" id="grabar" data-toggle="tooltip" data-placement="top" title="ALT+G Grabar"> <i class="fa fa-save"></i>&nbsp;Grabar Informe</button>-->    
                            <button type="button" class="btn btn-sm btn-primary" accesskey="i"  id="btn_imprimir" onclick="imprimir()"> <li class="fa fa-print"></li>&nbsp;Imprimir</button>
                        </div>           
                    </div> 

                    <div class="row  d-print-none">
                        <div class="col-sm-2 pl-2 ml-0">
                            <label for="nomPaciente" class="col-sm-10">Paciente:</label>
                        </div>
                         <div class="col-sm-3">
<!--                            <?php //echo $nom_paciente; ?> -->
                            <input type="text"  class ="form-control form-control-sm" placeholder="Paciente" name="nom_paciente" id="nom_paciente" value="<?php echo $nom_paciente ?>" required>                           
                        </div>     
                        
                        <div class="col-auto form-inline pl-2 ml-0">
                            <label for="osAfil" class="">OS/Afil:</label>
                            <input type="text"  class ="form-control form-control-sm col-3" placeholder="ObraSocial" name="obraSocial" id="obraSocial" value="<?php echo $pacObraSocial ?>">                           
                            <input type="text"  class ="form-control form-control-sm" placeholder="Afiliado" name="afiliado" id="afiliado" value="<?php echo $afiliado ?>">                           
                        </div>     
                        
                        
                    </div> 
                    
                    <div class="row  d-print-none ">
                        <div class="col-sm-2 pl-2 ml-0">
                            <label for="profesional" class="col-sm-10">Solicitante:</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class ="form-control form-control-sm" placeholder="solicitante" name="solicitante" id="solicitante" value="<?php echo $solicitante ?>" required>
                        </div>      
                        <div class="col-sm-2 pl-2 ml-0">
                            <label for="fecha_estudio" class="col-sm-10">Fecha:</label>
                        </div>
                        
                        <div class="col-sm-2 p-0 m-0">
                            <input type="date" class ="form-control form-control-sm" name="fecha_estudio" value="<?php echo $fecha_estudio; ?>" id="fecha_estudio">
                        </div>      
                    </div> 

                    <div class="row d-print-none">
                        
                        <div class="col-sm-2 pl-2 ml-0">
                            <label for="firmante" class="col-sm-10">Firmante:</label>
                        </div>
                        
                        <div class="col-sm-2">
                            <select class ="form-control form-control-sm" id="firmante" name="firmante">
                                <option value="1">Sebastian</option>
                                <option value="2">Leopoldo</option>
                                <option value="2">Carolina</option>
                            </select>    
                        
                        </div>
                    </div> 


                    <div class="row  d-print-none">
                        <div class="col">
                        </div>    
                    </div>
            </div>





            <div class="form-group w-100 d-print-none" id="texto_ingresado">
                <BR>
                <!--<B><label for="informe">Informe:</label></B>-->
                <!--<textarea onkeyup="onKeyUp(event)" class="form-control" rows="37" cols="85" id="informe" name="informe"><?php echo $informe; ?></textarea>-->
                
             
                
                <textarea  cols="100" id="editor1" name="editor1" rows="20" data-sample-short></textarea>
                
            </div>

            <div class="form-group" id="imagen_firmas">
                <?php
                echo "<center>";
                $f = explode(",", $firmante);
                foreach ($f as $unFirmante) {
                    if (trim($unFirmante) != "") {
                        echo "<img src='../IntegracionVisualMedica/" . strtolower(trim($unFirmante)) . ".jpg'>";
                    }
                }
                echo "</center>";
                ?>
            </div>
            <div class="form-group" id="texto_firmas" hidden>
                <?php
                echo trim($firmante);
                ?>
            </div>
             <div id="archivo" hidden>
                <?php
                echo $archivo;
                ?>
            </div>
            <div id="accessionNumber" hidden>
                <?php
                echo trim($accessionNumber);
                ?>
            </div>
        <div id="studyInstance" hidden>
                <?php
                echo trim($studyInstance);
                ?>
            </div>
            <div  id="informe_html" hidden>
                 <?php echo urldecode($informe);?>
             </div>
                
        </div>
<div class="container">
    
    <button type="button" class="btn btn-success" onclick="grabarInforme();"><i class="fa fa-floppy-o"  aria-hidden="true"></i> Grabar </button>
    
    <button type="button" class="btn btn-success" onclick="grabarInforme();window.open('./ImprimirInforme.php?studyinstance=<?php echo $studyInstance;?>&dniPaciente=<?php echo $dniPaciente;?>')"><i class="fa fa-print"  aria-hidden="true"></i> Imprimir </button>
    
</div>
        
         
  <script>
    CKEDITOR.replace('editor1', {
      
      height: 300,
      width: '100%',
      removeButtons: ''
    });
    
    let v=document.getElementById('editor1');
    let vinforme=document.getElementById('informe_html');
    v.value=vinforme.innerHTML;
    
  </script>
        
    </body>
  




