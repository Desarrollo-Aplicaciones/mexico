{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($error)}
<p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else}
    
{literal}
 



<script>
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
</script>
<script>
  function pulsar(e) {
  tecla = (document.all) ? e.keyCode :e.which;
  return (tecla!=13);
  }
  
  
</script>

{/literal}
 
<style type="text/css">
      



select,input { 

	border-top:1px solid #acbd5a;
	border-left:1px solid #acbd5a;
	border-bottom:1px solid #6e7f3d;
	border-right:1px solid #6e7f3d;
}


input#submit{
	width:115px;
	height:25px;

	padding:2px 0px 3px 24px;
	color:#fff;
	text-align:left;
	margin-left:237px;
	border:none;
	
	}
	input#submit:focus{
	
	}

        
.contend-form{
 display: inline-block;
  width: 97%;
  
        
        }

label.error {
    border-top:1px solid #99182c;
    border-bottom:1px solid #99182c;
    border-right:1px solid #99182c;    
    color:black;
    padding:1px 2px 1px 2px;
    font-size:100%;

}   
  .ui-datepicker-calendar {
    display: none;
    }      
 </style>
 


<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
<script type="text/javascript">
    
 var ruta = "{$modules_dir}payulatam/ajax_bines.php"; 
 var divbeneficio = '<div id="div_beneficio" class="div_beneficio" > <div class="div_img_beneficio" > <img id="img_beneficio" class="img_beneficio"  > </div> <div id="txt_beneficio" class="div_txt_beneficio"></div></div>';
  
    $(function(){

{literal}
$( "#numerot" ).change(function() {

    // enviar solicitutd              
   $.ajax(ruta, {
   "type": "post", // usualmente post o get
   "success": function(result) {
      
      if(result!='0')
      {          
   
  if ($('#div_beneficio').length) {
 
$( "#div_beneficio" ).remove();
 
}
          
          var Array = result.split('|');
                 $('#formfiles').append(divbeneficio);
                 $("#img_beneficio").attr('src', '{/literal}{$img_dir}{literal}mediosp/bancos/'+Array[1]);
                 $('#txt_beneficio').append('<b>Descuento del '+Array[0]+'%.</b>');
      }else{
          
   if ($('#div_beneficio').length) {
 
$( "#div_beneficio" ).remove();
 
}
          
      }
   },
   "error": function(result) {
    console.log("Error ajaxbines -> "+result);
   },
   "data": {numerot: $( "#numerot" ).val(), accion: "ajax_bin"},
   "async": true
});    
    

});

{/literal}

    $('.date-picker').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: false,
        dateFormat: 'yy/mm',
        /*yearRange: (new Date).getFullYear()+'2018'*/
        minDate:'m',
        maxDate:'+12Y',
        onChangeMonthYear: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
             
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
    

{*
        $("input[name='mediop']").change(function() {

{literal}
            var settings = $("#formPayU").validate().settings;

            //walert(settings);
{/literal}
            if ($("input[name='mediop']:checked").val() === "VISA" || $("input[name='mediop']:checked").val() === "MASTERCARD") {
                // Modify validation settings
                $.extend(settings, {
                    rules: {
                        numerot: {
                            required : true,
                            number : true,
                            minlength: 16,
                            maxlength: 16
                        },
                        codigot: {
                            required : true,
                            number : true,
                            minlength: 3,
                            maxlength: 3
                        },
                        mediop : {
                          required : true
                        },
                        nombre : {
                          required : true
                        },
                        datepicker : {
                          required : true
                        },
                        cuotas : {
                          required : true
                        }
                    },
                    
                    messages: {
                        numerot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de tarjeta v&aacute;lido.",
                            minlength: "Por favor ingrese m&iacute;nimo 16 n&uacute;meros.",
                            maxlength: "Por favor ingrese m&aacute;ximo 16 n&uacute;meros.",
                        },
                        codigot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de verificaci&oacute;n v&aacute;lido.",
                            minlength: "Por favor ingrese m&iacute;nimo 3 n&uacute;meros.",
                            maxlength: "Por favor ingrese m&aacute;ximo 3 n&uacute;meros.",
                        },
                        mediop : {
                            required : "El campo es requerido."
                        },
                        nombre : {
                            required : "El campo es requerido."
                        },
                        datepicker : {
                            required : "El campo es requerido."
                        },
                        cuotas : {
                            required : "El campo es requerido."
                        }
                    },
                });
        

            } else if ($("input[name='mediop']:checked").val() === "DINERS" ) {    
                
                $.extend(settings, {
                    rules: {
                        numerot: {
                            required : true,
                            number : true,
                            minlength: 14,
                            maxlength: 14
                        },
                        codigot: {
                            required : true,
                            number : true,
                            minlength: 3,
                            maxlength: 3
                        },
                        mediop : {
                          required : true
                        },
                        nombre : {
                          required : true
                        },
                        datepicker : {
                          required : true
                        },
                        cuotas : {
                          required : true
                        }
                    },

                    messages: {
                        numerot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de tarjeta v&aacute;lido.",
                            minlength: "Por favor ingrese m&iacute;nimo 14 n&uacute;meros.",
                            maxlength: "Por favor ingrese m&aacute;ximo 14 n&uacute;meros.",
                        },
                        codigot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de verificaci&oacute;n v&aacute;lido.",
                            minlength: "Por favor ingrese m&iacute;nimo 3 n&uacute;meros.",
                            maxlength: "Por favor ingrese m&aacute;ximo 3 n&uacute;meros.",
                        },
                        mediop : {
                            required : "El campo es requerido."
                        },
                        nombre : {
                            required : "El campo es requerido."
                        },
                        datepicker : {
                            required : "El campo es requerido."
                        },
                        cuotas : {
                            required : "El campo es requerido."
                        }
                    },
                });        


            } else if ($("input[name='mediop']:checked").val() === "AMEX" ) {    
                
                $.extend(settings, {
                    rules: {
                        numerot: {
                            required : true,
                            number : true,
                            minlength: 15,
                            maxlength: 15
                        },
                        codigot: {
                            required : true,
                            number : true,
                            minlength: 4,
                            maxlength: 4
                        },
                        mediop : {
                          required : true
                        },
                        nombre : {
                          required : true
                        },
                        datepicker : {
                          required : true
                        },
                        cuotas : {
                          required : true
                        }
                    },

                    messages: {
                        numerot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de tarjeta v&aacute;lido.",
                            minlength: "Por favor ingrese m&iacute;nimo 15 n&uacute;meros.",
                            maxlength: "Por favor ingrese m&aacute;ximo 15 n&uacute;meros.",
                        },
                        codigot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de verificaci&oacute;n v&aacute;lido.",
                            minlength: "Por favor ingrese m&iacute;nimo 4 n&uacute;meros.",
                            maxlength: "Por favor ingrese m&aacute;ximo 4 n&uacute;meros.",
                        },
                        mediop : {
                            required : "El campo es requerido."
                        },
                        nombre : {
                            required : "El campo es requerido."
                        },
                        datepicker : {
                            required : "El campo es requerido."
                        },
                        cuotas : {
                            required : "El campo es requerido."
                        }
                    },
                });
            }


            // force a test of the form
            $("#formPayU").valid();
        });
        *}



        $('#formPayU').validate({
{literal}
                  wrapper: 'div',
            errorPlacement: function (error, element) {
                error.css({'padding-left':'10px','margin-right':'20px','padding-bottom':'2px'});
                error.addClass("arrow")
                error.insertAfter(element);
            },
{/literal}            
            rules :{                
                numerot : {
                    required : true,
                    number : true,   //para validar campo solo numeros
                    minlength : 14 , //para validar campo con minimo 3 caracteres
                    maxlength : 16 //para validar campo con maximo 9 caracteres                                   
                },                
                codigot : {
                    required : true,
                    number : true,   //para validar campo solo numeros
                    minlength : 3 , //para validar campo con minimo 3 caracteres
                    maxlength : 4 //para validar campo con maximo 9 caracteres                                   
                }, {*
                mediop : {
                  required : true
                },*}
                nombre : {
                  required : true
                },
                datepicker : {
                  required : true
                },
                cuotas : {
                  required : true
                }
            },
            messages: {
                        numerot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de tarjeta v&aacute;lido.",
                            minlength: "Por favor ingrese un m&iacute;nimo 14 n&uacute;meros.",
                            maxlength: "Por favor ingrese un m&aacute;ximo 16 n&uacute;meros.",
                        },
                        codigot: { 
                            required: "El campo es requerido.",
                            number : "Por favor ingrese un n&uacute;mero de verificaci&oacute;n v&aacute;lido.",
                            minlength: "Por favor ingrese un m&iacute;nimo 3 n&uacute;meros.",
                            maxlength: "Por favor ingrese un m&aacute;ximo 4 n&uacute;meros.",
                        }, {*
                        mediop : {
                            required : "El campo es requerido."
                        }, *}
                        nombre : {
                            required : "El campo es requerido."
                        },
                        datepicker : {
                            required : "El campo es requerido."
                        },
                        cuotas : {
                            required : "El campo es requerido."
                        }
                    },
        });    
    });

