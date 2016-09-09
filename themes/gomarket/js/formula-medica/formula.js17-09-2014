 $(function(){
     
   
 
   if($("#opcion1").is(':checked'))
   {
 $('#div1').show(); //muestro mediante id

   }
   
   if($("#opcion2").is(':checked'))
   {
      $('#div2').show(); //muestro mediante id   

   }
   
   if($("#opcion3").is(':checked'))
   {
       $('#div3').show(); //muestro mediante id
 
   }
   
   if($("#opcion4").is(':checked'))
   {
      $('#div4').show(); //muestro mediante id
   }
  
   if($("#eps").is(':checked'))
   {
              $('#labeps').show(); //muestro mediante id
              $('#listeps').show(); //muestro mediante id  
   }
   
   if($("#particular").is(':checked'))
   {
        $('#labeps').hide(); //muestro mediante id
        $('#listeps').hide(); //muestro mediante id
    }
   
 });  
 
 
 // Mostrar y ocultar combo EPS
   $(function()
        {
            
            $("#particular").click(function(){
            $('#labeps').hide(); //muestro mediante id
             $('#listeps').hide(); //muestro mediante id
            
         });
         
            $("#eps").click(function(){
            $('#labeps').show(); //muestro mediante id
             $('#listeps').show(); //muestro mediante id
            
         });

        
        });
        
       function showdiv(obj){        
            hidedivs();
            $('#'+obj).show(); //muestro mediante id  
               }
        
        function  hidedivs()
        {
           $('#div1').hide(); //muestro mediante id  
            $('#div2').hide(); //muestro mediante id  
             $('#div3').hide(); //muestro mediante id  
              $('#div4').hide(); //muestro mediante id  
        }
        
