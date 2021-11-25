<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'Connections.php';

function getUrlForStudy($aStudy){
    
    $studyLink="http://".getPACSUserPassword()."@".getPACSServerIP().":".getPACSServerPort()."/osimis-viewer/app/index.html?study=".$aStudy;
    return $studyLink;
}

function SearchStudies($periodo,$id_pac, $modalities)
{
    //Validar que el acceso a la funcion sea dentro de la red local.
    //if (!validarRangoIPAcceso()) {
	 //		return false;
	//		}
      

	
    $service_url = getPACSServerURL() . '/tools/find';
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
    } elseif ($periodo=='todos')
    {
        $filtro=date('Ymd', strtotime(date("Y-m-d"). ' - 99999 days'));
        $filtro2=date('Ymd', strtotime(date("Y-m-d")));
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
    
    if ($filtro==''){
        $filtro='{"StudyDate":"'. date("Ymd") .'"';
    }
    
    $filtro =$filtro. ',"Modality":"'.$modalities.'"}';
    
    $query = '{"Level":"Studies","Query": '.$filtro.'}';
   // echo $query;
 
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
    // echo $curl_response;
    if (isset($decodedQuery->response->status) && $decodedQuery->response->status == 'ERROR') {
        die('error occured: ' . $decodedQuery->response->errormessage);
    }
    
    $resultado=null;
    foreach ($decodedQuery as $aStudy) {
        $resultado=getStudyDetails($aStudy);
    }
 return $resultado;      
}


function getStudyDetails($aStudy){
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
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curlStudyDetail);
    $decodedQuery = json_decode($curlQuery_response);
    $patientName=$decodedQuery->PatientMainDicomTags->PatientName;
    $patientName=str_replace("^", "_", $patientName);
    $studyDate=$decodedQuery->MainDicomTags->StudyDate;
    $formatedDate = DateTime::createFromFormat('Ymd', $studyDate)->format('d-m-Y');
    
    if (isset($decodedQuery->response->status) && $decodedQuery->response->status == 'ERROR') {
        die('error occured: ' . $decodedQuery->response->errormessage);
    }
    $resultado="";
    $detalleAnt="";
    $cant=0;
    foreach ($decodedQuery->Series as $serie) {
        
        $resultado=$resultado.savePreviewSerie($serie,$patientName,$formatedDate,$cant);
        
        }
    return explode("|",$resultado);
    }
    

function savePreviewSerie($serie,$patientName,$formatedDate,&$cant){
    
        $directorioBase="./";
        if (!file_exists('./preview/')){
            if (file_exists('../preview/')){
                $directorioBase="../";
            }
        }
    
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
        $resultado="";
        $detalleSerie = json_decode($seriecurl_response);
        $cantImages= sizeof($detalleSerie->Instances);
        for ($numInstance=0; $numInstance<=$cantImages-1;$numInstance++){
        	
	        $preview= $detalleSerie->Instances[$numInstance];
	     //   $fileName=$directorioBase."preview/".$patientName."-".
                
	        //$fileName= $directorioBase."/preview/".$patientName."-".$preview.".png";
                $fileName= $directorioBase."/preview/".$patientName."-".$formatedDate."-".$cant++.".png";
                 //$fileName= "../preview/".$patientName."-".$formatedDate."-".$idx.".png";
                //$fileName= "../preview/".$preview.".png";
	        if (!file_exists($fileName)){
		        $service_url = getPACSServerURL().'/pacs/instances/' . $preview."/preview";
		        $aFile=fopen($fileName,'w+');
		        $serie_preview = curl_init($service_url);
		        curl_setopt($serie_preview, CURLOPT_USERPWD,getPACSUserPassword());
		        curl_setopt($serie_preview, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		        curl_setopt($serie_preview, CURLOPT_FILE, $aFile);
		    
		        $preview_response=curl_exec($serie_preview);
		        fwrite($aFile,$preview_response);
		        fclose($aFile);
                        if (filesize($fileName)>10){
                            $resultado=$resultado."|".$fileName;
                        }else
                        {
                            unlink($fileName);
                        }
	       	}
                else
                {
                    $resultado=$resultado."|".$fileName;
                }
	       	
	    }
    return $resultado;
}