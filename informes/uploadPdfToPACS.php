<?php


function uploadStudy($pdf_path, $study_id){
// Define el ID del estudio existente y la URL de Orthanc
//$study_id = "013f5776-2df7479a-d0969e92-b5a5dc94-aef7e4a2";
$orthanc_url = "http://127.0.0.1:8042";

// Lee el contenido del PDF del archivo
$pdf_content = base64_encode(file_get_contents($pdf_path));

// Crea una instancia en Orthanc y adjunta el PDF
$data = array(
    "Parent" => $study_id,
    "Tags" => array(
        
        "SeriesDescription" => "INFORME"
    ),
    "Content" => "data:application/pdf;base64," . $pdf_content
);

$ch = curl_init($orthanc_url . "/tools/create-dicom");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
$response = curl_exec($ch);
curl_close($ch);

if ($response !== false) {
    $response_data = json_decode($response, true);
    if (isset($response_data['ID'])) {
        $instance_id = $response_data['ID'];
        echo "Created new instance " . $instance_id . PHP_EOL;

        // Lee nuevamente el PDF desde Orthanc
        $ch = curl_init($orthanc_url . "/instances/" . $instance_id . "/pdf");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $pdf_content_response = curl_exec($ch);
        curl_close($ch);

        if ($pdf_content_response !== false) {
            echo "Saving PDF" . PHP_EOL;
            file_put_contents("/tmp/sample-pdf-readback.pdf", $pdf_content_response);
        } else {
            echo "Failed to retrieve PDF from Orthanc" . PHP_EOL;
        }
    } else {
        echo "Failed to create a new instance" . PHP_EOL;
    }
} else {
    echo "Failed to create DICOM instance" . PHP_EOL;
}

}
?>

