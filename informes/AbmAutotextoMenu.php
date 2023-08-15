<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function getMenu($tab){
$a="";
$a.='<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">';
$a.='<li class="nav-item">';
$a.='<a class="nav-link ';
if ($tab==0) { $a .= " active ";} 
$a.='" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Crear</a>';
$a.=' </li>';
$a.=' <li class="nav-item">';
$a.=' <a class="nav-link  ';
if ($tab==1) { $a .= " active ";} 
$a.='" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Editar</a>';
$a.='</li></ul>';
return $a;
}



  
    

  
    
  