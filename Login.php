<?php
set_include_path("./model/");
include_once 'Logger.php';
if (!(session_status() === PHP_SESSION_ACTIVE)){
    session_start();
}

  function procesarIngreso($unUser){
                
                if (isset($_SESSION['modulo'])){
                    $modulo=$_SESSION['modulo'];
                    if ($modulo=="PACSQuery"){header("location:./app/PACSQuery.php");}
                    elseif ($modulo=="ConsultaHCFactu"){header("location:./ConsultaHCFactu.php");}
                    elseif ($modulo=="Menu"){header("location:./app/PACSQuery.php");}
                    elseif ($modulo=="PortalMedico"){header("location:./portal_medico.php");}
                    elseif ($modulo=="LibroTomografia"){header("location:./LibroTomografia/LibroTomografia.php");}
                    elseif ($modulo=="PlanQuirurgico"){header("location:./plan_quirurgico.php");}
                    else {
                        
                        
                    $perfiles=$unUser->getPerfiles();
//                    if (sizeof($perfiles)==1)  {
//                        if ($perfiles[3]=='3'){
//                              //Si el unico perfil lo envio directamente al portal medico
//                            header("location:./portal_medico.php");
//                        }
//                    }
                    header("location:./app/PACSQuery.php");
                   //header("location:./menu.php");
                        
                   ;}
                    
                }else 
                    {
                    
                    $perfiles=$unUser->getPerfiles();
                    if (sizeof($perfiles)==1) {
                        if (isset($perfiles[3])){
                            if ($perfiles[3]=='3'){
                                  //Si el unico perfil lo envio directamente a la consulta de pacientes
                                header("location:./portal_medico.php");
                                return;
                            }
                            }
                    }
                    header("location:./menu.php");
                    }
            }
            
            
          //  header('Cache-Control: no cache'); //no cache
           // session_cache_limiter('private_no_expire'); // works
                      
            $_SESSION['alerta']="";
            if (isset($_GET['logout'])) {
                //unset($_SESSION['user_id']);
                unset($_SESSION['modulo']);
                unset($_POST['modulo']);
                
                session_destroy();
            }
            include_once './Usuario.php';
            if (isset($_POST["CambiarClave"]) && $_POST["CambiarClave"] == "S") {
                $_POST['username']=$_POST['cod_usuario'];
                $_POST['password']="";
                if (isset($_POST["cod_usuario"]) && isset($_POST["clave_actual"]) && isset($_POST["clave_nueva"]) && isset($_POST["clave_nueva_repetida"])) {
                    //Proceder al cambio de clave

                    $unUser = new Usuario();
                    
                     $usuario= filter_var($_POST['username'],FILTER_SANITIZE_STRING);
                    $clave_actual=filter_var($_POST['clave_actual'],FILTER_SANITIZE_STRING);
                    
                    $valido = $unUser->validarUser($usuario, $clave_actual);
                    if ($valido) {
                        $clave_nueva=filter_var($_POST["clave_nueva"],FILTER_SANITIZE_STRING);
                        $clave_nueva_repetida=filter_var($_POST["clave_nueva_repetida"],FILTER_SANITIZE_STRING);
                        if ($clave_nueva == $clave_nueva_repetida) {
                            $unUser->changePasswordMedical($usuario, $clave_nueva);
                            //$_POST['username']=$_POST['cod_usuario'];
                            $_POST['password']="";
                            
                            $_SESSION['alerta']='<div class="alert alert-success" role="alert">
                                    <h4 class="alert-heading">Cambio de clave</h4>
                                    <p>La clave ha sido cambiada correctamente.</p>
                                  </div>';
                                    
                            
                        }else
                        {
                            $_SESSION['alerta']='<div class="alert alert-danger" role="alert">
                                    <h4 class="alert-heading">Error en cambio de clave</h4>
                                    <p>Las claves no coinciden.</p>
                                  </div>';
                            
                        }
                    } else {
                            $_SESSION['alerta']='<div class="alert alert-danger" role="alert">
                                    <h4 class="alert-heading">Error en cambio de clave</h4>
                                    <p>La clave ingresada es incorrecta.</p>
                                  </div>';
                    }
                }
            }
           //echo var_dump($_POST);
            if (isset($_POST['username']) && isset($_POST['password']) &&  isset($_POST['login']))  {
                
                $unUser = new Usuario();
                $usuario= filter_var($_POST['username'],FILTER_SANITIZE_STRING);
                $clave=filter_var($_POST['password'],FILTER_SANITIZE_STRING);
                
                $valido = $unUser->validarUser($usuario, $clave);
                if ($valido) {
                    
                    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
                    
                    LogAcceso($unUser->usuario,'LoginMedico',"Acceso correcto");
                    $_SESSION['user_id'] = $unUser->usuario;
                    $_SESSION['user_nombre'] = $unUser->nombre.' '.$unUser->apellido;
                    //$_SESSION['usuario']=$unUser;	         	    
                    $_SESSION['alerta']="";
                    $_SESSION['agendas']=$unUser->agendas;
                    $_SESSION['ubicacion_id']=$unUser->ubicacion_id;
                  //  header("location:./listPatients.php");
                    
                  procesarIngreso($unUser);
                } else
                {
                    LogAcceso($usuario,'LoginMedico',"Acceso invalido ".$clave);
                    $_SESSION['alerta']='<div class="alert alert-danger" role="alert">
                                    <h4 class="alert-heading">Error de acceso</h4>
                                    <p>La clave ingresada es incorrecta.</p>
                                  </div>';
                }
            }


