<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function obtenerMenu(){
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $menu='
     <nav class="navbar navbar-expand-sm justify-content-between fondo_panel">
            <div class="nav-item">
            
<button class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuPpal" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-bars"></i>
                       
                    </button>
<div class="dropdown-menu" aria-labelledby="dropdownMenuPpal">
                        <a class="dropdown-item" href="../app/PACSQuery.php">Estudios</a>
                        <a class="dropdown-item" href="../informes/AbmAutotexto.php">Plantillas</a>
                         <a class="dropdown-item" href="../Login.php">Salir</a>
                    </div>

                <img class="navbar-brand" src="../img/logo_web.png" height="50" alt="PACS">
                <p class="navbar-text text-white" href="#">&nbsp;PACS Eolia</p>
            </div>

            <div class="nav-item">
                <div class="dropdown">

                    <div class="badge badge-secondary">
                        <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-file-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4 1h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H4z"/>
                        <path d="M13.784 14c-.497-1.27-1.988-3-5.784-3s-5.287 1.73-5.784 3h11.568z"/>
                        <path fill-rule="evenodd" d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg><div id="cod_usuario">
                        ' . $_SESSION['user_id'] . '
                    </div></div>

                    
                </div>
            </div>
            </hr>';
    return $menu;
}