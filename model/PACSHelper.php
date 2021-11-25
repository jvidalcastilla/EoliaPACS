<?php

/* 
 * Esta clase permite obtener datos del paciente desde un informe del PACS Visual Medica
 * 19-8-21 Jorge Vidal
 */

class PACSHelper {
        var $nombre = null;
        var $estudio = null;
        var $file = null;
        var $fecha = null;
        var $anio = null;
        var $mes = null;
        var $dia = null;
        var $firmantes = null;
        var $accessionNumber= null;
        var $aFile=null;
        var $aFilePdf=null;
        var $dni=null;
        var $paciente=null;
        var $informe=null;
        var $html;
        var $urlPdf=null;
        
        
        
        
    public function getInforme($aNode) {
            $nodes2 = $aNode->childNodes;
            $ret = "";
            if ($nodes2 <> null) {
                foreach ($nodes2 as $aNode2) {
                    if ($aNode2->nodeType == XML_TEXT_NODE) {
                        if (trim($aNode2->nodeValue) != "") {

                            $ret = "<p>" . $aNode2->nodeValue . "</p>";
                        }
                    } ELSE {
                        $ret = $ret . $this->getInforme($aNode2);
                    }
                }
            }
            return $ret;
        }

    function recurseNode($aNode) {
            $nodes2 = $aNode->childNodes;
            $ret = "";
            if ($nodes2 <> null) {
                foreach ($nodes2 as $aNode2) {
                    if ($aNode2->nodeType == XML_TEXT_NODE) {
                        if (trim($aNode2->nodeValue) != "") {

                            $ret = $aNode2->nodeValue;
                        }
                    } ELSE {
                        $ret = $ret .$this->recurseNode($aNode2);
                    }
                }
            }
            return $ret;
        }

    public function cargarNombresArchivo(){
            //"Z:\2021\07\23\BARRIA^RENE^HORACIO_1.2.392.200036.9116.2.6.1.44063.1797790429.1627000579.405942.html"
            //"Z:\2021\07\22\MOLINA^MAXIMILIANO 12A_1.2.840.113564.1921680251.20210722140056968450.html"
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->aFile = "Z:\\" . $this->anio . "\\" . $this->mes . "\\" . $this->dia . "\\" . $this->nombre . "_" . $this->estudio . ".html";
            $this->aFilePdf = "Z:\\" . $this->anio . "\\" . $this->mes . "\\" . $this->dia . "\\" . $this->nombre . "_" . $this->estudio . ".pdf";
            $this->urlPdf= $this->aFilePdf;
        }else{
            $this->aFile = "/mnt/InformesPACS/" . $this->anio . "/" . $this->mes . "/" . $this->dia . "/" . $this->nombre . "_" . $this->estudio . ".html";
            $this->aFilePdf = "/mnt/InformesPACS/" . $this->anio . "/" . $this->mes . "/" . $this->dia . "/" . $this->nombre . "_" . $this->estudio . ".pdf";        
            $this->urlPdf= $this->aFilePdf;
        }
        $_SESSION['archivo_pdf']=$this->aFilePdf;
        $_SESSION['archivo_html']=$this->aFile;
    }
    
    //Abre el archivo con los parametros anio mes dia nombre y estudio
    //del archivo obtiene: 
    //  dni del paciente =>$dni
    //  nombre del paciente =>$paciente
    
    public function getValores($anio ,$mes ,$dia ,$nombre,  $estudio){
        $this->anio=$anio;
        $this->mes=$mes;
        $this->dia=$dia;
        $this->nombre=$nombre;
        $this->estudio=$estudio;
        
        $this->cargarNombresArchivo();
    
        //Si el archivo no existe retorno false, sino prosigo. 
        if (!file_exists($this->aFile) ){
            return false;
        }
        
        
        
        //Parseo el xml para obtener los campos de nombre y otros datos
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTMLFile($this->aFile);

        $elements = $doc->getElementsByTagName('body');
        $html = "";
        $informe = "";
        if (!is_null($elements)) {
            foreach ($elements as $element) {
                $html .= $this->recurseNode($element);
                $informe .= $this->getInforme($element);
            }
        }
        
        $iDesde = strPos($informe, "Descripción Estudio:");
        $informe = substr($informe, $iDesde + 24);


        $iDesde = strPos($informe, "</p>");
        $informe = substr($informe, $iDesde + 16);

        $iDniDesde = strpos($html, "Cédula/ID:") + 11;
        $iDniHasta = strpos($html, "Referido Por:");
        $this->dni = substr($html, $iDniDesde, ($iDniHasta - $iDniDesde));
        $iDesde = strpos($html, "Nombre del Paciente:") + 20;
        $iHasta = strpos($html, "Fecha Nacimiento:");
        $this->paciente = substr($html, $iDesde, ($iHasta - $iDesde));
        $this->paciente= str_replace("^", " ", $this->paciente);

        return true;
    }     
    
    
}