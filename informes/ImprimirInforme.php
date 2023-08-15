<?php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    
    
    session_cache_limiter('private_no_expire'); // works
    
    
    if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
    }

    $studyInstance= filter_var($_GET['studyinstance'],FILTER_SANITIZE_STRING);
    $dniPaciente="";
    if (isset($_GET['dniPaciente'])){
        $dniPaciente=filter_var($_GET['dniPaciente'],FILTER_SANITIZE_STRING);
    }
    
    
    $_SESSION['modulo'] = "EmitirInformes";
    require_once '../model/security_validation.php';
    include_once '../model/Connections.php'; 
    include_once 'Autotexto.php';
    include_once '../model/TextFunctions.php';
    include_once '../model/CInstitucion.php';
    include_once '../model/PACSHelper.php';
    include_once '../model/CInforme.php';
    
    
    $unInforme=new CInforme();
    $unInforme->getInformeByStudy($studyInstance);
    $informe=$unInforme->getInforme();
    $afiliado=$unInforme->getPac_afiliado();
    $pacObraSocial=$unInforme->getPac_os();
    $nom_paciente="";
    if ($unInforme->getPac_nombre()!=""){
        $nom_paciente=$unInforme->getPac_nombre();
    }
    if ($unInforme->getSolicitante_nom()!=""){
        $solicitante=$unInforme->getSolicitante_nom();
    }
    $institucion= new CInstitucion();
    $institucion ->getInstitucion();
    
    
$informe='
<head>
    <html lang="es" xml:lang="es">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="favicon.png" sizes="16x16">
        <title>Imprimir informe</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../fa/css/font-awesome.min.css">
        <link rel="stylesheet" href="./ImprimirInforme.css">
        <script src="../js/jquery.min.js"></script>
        <script src="../js/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
    </head>

    
    <body>
        <style>
            .titulo{
              font-size: larger;
              font-weight: bold;
            }
        </style>

        <div class="encabezado">
            &nbsp;
        </div>
            <table>
            <tr>
                <td style="width:50%">
                    <img class="mr-3" src="./logo.jpg" alt="logo" 
                    >
                </td>
                <td class="pl-5">
                    </BR>
                    <span class="titulo">'.$institucion->getNombre().'<BR></span>
                    <margen>'.$institucion->getDireccion().'<BR></margen>
                    <margen>Tel:'.$institucion->getTelefonos().'<BR></margen>
                    <margen>E-Mail:'.$institucion->getEmail().'<BR></margen>
                </td>
            </tr>    
            </table>            
            <br>           
            <div class="card ml-2 mr-5 bg-light">
                <div class="card-text padtit">Paciente: <B><tipo_pac>'. $nom_paciente." ".$dniPaciente.'</tipo_pac></B></div>
                <div class="card-text padtit">Afiliado / Obra social:<B>  &nbsp;'.$afiliado . " " . $pacObraSocial.'</B>  </div>
                <div class="card-text padtit" id="lblProfesional"></div>
                <div class="card-text padtit" id="lblFecha"></div>
            </div>
            </br>            
            <div id="print_helper" class="pl-2">
                '. 
                $informe.'
            </div>
        </div>   
    </body>
</html>
';
use Dompdf\Dompdf;
$emitirPDF=true;

if ($emitirPDF){
require_once 'dompdf/autoload.inc.php';
// reference the Dompdf namespace


// instantiate and use the dompdf class
$dompdf = new Dompdf(["dpi" => 300,"chroot" => __DIR__]);
$dompdf->loadHtml($informe);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');
$options = $dompdf->getOptions();
$options->setDefaultFont('Courier');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser

$dompdf->stream();
$output = $dompdf->output();
$pdf_file="../docs/$studyInstance.pdf";
file_put_contents($pdf_file, $output);

include_once ("./uploadPdfToPACS.php");
uploadStudy($pdf_file,$studyInstance);
}
$mostrarHTML=true;
if ($mostrarHTML){
    echo $informe;
}

