<?php

include_once '../model/Connections.php';

class HISAutotexto {
    var $codigo;
    var $texto;
    var $usuario;

    public function deleteByCodigo($codigo,$user){
        $search=trim(strtoupper($codigo));
        try {
            $conn = getMedicalConn();
            pg_query_params($conn,"DELETE FROM AUTOTEXTO P WHERE P.CODIGO=$1 and P.USUARIO=$2",array($search,$user));
            
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
    
    public function getByCodigo($codigo,$user){
        try {
            $search=trim(strtoupper($codigo));
            $this->codigo="";
            $this->texto="";
            $conn = getMedicalConn();
            $rs= pg_query_params($conn,"SELECT * FROM AUTOTEXTO  WHERE CODIGO=$1 and USUARIO=$2",array($search,$user));
            
            while ($aRow= pg_fetch_assoc($rs)){
                $this->codigo=$aRow['codigo'];
                $this->texto=$aRow['texto'];
                $this->usuario=$aRow['usuario'];
            } 
              
        }
        catch(Exception $e) {
                echo $e->getMessage();
            }
       $rs=null;     
    }
    
    public function save($codigo,$texto, $usuario){
        //Quito comillas simples y null.. 
        $codigo= strtoupper(trim($codigo));
        //$texto=SanearTXT($texto);
        try {
            $conn = getMedicalConn();
            $rs= pg_query_params($conn,"SELECT * FROM AUTOTEXTO P WHERE P.CODIGO=$1 and P.USUARIO=$2",array($codigo,$usuario));
            
            if ($rs=pg_fetch_assoc($rs)){
                    $stmt    = pg_query_params($conn, "DELETE FROM AUTOTEXTO WHERE CODIGO=$1 and P.USUARIO=$2",array($codigo,$usuario));
                }
                $stmt   = pg_query_params($conn, "INSERT INTO AUTOTEXTO ( CODIGO,TEXTO, usuario) VALUES($1,$2,$3)", array($codigo,$texto,$usuario));
                   
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        $rs=null;
    }
    
    
    
    /**
     * @return mixed
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    
}