/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



var ACCESSION_NUMBER = '<?php echo $accessionNumber;?>';
var ARCHIVO_INFORME = '<?php echo $archivo;?>';



function getCodUsuario(){
    let s=document.getElementById("cod_usuario").innerText;
    return s;
}

function getFirmante(){
     let firmante=document.getElementById("firmante").value;
     return firmante;
}

function grabarInforme(){
    
    
    
    let informe= CKEDITOR.instances["editor1"].getData();
   // let separador="</p>\n"
    informe=encodeURIComponent(informe);
    
    let solicitante=document.getElementById("solicitante").value;
    let nom_paciente=encodeURI(document.getElementById("nom_paciente").value);
    let dniPaciente=encodeURI(document.getElementById("dniPaciente").value);
    let fecha=encodeURI(document.getElementById("fecha_estudio").value);
    let afiliado=encodeURI(document.getElementById("afiliado").value);
    let obraSocial=encodeURI(document.getElementById("obraSocial").value);
    let usuario=getCodUsuario();
    let firmante=getFirmante();
    let studyInstance=encodeURI(document.getElementById("studyInstance").innerHTML.trim());
    
//    alert (dniPaciente);
    $link="./wsSaveInforme.php?informe="+informe+"&dni="+dniPaciente+"&solicitante="+solicitante+"&paciente="+nom_paciente+"&afiliado="+afiliado+"&obraSocial="+obraSocial+"&usuario="+usuario+"&fecha="+fecha+"&firmante="+firmante+"&studyInstance="+studyInstance;
    getRequest($link, informeGrabado, informeError)
    
}

function informeGrabado(response){
    alert(response);
}

function informeError(response){
    alert(response);
}


function buscar_pac(event) {
    if (event.keyCode == 13 || event.which == 13) {
        buscarPaciente();
    }

}
function getUsuario() {
    return document.getElementById("cod_usuario").innerText;
}
function getAutoTexto(codigo, cursorPosition) {
    //    alert(codigo);
    var usuario = getUsuario();
    getRequest(
            'getAutoTexto.php?codigo=' + codigo + "&user=" + usuario + "&position=" + cursorPosition, // URL for the PHP file
            insertaAutotexto, // handle successful request
            errorAutotexto    // handle error
            );
    return false;
}




// helper function for cross-browser request object
function getRequest(url, success, error) {
    var req = false;
    try {
        // most browsers
        req = new XMLHttpRequest();
    } catch (e) {
// IE
        try {
            req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
// try an older version
            try {
                req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                return false;
            }
        }
    }
    if (!req)
        return false;
    if (typeof success != 'function')
        success = function () {};
    if (typeof error != 'function')
        error = function () {};
    req.onreadystatechange = function () {
        if (req.readyState == 4) {
            return req.status === 200 ?
                    success(req.responseText) : error(req.status);
        }
    }
//  alert (url);
    req.open("GET", url, true);
    req.send(null);
    return req;
}



function envioMail(responseText) {
    if (responseText === "-1") {
        alert("1-Error al grabar el informe:" + responseText);
        return;
    }
    let paciente = document.getElementById("lbl_nomPaciente").innerHTML;
    let mailto = document.getElementById("email").value;
    let archivo = document.getElementById("archivo").innerHTML;
    // alert("Archivo:" + archivo);
    let accessionNumber = ACCESSION_NUMBER;
    getRequest(
            'wsEnviarEmail.php?mailto=' + mailto + "&paciente=" + paciente + "&file=" + archivo + "&accessionNumber=" + accessionNumber, // URL for the PHP file
            insertaAutotexto, // handle successful request
            errorAutotexto    // handle error
            );
}

function grabaImpresionOK(responseText) {
    //console.log(responseText);
    if (responseText.substring(0, 2) != "OK") {
        alert("Error al grabar el informe: " + responseText);
        return;
    }


    let filtros = document.getElementById("filtros");
    filtros.hidden = true;
    let texto = document.getElementById("texto_ingresado");
    texto.hidden = true;
    window.print();
    //btn_limpiar.click(); 
}


function limpiar() {
    return;
    dniPaciente.value = "";
    codigo.value = "";
    document.getElementById("lbl_nomPaciente").innerHTML = "";
    document.getElementById("lbl_dirPaciente").innerHTML = "";
    document.getElementById("informe").value = "";
    var x = document.getElementById("opt_profesional");
    while (x.length > 0) {
        x.remove(x.length - 1);
    }
    document.getElementById("dniPaciente").value = "";
    FormEntrar.submit();
}

function buscarPaciente() {
    pac = encodeURIComponent(bApeNombre.value);
    //   alert("Buscar "+ pac);
    getRequest("wsPacientes.php?id_paciente=" + pac, pacienteEncontrado, pacienteError);
}


function tblPacientesSel(clicked_id) {

    var rowID = clicked_id.substring(12);
    var row = document.getElementById(rowID);
    var strDni = row.cells[2].innerHTML;
    var strNombre = row.cells[1].innerHTML;
    var strDireccion = row.cells[4].innerHTML;
    dniPaciente.value = strDni;
    document.getElementById("dniPaciente").value = strDni;
    document.getElementById("lbl_nomPaciente").innerHTML = strNombre;
    document.getElementById("lbl_dirPaciente").innerHTML = strDireccion;
    $("#buscadorPacModal").modal("hide");
}

