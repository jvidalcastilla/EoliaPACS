/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



function getCodUsuario() {
    let s = document.getElementById("cod_usuario").innerText;
    return s;
}

function grabarNuevaPlantilla(){
    let nombrePantilla = document.getElementById("nombreNuevaPlantilla").value;
    let informe="";

    let idPlantilla = 0;
    let codigo = nombrePantilla;
    let usuario = getCodUsuario();



    var dataToSend = {
        id: idPlantilla,
        codigo: codigo,
        texto: informe,
        usuario: usuario
    };

// Realizar la solicitud AJAX
    $.ajax({
        type: "POST",
        url: "./wsSaveAutotexto.php", 
        data: dataToSend,
        success: function (response) {
            // La respuesta del servidor está en la variable 'response'
            console.log("Respuesta del servidor:", response);
            location.reload();
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
        }
    });
    
    
}


function grabarAutotexto(estado) {

    let informe = CKEDITOR.instances["editor1"].getData();
    informe = encodeURIComponent(informe);

    let idPlantilla = document.getElementById("idPlantilla").value;
    let codigo = document.getElementById("codigo").value;
    let usuario = getCodUsuario();



    var dataToSend = {
        id: idPlantilla,
        codigo: codigo,
        texto: informe,
        usuario: usuario
    };

// Realizar la solicitud AJAX
    $.ajax({
        type: "POST",
        url: "./wsSaveAutotexto.php", 
        data: dataToSend,
        success: function (response) {
            // La respuesta del servidor está en la variable 'response'
            console.log("Respuesta del servidor:", response);
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
        }
    });


}

  CKEDITOR.replace('editor1', {
                height: 300,
                width: '100%',
                removeButtons: ''
            });

            let v = document.getElementById('editor1');
            let vinforme = document.getElementById('informe_html');
            v.value = vinforme.innerHTML;

