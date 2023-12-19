<?php
/*
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
*/


session_cache_limiter('private_no_expire'); // works


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$addImages = filter_var($_GET['addImages'], FILTER_SANITIZE_STRING);
$studyInstance = filter_var($_GET['studyinstance'], FILTER_SANITIZE_STRING);
$dniPaciente = "";
if (isset($_GET['dniPaciente'])) {
    $dniPaciente = filter_var($_GET['dniPaciente'], FILTER_SANITIZE_STRING);
}


$_SESSION['modulo'] = "EmitirInformes";
require_once '../model/security_validation.php';
include_once '../model/Connections.php';
include_once 'Autotexto.php';
include_once '../model/TextFunctions.php';
include_once '../model/CInstitucion.php';
include_once '../model/PACSHelper.php';
include_once '../model/CInforme.php';


$unInforme = new CInforme();
$unInforme->getInformeByStudy($studyInstance);
$informe = $unInforme->getInforme();
$afiliado = $unInforme->getPac_afiliado();
$pacObraSocial = $unInforme->getPac_os();
$nom_paciente = "";
if ($unInforme->getPac_nombre() != "") {
    $nom_paciente = $unInforme->getPac_nombre();
}
if ($unInforme->getSolicitante_nom() != "") {
    $solicitante = $unInforme->getSolicitante_nom();
}
$institucion = new CInstitucion();
$institucion->getInstitucion();


$informe = '
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
            .dicom {
                width:85%;
                
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
                    <span class="titulo">' . $institucion->getNombre() . '<BR></span>
                    <margen>' . $institucion->getDireccion() . '<BR></margen>
                    <margen>Tel:' . $institucion->getTelefonos() . '<BR></margen>
                    <margen>E-Mail:' . $institucion->getEmail() . '<BR></margen>
                </td>
            </tr>    
            </table>            
            <br>           
            <div class="card ml-2 mr-5 bg-light">
                <div class="card-text padtit">Paciente: <B><tipo_pac>' . $nom_paciente . " " . $dniPaciente . '</tipo_pac></B></div>
                <div class="card-text padtit">Afiliado / Obra social:<B>  &nbsp;' . $afiliado . " " . $pacObraSocial . '</B>  </div>
                <div class="card-text padtit" id="lblProfesional"></div>
                <div class="card-text padtit" id="lblFecha"></div>
            </div>
            </br>            
            <div id="print_helper" class="pl-2">
                ' .
        $informe . '
            </div>
        </div>   

';

//----------------- MOSTRAR IMAGENES
function imprimirStudy($studyInstance) {
    $service_url = getPACSServerURL() . '/studies/' . $studyInstance;
    $curlStudyDetail = curl_init($service_url);
    curl_setopt($curlStudyDetail, CURLOPT_USERPWD, getPACSUserPassword());
    curl_setopt($curlStudyDetail, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curlStudyDetail, CURLOPT_RETURNTRANSFER, true);

    $curlQuery_response = curl_exec($curlStudyDetail);
    if ($curlQuery_response === false) {
        $info = curl_getinfo($curlStudyDetail);
        curl_close($curlStudyDetail);
        die('error occured during curl exec. Additional info: ' . var_export($info));
    }
    curl_close($curlStudyDetail);

    $decodedQuery = json_decode($curlQuery_response);

    if (isset($decodedQuery->response->status) && $decodedQuery->response->status == 'ERROR') {
        die('error occured: ' . $decodedQuery->response->errormessage);
    }
    $imagenes="";
    foreach ($decodedQuery->Series as &$serie) {
        $imagenes.= showSerie($serie);
    }
    return $imagenes;
}


function showSerie($serie) {


    $ret = '<div class="container-fluid">';

    $service_url = getPACSServerURL() . '/series/' . $serie;
    $seriecurl = curl_init($service_url);
    curl_setopt($seriecurl, CURLOPT_USERPWD, getPACSUserPassword());
    curl_setopt($seriecurl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($seriecurl, CURLOPT_RETURNTRANSFER, true);
    $seriecurl_response = curl_exec($seriecurl);
    if ($seriecurl_response === false) {
        $info = curl_getinfo($seriecurl);
        curl_close($seriecurl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }

    $detalleSerie = json_decode($seriecurl_response);

    $cantImages = sizeof($detalleSerie->Instances);
    for ($numInstance = 0; $numInstance <= $cantImages - 1; $numInstance++) {
        $preview = $detalleSerie->Instances[$numInstance];
        $fileName = "./preview/P" . $serie . $numInstance . ".png";
        $dataUri = "";
        if (!file_exists($fileName)) {
            //$service_url = getPACSServerURL() . '/instances/' . $preview . "/preview";
            $service_url = getPACSServerURL() . '/instances/' . $preview . "/rendered?width=200";
            $aFile = fopen($fileName, 'w+');
            $serie_preview = curl_init($service_url);
            curl_setopt($serie_preview, CURLOPT_USERPWD, getPACSUserPassword());
            curl_setopt($serie_preview, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($serie_preview, CURLOPT_FILE, $aFile);
            $preview_response = curl_exec($serie_preview);
            $dataUri = 'data:image/png;base64,' . base64_encode($preview_response);
            fwrite($aFile, $preview_response);
            fclose($aFile);
        }
        $type = pathinfo($fileName, PATHINFO_EXTENSION);
        $data = file_get_contents($fileName);
        $dataUri = 'data:image/png;base64,' . base64_encode($data);
        //Descarto los preview sin datos
        if (filesize($fileName) > 2048) {
            $ret .= '<div class="row p-5">';
            $ret .= '<div class="col-12 text-center">';
            $ret .= "<img name='imagen' class='dicom' id='imagen' src='" . $dataUri . "'>";
            $ret .= "</div>";
            $ret .= "</div>";
        }
    }
    return $ret.'</div>';
}



//------------------- fin mostrar imagenes

use Dompdf\Dompdf;


$mostrarHTML = false;

if ($addImages) {
    require_once 'dompdf/autoload.inc.php';
// reference the Dompdf namespace
// instantiate and use the dompdf class
    $dompdf = new Dompdf(["dpi" => 300, "chroot" => __DIR__]);

    $imagenes=imprimirStudy($studyInstance);
    
    $dompdf->loadHtml($informe.$imagenes."</body></html>");
    
// (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');
    $options = $dompdf->getOptions();
    $options->setDefaultFont('Courier');

// Render the HTML as PDF
    $dompdf->render();

// Output the generated PDF to Browser

    
    $dompdf->stream();
    $output = $dompdf->output();
    $pdf_file = "../docs/$studyInstance.pdf";
    file_put_contents($pdf_file, $output);

    include_once ("./uploadPdfToPACS.php");
    uploadStudy($pdf_file, $studyInstance);
}

if ($mostrarHTML) {
   // echo $informe;
   // imprimirStudy($studyInstance);
}