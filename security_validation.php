<?php

include_once "Connections.php";
include_once "Logger.php";

function isValidSession() {

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['patient_id'])) {
        return false;
    }
    return true;
}


/*Redirigir al Login*/
function goToLogin() {

    if (file_exists("." . DIRECTORY_SEPARATOR . "Login.php")) {
        header("location:." . DIRECTORY_SEPARATOR . "Login.php");
    } else {
        if (file_exists(".." . DIRECTORY_SEPARATOR . "Login.php")) {

            header("location:.." . DIRECTORY_SEPARATOR . "Login.php");
        } else {
            header("location:." . DIRECTORY_SEPARATOR . "Login.php");
        }
    }
}

function validarRangoIPAcceso() {
    $ipCliente = obtenerDireccionIP();

    if (substr($ipCliente, 0, 8) == "192.168.") {
        return true;
    } else {
        die('Rango de acceso no valido');
        return false;
    }
}

function validarLocalhost() {
    $ipCliente = obtenerDireccionIP();

    if (substr($ipCliente, 0, 9) == "127.0.0.1") {
        return true;
    } else {
        die('Rango de acceso no valido');
        return false;
    }
}

$modulo = "";
$user = "";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_POST['salir'])) {
    goToLogin();
    return;
}

if (!(isset($_SESSION['user_id']))) {
    goToLogin();
    return;
} else {
    $user = $_SESSION['user_id'];
}

if (!(isset($_SESSION['modulo']))) {
    goToLogin();
    return;
} else {
    $modulo = $_SESSION['modulo'];
}

$user = filter_var($user, FILTER_SANITIZE_SPECIAL_CHARS);
$bAccesoPermitido = false;
$conMedic = getMedicalConn();
$sql = "select * from usuario_perfil where codigo=$1";
$resultUP = pg_query_params($conMedic, $sql, array($user));

while ($rowUP = pg_fetch_assoc($resultUP)) {

    $perfil = $rowUP['perfil_id'];
    //Perfil 1 es admin, puede acceder a todo.
    if ($perfil == 1) {
        $bAccesoPermitido = true;
        break;
    }
    $sql = "select * from perfiles_funciones where perfil_id=$1 and funcion=$2";
    $result = pg_query_params($conMedic, $sql, array($perfil, $modulo));
    while ($row = pg_fetch_assoc($result)) {
        $bAccesoPermitido = true;
        break;
    }
    if ($bAccesoPermitido) {
        break;
    }
}
$result = null;
$conMedic = null;
//LogAcceso($user, $modulo, "Acceso:".$bAccesoPermitido);

if (!$bAccesoPermitido) {
    require "acceso_invalido.php";

    echo "<center>Acceso denegado a " . $user . "-" . $modulo . "</center>";

    die();
}