$(function($){
    $.datepicker.regional['es'] = {
        closeText: 'Ok',
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi    \u00e9rcoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mi   \u00e9','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
});
</script>

<style type="text/css">
.ui-datepicker-calendar {
    display: none;
  
    }
    
  .div_beneficio{
     display: inline-block; width: 100%;
  }
  
  .div_img_beneficio{
     min-width: 49%;
     max-width: 100%;
     width: 49%;
     text-align: left;
     float: left;
     padding: 3px;
  }
  
  .div_txt_beneficio{
     float: left;  
     min-width: 49%;
     max-width: 100%; 
     margin: 28px 5px 0 0;
     color: #009207;
  }
  .img_beneficio{

  height: 80px;
  }
</style>
  
 <div class="pagocont">
    
     
     
     <form  method="POST" action="./modules/payulatam/credit_card.php" id="formPayU" autocomplete="off" >
         
         
         <div>
             {*
     <div id="credircard" class="contend-form" >
         <div style="float: left; width: 55px; margin: 0 15px; color: #039701;"> <input type="radio" name="mediop" value="VISA" id="VISA" checked="checked"/><label style="float: right; margin: 0 5px 0 auto; width: auto;">Visa</label></div>
         <div style="float: left; width: 69px; margin: 0 15px;">                 <input type="radio" name="mediop" value="AMEX" id="AMEX"/><label  style="float: right; margin: 0 5px 0 10px; width: auto;">Amex</label></div>
         <div style="float: left; width: 110px; margin: 0 15px;">                <input type="radio" name="mediop" value="MASTERCARD" id="MASTERCARD" /><label style="float: right; margin: 0 5px 0 10px; width: auto;">MasterCard</label></div>
         <div style="float: left; width: 74px; margin: 0 15px;">                 <input type="radio" name="mediop" value="DINERS" id="DINERS" /><label style="float: right; margin: 0 5px 0 10px; width: auto;">Diners</label> </div>
     </div>
             *}
     <div id="formfiles" class="contend-form" style="float: none;">
         <div style="display: inline-block; width: 100%;" ><div style=" min-width: 49%; max-width: 100%; text-align: left;  float: left; padding: 3px;">Número de Tarjeta de Crédito*</div><div  style=" float: left;  min-width: 49%; max-width: 100%; max-width: 90%; "><input style="width: 90%;" type="text" name="numerot" id="numerot" /></div> </div>
         <div style="display: inline-block; width: 100%;"> <div style=" min-width: 49%; max-width: 100%; text-align: left;  float: left; padding: 3px;">Nombre del Titular*</div><div style=" float: left;   min-width: 49%; max-width: 100%; "><input style="width: 90%;" type="text" name="nombre" id="nombre" value="" placeholder="(Tal cual aparece en la tarjeta de Crédito)" /> </div> </div>
         <div style="display: inline-block; width: 100%;"> <div style=" min-width: 49%; max-width: 100%; text-align: left;  float: left; padding: 3px;">Fecha de vencimiento*</div><div style=" float: left;   min-width: 49%; max-width: 100%; " > <input style="width: 90%;" type="text" id="datepicker" name="datepicker" class="date-picker" placeholder="yyyy/mm"> </div> </div>
         <div style="display: inline-block; width: 100%;"> <div style=" min-width: 49%; max-width: 100%;  width: 49%; text-align: left;  float: left; padding: 3px;">Número de verificación*</div><div style=" float: left;   min-width: 49%; max-width: 100%; " ><input style="width: 90%;" type="password" name="codigot" id="codigot" /></div> </div>
         <div style="display: inline-block; width: 100%;"> <div style=" min-width: 49%; max-width: 100%;  width: 49%; text-align: left;  float: left; padding: 3px;">Número de cuotas*</div><div style=" float: left;   min-width: 49%; max-width: 100%; " >
                 <select name="cuotas" id="cuotas" style="width: 100%;">
      <option value="01" >01</option>    
      <option value="02">02</option>   
      <option value="03">03</option>   
      <option value="04">04</option>
      <option value="05">05</option>   
      <option value="06">06</option>
      <option value="07">07</option>  
      <option value="08" >08</option>    
      <option value="09">09</option>   
      <option value="10">10</option>   
      <option value="11">11</option>
      <option value="12">12</option>   
      <option value="13">13</option>
      <option value="14">14</option> 
      </select></div> </div>
     </div>
       <!--  <div style="margin:0 0 0 1%; ">
        <input type="submit" id="payuSubmit" value=" " style="
padding: 0 0; 
margin: 7px 7px 7px 7px;
width:145px;
height:40px;
border:none;
background:url({$img_dir}pagar-normal.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;" />
         </div>-->
     </div>       
<input type="hidden" value="{$deviceSessionId}"  name="deviceSessionId" />

</form>
</div>


{/if}

<style type="text/css">
 .ui-datepicker-year {
    float: left !important;
  }
  
  .ui-datepicker-month {
    float: right !important;
  }
</style>  
