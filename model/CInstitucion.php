<?php
include_once '../model/Connections.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 class CInstitucion{
    var $id;
    var $nombre;
    var $email;
    var $telefonos;
    var $direccion;
    var $localidad;
    var $logo_informes;
    var $logo_login;
    
    var $stmtExists=null;
    var $stmtGetInforme=null;
     
    const INSTITUCION=1;
     
    function getInstitucion(){
        if ($this->stmtExists==null){
            $conn= getMedicalConn();
            $sql="select * from public.institucion where id=$1";
            $this->stmtExists= pg_prepare($conn,"getInstitucion",$sql);
        }
        $params=array(self::INSTITUCION);
        //$rs= pg_query_params($conn,$sql,$params);
        $rs= pg_execute("getInstitucion",$params);
        $rows=  pg_affected_rows($rs);
        $row= pg_fetch_assoc($rs);
        if ($rows>0){
            $this->setDireccion($row['direccion']);
            $this->setEmail ($row['email']);
            $this->setLocalidad ($row['localidad']);
            $this->setNombre ($row['nombre']);
            $this->setTelefonos ($row['telefonos']);
            $this->setDireccion ($row['direccion']);
            $this->setLogo_informes ($row['logo_informes']);
            $this->setLogo_login ($row['logo_login']);
        }
        
    } 
    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getEmail() {
        return $this->email;
    }

    function getTelefonos() {
        return $this->telefonos;
    }

    function getDireccion() {
        return $this->direccion;
    }

    function getLocalidad() {
        return $this->localidad;
    }

    function getStmtExists() {
        return $this->stmtExists;
    }

    function getStmtGetInforme() {
        return $this->stmtGetInforme;
    }

    function setId($id): void {
        $this->id = $id;
    }

    function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    function setEmail($email): void {
        $this->email = $email;
    }

    function setTelefonos($telefonos): void {
        $this->telefonos = $telefonos;
    }

    function setDireccion($direccion): void {
        $this->direccion = $direccion;
    }

    function setLocalidad($localidad): void {
        $this->localidad = $localidad;
    }

    function setStmtExists($stmtExists): void {
        $this->stmtExists = $stmtExists;
    }

    function setStmtGetInforme($stmtGetInforme): void {
        $this->stmtGetInforme = $stmtGetInforme;
    }
    function getLogo_informes() {
        return $this->logo_informes;
    }

    function getLogo_login() {
        return $this->logo_login;
    }

    function setLogo_informes($logo_informes): void {
        $this->logo_informes = $logo_informes;
    }

    function setLogo_login($logo_login): void {
        $this->logo_login = $logo_login;
    }



     
 }