function vaciarTblPacientes() {
    //GESTION DE LA TABLA
    var table = document.getElementById("tblPacientes");
    var cantidad = table.rows.length - 1;
    for (var i = 0; i < cantidad; i++) {
        table.deleteRow(-1);
    }
}
function pacienteEncontrado(response) {
    vaciarTblPacientes();
    if (response == "") {
        return;
    }

    var pacientes = JSON.parse(response);
    //GESTION DE LA TABLA
    var table = document.getElementById("tblPacientes");
    for (var pac in pacientes) {
        var table_len = (table.length);
        table.insertRow(table_len).outerHTML = '<TR id="' + pacientes[pac].dni + '"><td><button type="button" name="seleccionar' + pacientes[pac].dni + '" value="' + pacientes[pac].dni + '" onclick="tblPacientesSel(this.id)" id="seleccionar_' + pacientes[pac].dni + '" class="btn fa fa-plus"></button></td><TD>' + pacientes[pac].apellido + "</td><td>" + pacientes[pac].dni + "</td><td>" + pacientes[pac].obra_social_nombre + '</td><td>' + pacientes[pac].direccion + '</td></tr>';
    }
    return;
}


function pacienteError(response) {
    alert('Error al buscar pacientes.' + response);
}

function errorGrabaInforme(response) {

    alert('Error al grabar el informe' + response);
}




function enviarMail() {

    informe.value = informe.value.trim();
    if (opt_profesional.selectedIndex < 0) {
        alert("Debe ingresar el profesional");
        return;
    }

    document.getElementById("print_helper").innerHTML = informe.value;
    //Agrego la hora 8:00 para evitar diferencia horaria GMT
    var fecha = new Date(fecha_estudio.value + " 08:00");
    var sFecha = fecha.getDate() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getFullYear();
    document.getElementById("lblFecha").innerHTML = "Fecha: <b>" + sFecha + "</b>";
    document.getElementById("lblProfesional").innerHTML = "Profesional: <b>" + opt_profesional.options[opt_profesional.selectedIndex].text.trim() + "</b>";
    var usuario = getUsuario();
    //var fecha=fecha_estudio.value;
    var codi_profe = opt_profesional.options[opt_profesional.selectedIndex].value;
    let unInforme = encodeURIComponent(informe.value);
    let firmante = document.getElementById("texto_firmas").innerHTML;
    let mes = "00" + (fecha.getMonth() + 1);
    mes = mes.slice(-2);
    let dia="00" + fecha.getDate();
    dia=dia.slice(-2);
    //alert(mes);
    sFecha = fecha.getFullYear() + "-" + mes + "-" + dia + " 08:00:00";
    getRequest(
            'wsSavePatientHC.php?informe=' + unInforme + "&paciente=" + dniPaciente.value + "&firmante=" + firmante + "&usuario=" + usuario + "&fecha=" + sFecha + "&codi_profe=" + codi_profe, // URL for the PHP file
            envioMail, // handle successful request
            errorGrabaInforme    // handle error
            );
    document.getElementById('header_m').style.visibility = 'visible';
    document.getElementById('header_m').style.display = 'block';
}




function imprimir() {


    if (opt_profesional.selectedIndex < 0) {
        alert("Debe ingresar el profesional");
        return;
    }

    let data= CKEDITOR.instances["editor1"].getData();
    let separador="</p>\n"
    data=data.replaceAll("</p>",separador);
    informe_html.innerHTML = data;
    
    
    editor1.hidden = true;
    document.getElementById("print_helper").innerHTML = informe_html.innerHTML;
    //Agrego la hora 8:00 para evitar diferencia horaria GMT
    var fecha = new Date(fecha_estudio.value + " 08:00");
    console.log("Fecha="+fecha);
    var sFecha = fecha.getDate() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getFullYear();
    document.getElementById("lblFecha").innerHTML = "Fecha: <b>" + sFecha + "</b>";
    document.getElementById("lblProfesional").innerHTML = "Profesional: <b>" + opt_profesional.options[opt_profesional.selectedIndex].text.trim() + "</b>";
    var usuario = encodeURIComponent(getUsuario());
    var codi_profe = encodeURIComponent(opt_profesional.options[opt_profesional.selectedIndex].value);
    
    let unInforme = encodeURIComponent(informe_html.innerText);
    let firmante = encodeURIComponent(document.getElementById("texto_firmas").innerText);
    let mes = "00" + (fecha.getMonth() + 1);
    let dia="00" + fecha.getDate();
    dia=dia.slice(-2);
    mes = mes.slice(-2);
    //alert(mes);
    sFecha = encodeURIComponent(fecha.getFullYear() + "-" + mes + "-" + dia + " 08:00:00");
    getRequest(
            'wsSavePatientHC.php?informe=' + unInforme + "&paciente=" + encodeURIComponent(dniPaciente.value) + "&firmante=" + firmante + "&usuario=" + usuario + "&fecha=" + sFecha + "&codi_profe=" + codi_profe, // URL for the PHP file
            grabaImpresionOK, // handle successful request
            errorGrabaInforme    // handle error
            );
    document.getElementById('header_m').style.visibility = 'visible';
    document.getElementById('header_m').style.display = 'block';
}

$(function () {
    $("#btnClosePopup").click(function () {
        var e = document.getElementById("select_paciente");
        var strDni = e.options[e.selectedIndex].value;
        var strApellido = e.options[e.selectedIndex].text;
        dniPaciente.value = strDni;
        lbl_nomPaciente.innerhtml = strApellido;
        $("#buscadorPacModal").modal("hide");
        ingresoDniPac.click();
    });
});


