var paciente;   
var estudios;
var estudios_orig;
    
function popup(urlToOpen) {
  var popup_window=window.open(urlToOpen,"myWindow","toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=yes, width=400, height=400");            
  try {
    popup_window.focus();   
  }
  catch (e) {
    alert("Pop-up Blocker is enabled! Please add this site to your exception list.");
  }
}    
    
function emitirInforme(a,b)    {
    
    alert(a);
    alert (b);
}
    
function sortTable(n) {
  var table, rows, switching, i, x, y, xVal, yVal, shouldSwitch, dir, dia,mes,anio, switchcount = 0;
  table = document.getElementById("estudios");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc";
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n]; 
      
      xVal=x.innerHTML.toLowerCase();
      yVal=y.innerHTML.toLowerCase();
      
      //Columna nombre de paciente
      if (n==1){
        xVal=rows[i].getElementsByTagName("TD")[n].textContent;
        yVal=rows[i+1].getElementsByTagName("TD")[n].textContent;
        
      }
      
      //Columna Fecha
      if (n==2){
        xVal=rows[i].getElementsByTagName("TD")[n].textContent;
        dia=parseInt(xVal.substring(0,2));
        mes=parseInt(xVal.substring(3,5)-1);
        anio=parseInt(xVal.substring(6,10));
        
        //alert (anio + " " + mes + " " + dia);
        xVal=new Date(anio,mes, dia,0,0,0) ;
        
        yVal=rows[i+1].getElementsByTagName("TD")[n].textContent;
        dia=parseInt(yVal.substring(0,2));
        mes=parseInt(yVal.substring(3,5)-1);
        anio=parseInt(yVal.substring(6,10));
        yVal=new Date(anio,mes, dia,0,0,0) ;
      }
      
      
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (xVal > yVal) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (xVal < yVal) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  
  }
}
        
        
        
    function yesnoCheck(that) {
        
        if (that.value == "rango") {
              document.getElementById("rango_desde").valueAsDate=new Date();
              document.getElementById("rango_hasta").valueAsDate=new Date();
              document.getElementById("rango_desde").style.display = "block";
              document.getElementById("rango_hasta").style.display = "block";
              document.getElementById("lbl_hasta").style.display = "block";
              document.getElementById("lbl_desde").style.display = "block";
        } else {
            document.getElementById("rango_desde").style.display = "none";
            document.getElementById("rango_hasta").style.display = "none";
            document.getElementById("lbl_hasta").style.display = "none";
            document.getElementById("lbl_desde").style.display = "none";
        }
    }
    
   
 
 
    
    function sendEmail(study,idPac){
         //alert ("send email:"+ study + "  " + id);
         //Obtener el id del paciente y del estudio  
        
        var container = document.getElementById('attached-files');
        container.innerHtml="<p>...Generando imagenes...</P>";
         
        container = document.getElementById('recipient-name');
        container.value="";
        
        container = document.getElementById('recipient-address');
        container.value="";
        
                 
        var btnEnviar = document.getElementById('btnEnviarMail');
        btnEnviar.innerHTML="Cargando...";
        btnEnviar.disabled=true;
         
         getRequest(
                        './servicios/wsPacientePorDni.php?id_paciente='+idPac ,
                        cargaPaciente, // handle successful request
                        errorPaciente    // handle error
                        );
         
        getRequest(
                        './servicios/wsgetPACSFiles.php?study='+study ,
                        cargarEstudios , // handle successful request
                        errorPaciente    // handle error
                        );
          
        $('#sendEmailPopup').modal('show');   
          
      }
    
    function cargarEstudios(response){
        estudios_orig=response;
        //alert (response);
        estudios=JSON.parse(response);
       //  alert (estudios);
         var container = document.getElementById('attached-files');
         var s="<p>";
         for (var i = 0, max = estudios.length; i < max; i++) {
             if (estudios[i]!=""){
             s=s + estudios[i] + "</br> ";
             
         }
        }
        s=s+"</p>";
        container.innerHTML=s;
       
        btnEnviar = document.getElementById('btnEnviarMail');
        btnEnviar.innerHTML="Enviar";
        btnEnviar.disabled=false;
         
    }
    
 
    function cargaPaciente(response)  {
        paciente=JSON.parse(response);
        var container = document.getElementById('recipient-name');
        if (paciente.apellido!=null){
            container.innerHTML="<email_dest>"+ paciente.apellido +"</email_dest>";
        }else{
            container.innerHTML="<email_dest>Paciente no hallado en RP.</email_dest>";
        }
        
        var container = document.getElementById('recipient-address');
        if (paciente.mail!=null){
            container.value=paciente.mail;
        } else {
            container.value="";
        }
        
        
        var container = document.getElementById('attached-files');
        container.innerHTML="";
        //alert (response);
        
        
        
    }
    
    function errorPaciente(error)  {
        alert (error);
    }
      
      
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
      
      function enviarEmail(){
           
        var container = document.getElementById('recipient-name');
        container.innerHTML="<email_dest>ENVIANDO CORREO</email_dest>";
        
        var emailContainer = document.getElementById('recipient-address');
        var mailto= emailContainer.value;
        
        btnEnviar = document.getElementById('btnEnviarMail');
        btnEnviar.innerHTML="Enviando...";
        btnEnviar.disabled=true;
        
        getRequest(
            './servicios/wsSendMailFromPACS.php?mailto='+mailto +"&paciente=" +paciente.apellido +  "&files="+ estudios_orig,
            mailOK, // handle successful request
            errorPaciente    // handle error
            );

           
      }
      
    function mailOK(response){
         $('#sendEmailPopup').modal('hide');  
//         alert ("Correo enviado con exito.");
      }
    