
<table class="table PACSTable table-responsive table-hover table-bordered table-striped table-sm w-100 p-0 table-dark" id="estudios">
  <thead class="thead-dark"> <!-- add class="thead-light" for a light header -->
    <tr>
      <th  scope="col" onclick="sortTable(0)" style="width:8%">ID</th>
      <th  scope="col" onclick="sortTable(1)" style="width:24%" Title="Click para ordenar por el campo.">Paciente <i class="fa fa-arrows-v" aria-hidden="true"></i></th>
      <th  scope="col" onclick="sortTable(2)" style="width:10%" Title="Click para ordenar por el campo.">Fecha <i class="fa fa-arrows-v" aria-hidden="true"></i></th>
      <th   scope="col" onclick="sortTable(3)" style="width:5%">Tipo</th>
      <th  scope="col" onclick="sortTable(4)" style="width:25%">Estudio</th>
      <th  scope="col" onclick="sortTable(5)" style="width:5%">Estado</th>
      <th  scope="col" style="width:15%">Accion</th>
      
     <!---
      
      <th style="width:5%"></th>
     ---->
  </thead>
  <tfoot>
   

  <tbody>    
   
   <?php 

function SearchStudies($periodo,$id_pac,$nom_pac, $modalities, $rangoDesde, $rangoHasta)
{
    $unInforme=new CInforme();
    
    $service_url = getPACSServerURL() . '/pacs/tools/find';
    $curlStudies = curl_init($service_url);
    curl_setopt($curlStudies, CURLOPT_USERPWD, getPACSUserPassword());
    curl_setopt($curlStudies, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curlStudies, CURLOPT_RETURNTRANSFER, true);
    
    date_default_timezone_set('UTC');
    date_default_timezone_set("America/Buenos_Aires");
    
    //echo $periodo;
    $periodo = filter_var($periodo, FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
    $filtro='';
    if ($periodo=='hoy')
    {
        $filtro='{"StudyDate":"'. date("Ymd") .'"';
    }elseif ($periodo=='ayer')
    {
        $filtro=date('Ymd', strtotime(date("Y-m-d"). ' - 1 days'));
        $filtro='{"StudyDate":"'. $filtro .'"';
    } elseif ($periodo=='semana')
    {
        $filtro=date('Ymd', strtotime(date("Y-m-d"). ' - 7 days'));
        $filtro2=date('Ymd', strtotime(date("Y-m-d")));
        $filtro='{"StudyDate":"'. $filtro .'-'.$filtro2.'"';
    } elseif ($periodo=='rango')
    {
        $filtro=date('Ymd', strtotime($rangoDesde));
        $filtro2=date('Ymd', strtotime($rangoHasta));
        $filtro='{"StudyDate":"'. $filtro .'-'.$filtro2.'"';
    }  
    elseif ($periodo=='mes')
    {
        $filtro=date('Ymd', strtotime(date("Y-m-d"). ' - 1 month'));
        $filtro2=date('Ymd', strtotime(date("Y-m-d")));
        $filtro='{"StudyDate":"'. $filtro .'-'.$filtro2.'"';
    };
       
  
       
    if (isset($id_pac) && strlen($id_pac)>3){
            $filtro=$filtro.',"PatientID":"'. $id_pac.'"';
        } ;
        
    
    if (isset($nom_pac)&& strlen($nom_pac)>3){
            $nom_pac=trim($nom_pac);    
            $nom_pac= str_replace(" ","*", $nom_pac);
            $filtro=$filtro.',"PatientName":"*'. $nom_pac.'*"';
       }
        
    
    if ($filtro==''){
        $filtro='{"StudyDate":"'. date("Ymd") .'"';
    }
    if ($modalities<>"ALL"){
      //  $filtro =$filtro. ',"Modality":"'.$modalities.'"';
    }
     
    $filtro .="}";
    $filtro .=',"Limit":500';
    $query = '{"Level":"Studies","Query": '.$filtro.'}';
    //echo $query;
   
 
    curl_setopt($curlStudies, CURLOPT_POST, 1);
    curl_setopt($curlStudies, CURLOPT_POSTFIELDS, $query);
    
    $curlQuery_response = curl_exec($curlStudies);
    if ($curlQuery_response === false) {
        $info = curl_getinfo($curlStudies);
        curl_close($curlStudies);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curlStudies);
    $decodedQuery = json_decode($curlQuery_response);
   //  echo $decodedQuery;
    if (isset($decodedQuery->response->status) && $decodedQuery->response->status == 'ERROR') {
        die('error occured: ' . $decodedQuery->response->errormessage);
    }
    
    foreach ($decodedQuery as &$aStudy) {
        getStudyDetails($aStudy,$modalities,$unInforme);
    }
       
}

function translateBodyPart($bodyPart){
    if ($bodyPart=='ANKLE'){
        return "Tobillo";
    } elseif ($bodyPart=='BREAST'){
        return "Mamas";
    } elseif ($bodyPart=='FOOT'){
        return "Pie";
    } elseif ($bodyPart=='SHOULDER'){
        return "Hombro";
    } elseif ($bodyPart=='SPINE'){
        return "Columna";     
    } elseif ($bodyPart=='KNEE'){
        return "Rodilla";    
    } elseif ($bodyPart=='PATELLA'){
        return "Rotula";    
    } elseif ($bodyPart=='SPINE'){
        return "Espina";    
    } elseif ($bodyPart=='HAND'){
        return "Mano";
    } elseif ($bodyPart=='CHEST'){
        return "Pecho";
    } elseif ($bodyPart=='SKULL'){
        return "Craneo";
    } elseif ($bodyPart=='FOREARM'){
        return "Antebrazo";      
    } elseif ($bodyPart=='HIP'){
        return "Cadera";
    } elseif ($bodyPart=='CLAVICLE'){
        return "Clavicula";
    } elseif ($bodyPart=='WRIST'){
        return "Muneca";
    } elseif ($bodyPart=='ELBOW'){
        return "Codo";
     } elseif ($bodyPart=='HUMERUS'){
        return "Humero";
     } elseif ($bodyPart=='ABDOMEN'){
        return "Abdomen";
    } elseif ($bodyPart=='CABEZA'){
        return "Cabeza";        
    } elseif ($bodyPart=='COLUMNAS'){
        return "Columna";        
     } elseif ($bodyPart=='TORAX'){
        return "Torax";
    } else
    {
        return $bodyPart;
    }
    
}

function translateModality($modType){
       return $modType;
}


function getStudyDetails($aStudy,$modalities, $unInforme){
    //   echo "Getting study details for:".$aStudy."</br>";
    
    $service_url = getPACSServerURL() . '/pacs/studies/'.$aStudy;
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
    
    $studyDate=$decodedQuery->MainDicomTags->StudyDate;
    $patientName=$decodedQuery->PatientMainDicomTags->PatientName;
    $patientName=str_replace("^", " ", $patientName);
    $encodedPatientName=urlencode($patientName);
    $patientId=$decodedQuery->PatientMainDicomTags->PatientID;
    $patientName="<A HREF='http://".getPACSServerIP().":".getPACSServerPort()."/pacs/osimis-viewer/app/index.html?study=".$decodedQuery->ID."'  target='_blank' id='pac_".$patientName."'>".$patientName."</a>";
    $referringPhysicianName=$decodedQuery->MainDicomTags->ReferringPhysicianName;
    $formatedDate = DateTime::createFromFormat('Ymd', $studyDate)->format('d/m/Y');
    
    $studyLink="<A HREF='http://".getPACSServerIP().":".getPACSServerPort()."/pacs/osimis-viewer/app/index.html?study=".$decodedQuery->ID."'  target='_blank'>".$formatedDate."</a>";
    $btnVisualizar="<A HREF='http://".getPACSServerIP().":".getPACSServerPort()."/pacs/osimis-viewer/app/index.html?study=".$decodedQuery->ID."'  target='_blank'><button class='btn btn-outline-secondary btn-sm boton_grilla'> <i class='fa fa-eye' aria-hidden='true'></i></button></a>";
            
            
    $downloadLink="<A HREF='http://".getPACSServerIP().":".getPACSServerPort()."/pacs/studies/".$decodedQuery->ID."/media' download='paciente' class='btn btn-outline-secondary btn-sm boton_grilla'> <i class='fa fa-download' aria-hidden='true'></i> </a>";
    
    $downloadLink=$downloadLink."<A HREF='./Pacs/openQR.php?id=".$aStudy."&nom_pac=".($encodedPatientName)."' target='_blank'  class='btn btn-outline-secondary btn-sm boton_grilla'><i class='fa fa-copy' aria-hidden='true'></i> </A>";
    
    $qrLink="<A HREF='./Pacs/GenerarQR.php?Study=".$aStudy."&nom_pac=".($encodedPatientName)."' target='_blank' class='btn btn-outline-secondary btn-sm boton_grilla'><i class='fa fa-qrcode' aria-hidden='true'></i></A>";
    //$emailLink="<A HREF='./Pacs/SendEmail.php?Study=".$aStudy."&nom_pac=".($encodedPatientName)."' target='_blank'><img src='./images/qr.png' alt='QR' height='16' width='16'> Email</A>";
    //class="btn_mail" <svg width="0.9em" height="0.9em" viewBox="0 0 16 16" class="bi bi-envelope" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  //<path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
//</svg>'
    $emailLink='<button type="button" class="btn btn-outline-secondary btn-sm boton_grilla" id="'.$patientId.'", data-toggle="" data-target="#sendEmailPopup" onClick="sendEmail(this.value, this.id)" value="'.$aStudy.'">'
            . '<i class="fa fa-envelope"></i>'
            . '</button>';
    
    
    $informeBtn='<button type="button" class="btn btn-outline-secondary btn-sm boton_grilla" id="'.$patientId.'" onClick="emitirInforme(this.value, this.id)" value="'.$aStudy.'">'
            . '<i class="fa fa-pencil-square-o"></i>'
            . '</button>';
    
    
    $existeInforme=$unInforme->existsInforme($aStudy);
    $colorBoton="btn-outline-secondary";
    if ($existeInforme){
        $colorBoton="btn-outline-success";
    }
        
    
    $data=array();
    $data['action']='informar';
    $data['uid']=$aStudy;
    $data['patientId']=$patientId;
    $data['name']=$encodedPatientName;
    $data['fecha']=DateTime::createFromFormat('Ymd', $studyDate)->format('Y-m-d');
    $data['referringPhysicianName']=$referringPhysicianName;
    $data['informe']=$existeInforme;
    $unParametro= urlencode(json_encode($data));
    $informeBtn='<form action="../informes/EmitirInforme.php" target="_blank" method="post" id="form_inf" class="form-inline">'
            . '<button class="btn '.$colorBoton.' btn-sm boton_grilla" name="param" id="'.$patientId.'" value="'.$unParametro.'">'
            . '<i class="fa fa-pencil-square-o"></i>'
            . '</button>'
            . '</form>';
    
    $detalleAnt="";
    $tipoModality="";
    
    foreach ($decodedQuery->Series as &$serie) {
        
        $service_url = getPACSServerURL().'/pacs/series/' . $serie;
        $seriecurl = curl_init($service_url);
        curl_setopt($seriecurl, CURLOPT_USERPWD,getPACSUserPassword());
        curl_setopt($seriecurl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($seriecurl, CURLOPT_RETURNTRANSFER, true);
        $seriecurl_response = curl_exec($seriecurl);
        if ($seriecurl_response === false) {
            $info = curl_getinfo($seriecurl);
            curl_close($seriecurl);
            die('error occured during curl exec. Additioanl info: ' . var_export($info));
        }
        
        $detalleSerie = json_decode($seriecurl_response);
        $tipoModality=translateModality($detalleSerie->MainDicomTags->Modality);
        $detalleEstudio=" ";
        
        if (property_exists($detalleSerie->MainDicomTags,"BodyPartExamined")) {
            $bodyPart= $detalleSerie->MainDicomTags->BodyPartExamined;
            $bodyPart=translateBodyPart($bodyPart);
                       
            $detalleEstudio=$detalleEstudio.$bodyPart;
        }
                     
        $aPos=strpos($detalleAnt, $detalleEstudio);
        if ($aPos===false){
            
            $detalleAnt=$detalleAnt.$detalleEstudio." ";
            $preview= $detalleSerie->Instances[0];
        }
    }
    if ($modalities==$tipoModality || $modalities=="ALL"){
    echo '<tr>';
    echo '<td>'.$patientId.'</td>';
    echo '<td>'.$patientName.'</td>';
    echo '<td>'.$studyLink.'</td>';
    echo '<td>'.$tipoModality.'</td>';
    echo '<td>'.$detalleAnt.'</td>';
    echo '<td>Pendiente</td>';
    echo '<td class="form-row p-0 m-0">'.$informeBtn.$btnVisualizar;
    echo $downloadLink;
    echo $qrLink;     
    
    if ($tipoModality<>'CT'){
        echo $emailLink;     
    } 
    echo '</td>';     
        
    echo '</tr>';
    }
}


function showPatientImages($dni){
    
    $service_url = getPACSServerURL() . '/pacs/tools/find';
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_USERPWD, getPACSUserPassword());
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $consulta = '{"Level":"Patient","Query": {"PatientID":"*'.$dni.'*"}}';
    
    // echo $consulta;
    // echo "<br>";
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $consulta);
    
    $curl_response = curl_exec($curl);
    //echo "Procesando resultados...</br>";
    
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);
    $decoded = json_decode($curl_response);
    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
        die('error occured: ' . $decoded->response->errormessage);
    }
    
    foreach ($decoded as &$valor) {
        
        $service_url = getPACSServerURL().'/pacs/patients/' . $valor;
        $paccurl = curl_init($service_url);
        curl_setopt($paccurl, CURLOPT_USERPWD,getPACSUserPassword());
        curl_setopt($paccurl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($paccurl, CURLOPT_RETURNTRANSFER, true);
        $paccurl_response = curl_exec($paccurl);
        if ($paccurl_response === false) {
            $info = curl_getinfo($paccurl);
            curl_close($paccurl);
            die('error occured during curl exec. Additional info: ' . var_export($info));
        }
        
        $patient = json_decode($paccurl_response);
        foreach ($patient->Studies as $aStudy) {
            getStudyDetails($aStudy,$dni);
        }
    }
}
$periodo="rango";
SearchStudies($periodo,$id_pac,$nom_pac,$modalities, $rango_desde,$rango_hasta);
?>

   
</tbody></table>     
  
