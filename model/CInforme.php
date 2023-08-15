<?php
include_once '../model/Connections.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 class CInforme{
    var $id;
    var $studyinstanceid ;
    var $informe;
    var $estado;
    var $fecha_creacion ;
    var $fecha_finalizado ;
    var $fecha_eliminado;
    var $solicitante_nom ;
    var $solicitante_matricula ;
    var $pac_nombre ;
    var $pac_afiliado ;
    var $paciente_id ;
    var $pac_os ;
    
    var $stmtExists=null;
    var $stmtEstado=null;
    var $stmtGetInforme=null;
    
    
    function saveInforme(){
        
        $conn= getMedicalConn();
        
        $sql="INSERT INTO public.informes(
	 studyinstanceid, informe, estado, fecha_creacion, fecha_finalizado, fecha_eliminado, 
         solicitante_nom, solicitante_matricula, pac_nombre, pac_afiliado, paciente_id, pac_os)
	VALUES ($1, $2, $3, now(), now() , null, $4, $5, $6, $7, $8, $9)
      ON CONFLICT (studyinstanceid) DO UPDATE SET informe = $2, ESTADO=$3, SOLICITANTE_NOM=$4,
      SOLICITANTE_MATRICULA=$5,pac_nombre=$6,pac_afiliado=$7, paciente_id=$8,pac_os=$9 WHERE informes.studyinstanceid=$1 ;";
        $params=array($this->studyinstanceid
                ,$this->informe
                ,$this->estado
                ,$this->solicitante_nom
                ,$this->solicitante_matricula
                ,$this->pac_nombre
                ,$this->pac_afiliado
                ,$this->paciente_id
                ,$this->pac_os);
        $rs= pg_query_params($conn,$sql,$params);
           
        return (pg_affected_rows($rs));
        
    }
    
    function getId() {
        return $this->id;
    }

    function getStudyinstanceid() {
        return $this->studyinstanceid;
    }

    function getInforme() {
        return $this->informe;
    }

    function getEstado() {
        return $this->estado;
    }

    function getFecha_creacion() {
        return $this->fecha_creacion;
    }

    function getFecha_finalizado() {
        return $this->fecha_finalizado;
    }

    function getFecha_eliminado() {
        return $this->fecha_eliminado;
    }

    function getSolicitante_nom() {
        return $this->solicitante_nom;
    }

    function getSolicitante_matricula() {
        return $this->solicitante_matricula;
    }

    function getPac_nombre() {
        return $this->pac_nombre;
    }

    function getPac_afiliado() {
        return $this->pac_afiliado;
    }

    function getPaciente_id() {
        return $this->paciente_id;
    }

    function getPac_os() {
        return $this->pac_os;
    }

    function setId($id): void {
        $this->id = $id;
    }

    function setStudyinstanceid($studyinstanceid): void {
        $this->studyinstanceid = $studyinstanceid;
    }

    function setInforme($informe): void {
        $this->informe = $informe;
    }

    function setEstado($estado): void {
        $this->estado = $estado;
    }

    function setFecha_creacion($fecha_creacion): void {
        $this->fecha_creacion = $fecha_creacion;
    }

    function setFecha_finalizado($fecha_finalizado): void {
        $this->fecha_finalizado = $fecha_finalizado;
    }

    function setFecha_eliminado($fecha_eliminado): void {
        $this->fecha_eliminado = $fecha_eliminado;
    }

    function setSolicitante_nom($solicitante_nom): void {
        $this->solicitante_nom = $solicitante_nom;
    }

    function setSolicitante_matricula($solicitante_matricula): void {
        $this->solicitante_matricula = $solicitante_matricula;
    }

    function setPac_nombre($pac_nombre): void {
        $this->pac_nombre = $pac_nombre;
    }

    function setPac_afiliado($pac_afiliado): void {
        $this->pac_afiliado = $pac_afiliado;
    }

    function setPaciente_id($paciente_id): void {
        $this->paciente_id = $paciente_id;
    }

    function setPac_os($pac_os): void {
        $this->pac_os = $pac_os;
    }

    function existsInforme($aStudy){
         
        if ($this->stmtExists==null){
            $conn= getMedicalConn();
            $sql="select studyinstanceid from public.informes where studyinstanceid=$1";
            $this->stmtExists= pg_prepare($conn,"existeInforme",$sql);
        }
        $params=array($aStudy);
        //$rs= pg_query_params($conn,$sql,$params);
        $rs= pg_execute("existeInforme",$params);
        $rows=  pg_affected_rows($rs);
        return ($rows>0);
    }

    function getEstadoInforme($aStudy){
         
        if ($this->stmtEstado==null){
            $conn= getMedicalConn();
            $sql="select estado from public.informes where studyinstanceid=$1";
            $this->stmtEstado= pg_prepare($conn,"estadoInforme",$sql);
        }
        $params=array($aStudy);
        $rs= pg_execute("estadoInforme",$params);
        $aRow= pg_fetch_assoc($rs);
        $estado="PND";
        if ($aRow!=null){
            $estado=$aRow['estado'];
        }
        
        return $estado;
    }
    
    
    
    function loadRs($row){
        $this->fecha_creacion=$row['fecha_creacion'];
        $this->fecha_finalizado=$row['fecha_finalizado'];
        $this->fecha_eliminado=$row['fecha_eliminado'];
        $this->informe=$row['informe'];
        $this->solicitante_nom=$row['solicitante_nom'];
        $this->solicitante_matricula=$row['solicitante_matricula'];
        $this->id=$row['id'];
        $this->pac_afiliado =$row['pac_afiliado'];
        $this->pac_nombre =$row['pac_nombre'];
        $this->pac_os =$row['pac_os'];
        $this->paciente_id =$row['paciente_id'];
        $this->estado =$row['estado'];
    }
     
    function getInformeByStudy($aStudy){
         
        if ($this->stmtGetInforme==null){
            $conn= getMedicalConn();
            $sql="select * from public.informes where studyinstanceid=$1";
            $this->stmtGetInforme= pg_prepare($conn,"getInforme",$sql);
        }
        $params=array($aStudy);
        $rs= pg_execute("getInforme",$params);
        $aRow= pg_fetch_assoc($rs);
        $this->loadRs($aRow);
        
        return;
    }
    
    
}