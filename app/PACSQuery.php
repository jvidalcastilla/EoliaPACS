<?php
set_include_path("../model/");
 header('Cache-Control: no cache'); //no cache
 session_cache_limiter('private_no_expire'); // works
 
if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
$_SESSION['modulo'] = "PACSQuery";
require_once 'security_validation.php';
include_once '../model/CInforme.php';

        ?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="logo_web.png" sizes="16x16">
        <title>PACS - Visualizar estudios</title>
        
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/theme.bootstrap_4.css">
        <link rel="stylesheet" href="../css/eoliapacs.css">
        
        <link rel="stylesheet" href="../fa/css/font-awesome.min.css">
        
        <script src="../js/jquery.min.js"></script>
        <script src="./PACSQuery.js"></script>
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
    
     

        <?php
        $rango_desde=date("Y-m-d");
        $rango_hasta=date("Y-m-d");
        
        $modalities = "ALL";
        if (isset($_POST['Modality'])) {
            $modalities = $_POST['Modality'];
        }
        
        $periodo = "hoy";
        if (isset($_POST['fecha'])) {
            $periodo = $_POST['fecha'];
        }
        
        $id_pac = "";
        if (isset($_POST['id_paciente'])) {
            $id_pac = $_POST['id_paciente'];
        }

        $nom_pac = "";
        if (isset($_POST['nom_paciente'])) {
            $nom_pac = $_POST['nom_paciente'];
        }
        
        if (isset($_POST['rango_desde'])) {
            $rango_desde=$_POST['rango_desde'];
            $rango_hasta=$_POST['rango_hasta'];
        }
        
        ?>

    
    <nav class="navbar navbar-expand-sm justify-content-between fondo_panel">
        <div class="nav-item">
        <img class="navbar-brand" src="../img/logo_web.png" height="50" alt="PACS">
        <p class="navbar-text text-white" href="#">&nbsp;PACS</p>
        </div>    
 
            <div class="col-auto mr-sm-2 mt-0"> 

                          <div class="badge badge-secondary mr-sm-0">
                              <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-file-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M4 1h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H4z"/>
                              <path d="M13.784 14c-.497-1.27-1.988-3-5.784-3s-5.287 1.73-5.784 3h11.568z"/>
                              <path fill-rule="evenodd" d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                              </svg>
                              <?php echo $_SESSION['user_id']?>
                          </div>


                </div> 
               
           
           
  <!----- ACA PONER LAS ALERTAS --->
 
</nav>
    
    
    <div class="container-fluid fondo_panel" id="filtro">
    <form action="" method="post" id="form_filtro" class="sticky-top">
            
                  <!---      
                <h1><span class="badge badge-pill badge-primary">Estudios de diag. x Imagen</span></h1>
                  --->
                <div class="form-row">  
                <div class="custom-control custom-control-inline">
                    Tipo:
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="defaultGroupExample1" name="Modality" value="ALL" <?php if ($modalities=='ALL') {echo 'checked';}?>>
                    <label class="custom-control-label" for="defaultGroupExample1">TODAS</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="defaultGroupExample4" name="Modality" value="CR" <?php if ($modalities=='CR') {echo 'checked';}?>>
                    <label class="custom-control-label" for="defaultGroupExample4">RX</label>
                </div>

                <!-- Group of default radios - option 2 -->
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="defaultGroupExample2" name="Modality" value="CT" <?php if ($modalities=='CT') {echo 'checked';}?>>
                    <label class="custom-control-label" for="defaultGroupExample2">TC</label>
                </div>

                <!-- Group of default radios - option 3 -->
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="defaultGroupExample3" name="Modality" value="US" <?php if ($modalities=='US') {echo 'checked';}?>>
                    <label class="custom-control-label" for="defaultGroupExample3">ECO</label>
                </div>
                </div>
                
                  
                  
                  <div class="row">
                    <div class="col-12 col-xl-3">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Desde: </span>
                            </div>
                            <input type="date" class="form-control" id ="rango_desde" name="rango_desde" value="<?PHP echo $rango_desde;?>">

                        </div>
                    </div>

                    <div class="col- col-xl-3">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Hasta:</span>
                            </div>
                            <input type="date" class="form-control" id="rango_hasta" name="rango_hasta" value="<?PHP echo $rango_hasta;?>">

                        </div>

                    </div>
                    
                </div>
                  
                  
                  
                  
                  
                  
                  
                  
                  

                <div class="row">
                    
                    
                    <div class="col-12 col-xl-3 ">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nombre</span>
                            </div>
                            <input type="text" class="form-control" placeholder="Nombre o apellido" name="nom_paciente"
                                   value="<?PHP echo $nom_pac;?>">
                        </div>

                    </div>
                    
                    <div class="col-6 col-xl-3">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">DNI</span>
                            </div>
                            <input type="text" class="form-control" placeholder="DNI Pac." name="id_paciente" value="<?PHP echo $id_pac;?>">

                        </div>
                    </div>

                    
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-primary btn-sm " id="btnNombre" name="periodo" value="nombre">
                            <li class="fa fa-search"></li>&nbsp;Buscar</button>
                    </div>
                </div>
            
    </form>
</div>	
     
     
     
     
     
     
    <hr>

    
  <div class="modal fade" id="sendEmailPopup" tabindex="-1" role="dialog" aria-labelledby="sendEmailPopupLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="recipient-name"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
           <div class="form-group">
            <label for="recipient-name" class="col-form-label">E-Mail:</label>
            <input type="email" class="form-control" id="recipient-address">
          </div>
          
           <div class="form-group">
            <label for="attached-files" class="col-form-label">Archivos:</label>
            <div id="attached-files"></div>
          </div>
            
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btnEnviarMail" class="btn btn-primary" onclick="enviarEmail();">Enviar</button>
      </div>
    </div>
  </div>
</div>
    
    <div class="container-fluid">
    <?php
    if (isset($_POST['periodo'])) {
        //header("Location: .\searchPatientImages.php?periodo=".$_POST['periodo']."&nom_pac=".$_POST['nom_paciente']."&id_pac=".$_POST['id_paciente']);

        require "./searchPatientImages.php";
     
    }
    ?>
    </div>
</body>
<footer>
    
    
            <firma>&copy;EoliaTI</firma>
    
    
</footer>
</html>