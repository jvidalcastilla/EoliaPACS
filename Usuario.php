<?php
include_once "Connections.php";

class Usuario{
    var $usuario;
    var $pass_auto;
    var $nombre;
    var $apellido;
    var $agendas;
    var $codi_profesional_rp;
    var $email;
    var $vencimiento;
    var $matricula;
    var $ubicacion_id;
    
    

        
    public function __construct() {
        $this->ubicacion_id=0;
        
    }
    
    public static function addPerfil($codUsuario, $perfilId){
        $conn= getMedicalConn();
        $params=array($codUsuario,$perfilId);
        $sql="select * from usuario_perfil where codigo=$1 and perfil_id=$2";
        $resultUP= pg_query_params($conn,$sql,$params);
        
        if (pg_affected_rows($resultUP)>0){
            return;
        }
        
        $perfiles=array(); 
        $sql="insert into usuario_perfil(codigo, perfil_id) values ($1,$2);";
        
        $rs= pg_query_params($conn,$sql,$params);
        
        
    }
    
     public static function removePerfil($codUsuario, $perfilId){
        $conn= getMedicalConn();
        $params=array($codUsuario,$perfilId);
        $sql="delete from usuario_perfil where codigo=$1 and perfil_id=$2";
        $resultUP= pg_query_params($conn,$sql,$params);
        
        if (pg_affected_rows($resultUP)>0){
            return;
        }
        
    }
    
    
    
    function getUsuario() {
        return $this->usuario;
    }

    function getPass_auto() {
        return $this->pass_auto;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getApellido() {
        return $this->apellido;
    }

    function getAgendas() {
        return $this->agendas;
    }

    function getCodi_profesional_rp() {
        return $this->codi_profesional_rp;
    }

    function getEmail() {
        return $this->email;
    }

    function getVencimiento() {
        return $this->vencimiento;
    }

    function setUsuario($usuario): void {
        $this->usuario = $usuario;
    }

    function setPass_auto($pass_auto): void {
        $this->pass_auto = $pass_auto;
    }

    function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    function setApellido($apellido): void {
        $this->apellido = $apellido;
    }

    function setAgendas($agendas): void {
        $this->agendas = $agendas;
    }

    function setCodi_profesional_rp($codi_profesional_rp): void {
        $this->codi_profesional_rp = $codi_profesional_rp;
    }

    function setEmail($email): void {
        $this->email = $email;
    }

    function setVencimiento($vencimiento): void {
        $this->vencimiento = $vencimiento;
    }

    public function changePasswordMedical($user,$newPass){

    $user= filter_var($user,FILTER_SANITIZE_SPECIAL_CHARS);

    //Valido en medical y luego en RP
    $conMedic= getMedicalConn();
    $claveMD5=md5($newPass);
    $sql="update usuarios set clave=$2, fecha_clave=current_timestamp, vencimiento=(current_timestamp+ interval '1 year') where codigo=$1";
    $result= pg_query_params($conMedic,$sql,array($user,$claveMD5));
    return true;

    }
    
     public function save(){

    $conMedic= getMedicalConn();
   
    $sql="update usuarios set nombre=$1, apellido=$2, email=$3, agendas=$4, matricula=$6 , agendas_sjb=$7 where codigo=$5";
    $params=array($this->nombre,$this->apellido,$this->email,$this->agendas,$this->usuario, $this->matricula, $this->agenda_sjb);
    $result= pg_query_params($conMedic,$sql,$params);
    return true;

    }
    
    
    public function getPerfiles(){
        $conMedic= getMedicalConn();
        $sql="select * from usuario_perfil where codigo=$1";
        $resultUP= pg_query_params($conMedic,$sql,array($this->usuario));
        $perfiles=array(); 
        while  ($rowUP=pg_fetch_assoc($resultUP)){
            $perfiles[$rowUP['perfil_id']]=$rowUP['perfil_id'];            
        }
        $resultUP=null;
        $conMedic=null;
        return $perfiles;

    }

    public function crear(){
    
    $conMedic= getMedicalConn();
   
    $sql="insert into usuarios (nombre,apellido, email,agendas, matricula,codigo, fecha_clave, "
            . "fecha_creacion, vencimiento, codigo_prof_rp, agendas_sjb) values ($1,$2,$3,$4,$5,$6,current_timestamp,"
            . "current_timestamp,current_timestamp,$7,$8)";
    $params=array($this->nombre,$this->apellido,$this->email,$this->agendas, $this->matricula,$this->usuario, $this->codi_profesional_rp, $this->agenda_sjb);
    $result= pg_query_params($conMedic,$sql,$params);
    return $result;

    }
        
    
    
    public function listUsers(){
        $conMedic= getMedicalConn();
        $sql="select * from usuarios order by apellido ";
        $result= pg_query_params($conMedic,$sql,array());
        $usuarios=array();
        while ($row=pg_fetch_assoc($result)){
                $aUser=new Usuario();
                $aUser->loadRow($row);
                $usuarios[$aUser->usuario]=$aUser;
        }
        return $usuarios;
    }
            
    private function loadRow($row){
        $this->usuario=$row['codigo'];
        $this->pass_auto=$row['clave'];
        $this->apellido=$row['apellido'];
        $this->nombre=$row['nombre'];
        $this->agendas=$row['agendas'];  
        $this->matricula=$row['matricula'];  
        $this->vencimiento=$row['vencimiento'];  
        $this->email=$row['email'];  
        $this->ubicacion_id==$row['ubicacion_id'];  
    }
    
    
    
    
    public function getUser($user) {
       // echo $user. '  '.$pass;
        $user= filter_var($user,FILTER_SANITIZE_SPECIAL_CHARS);
         
        //Valido en medical y luego en RP
        $conMedic= getMedicalConn();
        $sql="select * from usuarios where codigo=$1";
        $result= pg_query_params($conMedic,$sql,array($user));
        if ($row=pg_fetch_assoc($result)){
                $this->loadRow($row);
                return true;
                }
            
        
       return false;
    }
    
    
    
    public function validarUser($user,$pass) {
       // echo $user. '  '.$pass;
        $user= filter_var($user,FILTER_SANITIZE_SPECIAL_CHARS);
         
        //Valido en medical y luego en RP
        $conMedic= getMedicalConn();
        $claveMD5=md5($pass);
        $sql="select * from usuarios where codigo=$1";
        $result= pg_query_params($conMedic,$sql,array($user));
        if ($row=pg_fetch_assoc($result)){
                
                $this->usuario=$row['codigo'];
                $this->pass_auto=$row['clave'];
                $this->apellido=$row['apellido'];
                $this->nombre=$row['nombre'];
                $this->ubicacion_id=$row['ubicacion_id'];  
                if ($claveMD5==$this->pass_auto){                    
                    return true;
                }
            
        }
        
       return false;
    }
    
}