?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Eolia PACS</title>
        <link rel="icon" href="favicon.png" sizes="16x16">
        <link rel="stylesheet" href="./css/eoliapacs_login.css">
        <link rel="stylesheet" href="./css/eoliapacs.css">
        <link rel="stylesheet" href="./css/bootstrap.min.css">
        <link rel="stylesheet" href="./fa/css/font-awesome.min.css">
        <script src="./js/jquery.min.js"></script>
        <script src="./js/popper.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>

        <style type="text/css">
            .login-form {
                width: 320px;
                margin: 30px auto;
            }
            .login-form form {
                margin-bottom: 15px;
                background: #f7f7f7;
                box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
                padding: 10px;
                border-radius:  10px 10px 10px 10px;
            }
            .login-form h2 {
                margin: 0 0 15px;
            }
            .form-control, .btn {
                min-height: 38px;
                border-radius: 2px;
            }
            .btn {        
                font-size: 15px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="container">


            <nav class="navbar  navbar-expand-lg sticky-top bg-dark">
                <a class="navbar-brand" href="#">
                    <img src="./img/logo_web.png" width="55" height="50" class="d-inline-block align-top" alt="PACS Eolia">
                    PACS Eolia
                </a>    
                
                
            </nav>

          
            <div class="login-form bg-dark">
                <form action="" method="POST">
                    <h2 class="text-center">Acceder al sistema</h2>       
                    <div class="form-group">
                        <label for="Username">Usuario:</label>
                        <input type="text" class="form-control" placeholder="Username" ID="username" required="required" name="username">
                    </div>
                    <div class="form-group">
                        <label for="Username">Clave:</label>
                        <input type="password" class="form-control" placeholder="Password" id="password" required="required" name="password">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block" name="login" value="">Ingresar</button>
                    </div>

                    <!-- Large modal -->
                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target=".bd-example-modal-lg">Cambiar password</button>
                    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">            
                                <form action="" method="post">
                                    <div class="container"> 
                                        <div class="form-group">
                                            <div class="container">                
                                                <label for="cod_usuario" class="col-form-label"><B>Usuario</B></label>
                                                <input type="text"  class="form-control" id="cod_usuario" name="cod_usuario">
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div class="container">
                                                <label for="clave_actual" class="col-form-label"><B>Clave actual</B></label>               
                                                <input type="password"  class="form-control" id="clave_actual" name="clave_actual">                
                                            </div>
                                        </div>                


                                        <div class="form-group">
                                            <div class="container">
                                                <label for="clave_nueva" class="col-form-label"><B>Clave nueva</B></label>               
                                                <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control" id="clave_nueva" name="clave_nueva" 
                                                       title="Debe contener 8 letras, al menos un numero, una minuscula y una mayuscula" >                
                                            </div>
                                        </div>                

                                        <div class="form-group">
                                            <div class="container">
                                                <label for="clave_nueva_repetida" class="col-form-label"><B>Confirmar Clave nueva</B></label>               
                                                <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"  class="form-control" id="clave_nueva_repetida" name="clave_nueva_repetida"
                                                       title="Debe contener 8 letras, al menos un numero, una minuscula y una mayuscula" >                
                                            </div>
                                        </div>     

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>                
                                            <button type="submit" class="btn btn-warning" id="CambiarClave" name="CambiarClave" value="S">Cambiar Clave</button>
                                        
                                     
                                        </div>
                                        
                                        
                                    </div>
                                </form>
                            </div> 
                                  

                        </div>
                    </div>
                    <?php   
                                if ($_SESSION['alerta']!=''){
                                     echo $_SESSION['alerta'];
                                   //  echo var_dump($_POST);
                                }
                     ?>
                </form>

            </div>
        </div>
    </body>
</html>                                		                            