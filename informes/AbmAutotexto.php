<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['modulo'] = "AbmAutotexto";
require_once '../model/security_validation.php';
include_once '../model/Connections.php';
include_once 'Autotexto.php';
include_once '../model/TextFunctions.php';
include_once '../model/PACSHelper.php';
include_once '../model/CInforme.php';
include_once '../menuPrincipal.php';
$usuario = "";
$idPlantilla = "";

$usuario = $_SESSION['user_id'];
$estudio = "";

$autoTexto = new HISAutotexto();

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit') {
        $idAutotexto = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        $autoTexto->getById($idAutotexto);
    }
}

$codigoProf = "";
if (isset($_POST['codigo'])) {
    $codigoProf = $_POST['codigo'];
}

if (isset($_POST['editor1'])) {
    $informe = $_POST['editor1'];
}

$tab = 0;
if (isset($_GET['tab'])) {
    $tab = filter_var($_GET['tab'], FILTER_VALIDATE_INT);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="favicon.png" sizes="16x16">
        <title>Plantillas de informes</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../fa/css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/eoliapacs.css">
        <link rel="stylesheet" href="./AbmAutotexto.css">
    </head>
    <body>
        <div class="container-fluid">
            <?php echo obtenerMenu(); ?>
        </div>
        <div class="container-fluid">

            <nav class="navbar navbar-expand-sm">


                <a class="navbar-brand text-white"> <h5>&nbsp;Plantillas de informes</h5></a>


            </nav>

            <div class="container-fluid">
                <div class='row h-100'>
                    <div class='col-4'>
                        <table class="table table-sm table-striped table-hover bg-secondary">
                            <?php
                            $rs = $autoTexto->getAll();
                            while ($aRow = pg_fetch_assoc($rs)) {
                                $id = $aRow['id'];
                                $texto = $aRow['texto'];
                                $codigo = $aRow['codigo'];

                                echo "<tr>";
                                echo "<td>";
                                echo "<a href='./AbmAutotexto.php?action=edit&id=" . $id . "'>" . $codigo . "</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>

                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#nuevaPlantilla">Nueva plantilla</button>
                    </div>

                    <div class='col-8'>
                        <div class="card bg-light h-100" id="filtros">
                            <form action="" method="post" name="FormEntrar" id="FormEntrar">
                                <div class="row  d-print-none">
                                    <div class="col-12 input-group">
                                        <label for="dniPaciente" class="m-2">Plantilla:</label>
                                        <input type="text" class="form-control form-control-sm m-2" required value ="<?php echo $autoTexto->getCodigo(); ?>" id="codigo" name="codigo"  placeholder="Nombre de plantilla">
                                        <input type="text" hidden class="form-control form-control-sm" value ="<?php echo $autoTexto->getId(); ?>" id="idPlantilla" name="idPlantilla"  placeholder="Nombre de plantilla">
                                    </div>
                                </div> 

                                <div  id="informe_html" hidden>
                                    <?php echo urldecode($autoTexto->getTexto()); ?>
                                </div>
                                <textarea   cols="100" id="editor1" name="editor1" class="h-100" rows="20">
                                </textarea>
                        </div>   
                        <div class="container text-center">
                            <button type="button" class="btn btn-sm btn-success m-2" onclick="grabarAutotexto();"><i class="fa fa-floppy-o"  aria-hidden="true"></i> Grabar 
                            </button>
                        </div>
                    </div>   
                </div>
            </div>

            

            <div class="modal fade" id="nuevaPlantilla" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Nueva Plantilla</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                Ingrese el nombre de la nueva plantilla
                                <input type="text" id="nombreNuevaPlantilla" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="grabarNuevaPlantilla()">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
            <script src="../js/jquery.min.js"></script>
            <script src="../js/popper.min.js"></script>
            <script src="../js/bootstrap.min.js"></script>
            <script src="../js/ckeditor/ckeditor.js"></script>
            <script src="./AbmAutotexto.js"></script>
    </body>