// funci\u00f3n para el control del componente de fecha
$(function() {
    $( "#datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  });


// Traducci\u00f3n al espa�ol componente de fecha
$(function($){
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi 	\u00e9rcoles', 'Jueves', 'Viernes', 'S�bado'],
        dayNamesShort: ['Dom','Lun','Mar','Mi 	\u00e9','Juv','Vie','S�b'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S�'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
});

 function validar_texto(e){
    tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
    if ((tecla==8)||(tecla==0)){
        return true;
    }
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
    } 
    
    
    function acceptCGV()
{
  
  //se validan los capos del la opcion digitar formula medica       
  if($("#opcion1").is(':checked')) {  
               
  //obteniendo el valor que se puso en el campo text del formulario
 var nombremedico = document.getElementById("nombremedico").value;
  //la condici\u00f3n
 if (nombremedico.length == 0 || /^\s+$/.test(nombremedico)) {
   alert('El campo de (Nombre de su M\u00e9dico) esta vacio!');
   document.getElementById("nombremedico").focus();
   return false;
 }
 
 if(nombremedico.length >= 4){
}else{
alert("El campo de (Nombre de su M\u00e9dico) debe ser de m\u00ednimo 4 caracteres");
document.getElementById("nombremedico").focus();
return false;
}

if(nombremedico.length <= 50){
}else{
alert("El campo de (Nombre de su M\u00e9dico) debe ser de maximo 50 caracteres");
document.getElementById("nombremedico").focus();
return false;
}

 // tarjeta profesional
 
 var tarjeta = document.getElementById("tarjeta").value;
if (tarjeta.length == 0 || /^\s+$/.test(tarjeta)) {
   alert('El campo de (Tarjeta profesional) esta vacio!');
   document.getElementById("tarjeta").focus();
   return false;
}

if(tarjeta.length >= 5){
}else{
alert("El campo de (Tarjeta profesional) debe ser de m\u00ednimo 4 caracteres");
document.getElementById("tarjeta").focus();
return false;
}

if(tarjeta.length <= 8){
}else{
alert("El campo de (Tarjeta profesional) debe ser de maximo 8 caracteres");
document.getElementById("tarjeta").focus();
return false;
}

 //dosis
 
var dosis = document.getElementById("dosis").value;
if (dosis.length == 0 || /^\s+$/.test(dosis)) {
    alert('El campo de (Posologia) esta vacio!');
   return false;
 }
 
 if(dosis.length >= 10){
}else{
alert("El campo de (Posologia) debe ser de m\u00ednimo 10 caracteres");
document.getElementById("dosis").focus();
return false;
}

if(dosis.length <= 254){
}else{
alert("El campo de (Posologia) debe ser de maximo 254 caracteres");
document.getElementById("dosis").focus();
return false;
}
 
 //#datepicker
 var datepicker = document.getElementById("datepicker").value;
if (datepicker.length == 0 || /^\s+$/.test(datepicker)) {
    document.getElementById("datepicker").focus();
   alert('El campo de (Fecha de prescripci\u00f3n) esta vacio!');
   return false;
}
 
  var ExpReg = /^([0][1-9]|[12][0-9]|3[01])(\/|-)([0][1-9]|[1][0-2])\2(\d{4})$/;
     
        if(!(document.getElementById("datepicker").value.match(ExpReg)))
        {
          alert("formato de fecha no valido");
           document.getElementById("datepicker").focus();
            return false;
        }
       
       
       
 // valida si esta seleccionada EPS
 if($("#eps").is(':checked')) { 
      
       //Validando el combo select
var listeps = document.getElementById("listeps").value;
 if(listeps == ""){
    alert('Debe Elegir una EPS!');
    document.getElementById("listeps").focus();
    return false;
 }
  
 } 
 } // fin opcion_0 
 
 //se validan los capos del la opcion digitar formula medica       
  if($("#opcion3").is(':checked')) 
  {
      
 // Validaci\u00f3n numero de tel 	\u00e9fono
  var telefono = document.getElementById("telefono").value;
if (telefono.length == 0 || /^\s+$/.test(telefono)) {
     //$( ".validacion" ).append( "<p>Hola esto es una preuba</p>" );
      document.getElementById('telefono').style.backgroundColor='yellow';
      alert('El campo de (Tel\u00e9fono) esta vacio!');
  
   document.getElementById("telefono").focus();
   return false;
}

  var ExpReg = /^[0-9]{1,2}-? ?[0-9]{6,8}$/;
     
        if(!(document.getElementById("telefono").value.match(ExpReg)))
        {
          alert("Numero de tel\u00e9fono no valido");
           document.getElementById("telefono").focus();
            return false;
        }  
  }
  
   //se validan los capos del la opcion digitar formula medica       
  if($("#opcion4").is(':checked')) 
  {
      //archivoformula
  var archivoformula = document.getElementById("archivoformula").value;
if (archivoformula.length == 0 || /^\s+$/.test(archivoformula)) {
    alert('El campo de (Archivo formula m\u00e9dica) esta vacio!');
    document.getElementById("archivoformula").focus();
   return false;
 }
 
 
extArray = new Array(".png", ".jpg", ".pdf",".tiff",".xcf",".gif",".pcx",".wmp",".raw",".jp2",".bmp",".dng");

allowSubmit = false;
if (!archivoformula) return;
while (archivoformula.indexOf("\\") != -1)
archivoformula = archivoformula.slice(archivoformula.indexOf("\\") + 1);
ext = archivoformula.slice(archivoformula.indexOf(".")).toLowerCase();
for (var i = 0; i < extArray.length; i++) {
if (extArray[i] == ext) { allowSubmit = true; break; }
}
if (allowSubmit) form.submit();
else
alert("Se permiten �nicamente archivos con la extenci\u00f3n: " 
+ (extArray.join("  ")) + "\nPor favor, seleccione otro archivo "
+ "e intente de nuevo.");
return false;
      
  }
 
    

    
        
        
                // Aceptar contrato     
	///	if ($('#cgv').length && !$('input#cgv:checked').length)
	//	{
	//		alert(msg);
	//		return false;
	//	}
	//	else
	//		return true;
	}