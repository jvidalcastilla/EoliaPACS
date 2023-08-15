<?php

include_once '../model/Connections.php';

class HISAutotexto {

    var $id;
    var $codigo;
    var $texto;
    var $usuario;

    public function deleteByCodigo($codigo, $user) {
        $search = trim(strtoupper($codigo));
        try {
            $conn = getMedicalConn();
            pg_query_params($conn, "DELETE FROM AUTOTEXTO P WHERE P.CODIGO=$1 and P.USUARIO=$2", array($search, $user));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $conn = getMedicalConn();
            $rs = pg_query($conn, "SELECT * FROM AUTOTEXTO ORDER BY CODIGO ");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $rs;
    }

    public function getById($id) {
        try {
            $search = trim(strtoupper($id));
            $this->codigo = "";
            $this->texto = "";
            $conn = getMedicalConn();
            $rs = pg_query_params($conn, "SELECT * FROM AUTOTEXTO  WHERE ID=$1", array($search));

            if ($aRow = pg_fetch_assoc($rs)) {
                $this->codigo = $aRow['codigo'];
                $this->texto = $aRow['texto'];
                $this->id = $aRow['id'];
                //$this->usuario = $aRow['usuario'];
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $rs = null;
    }

    public function save($id, $texto,$codigo, $usuario) {
        //Quito comillas simples y null.. 
        // se inserta con id 0
        $codigo = strtoupper(trim($codigo));
        //$texto=SanearTXT($texto);
        try {
            $conn = getMedicalConn();
            if ($id>0){
                $rs = pg_query_params($conn, "SELECT * FROM AUTOTEXTO P WHERE P.ID=$1", array($id));

                if ($rs = pg_fetch_assoc($rs)) {
                    $stmt = pg_query_params($conn, "DELETE FROM AUTOTEXTO WHERE ID=$1", array($id));
                }
                $stmt = pg_query_params($conn, "INSERT INTO AUTOTEXTO (ID, CODIGO,TEXTO, USER_ALTA) VALUES($1,$2,$3,$4)", array($id, $codigo, $texto, $usuario));
            } else {
            $stmt = pg_query_params($conn, "INSERT INTO AUTOTEXTO (CODIGO,TEXTO, USER_ALTA) VALUES($1,$2,$3)", array($codigo, $texto, $usuario));
            
            }
            $rs = null;
            $stmt=null;      
            $conn=null;    
            
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
      
        
        
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getTexto() {
        return $this->texto;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function setCodigo($codigo): void {
        $this->codigo = $codigo;
    }

    function setTexto($texto): void {
        $this->texto = $texto;
    }

    function setUsuario($usuario): void {
        $this->usuario = $usuario;
    }

    function getId() {
        return $this->id;
    }

    function setId($id): void {
        $this->id = $id;
    }


    

}
