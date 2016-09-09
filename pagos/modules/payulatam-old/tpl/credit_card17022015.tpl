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
                Month : {
                    required : true
                },
                Year : {
                    required : true
                  },
                cuotas : {
                  required : true
                }
            },
            messages: {
                        numerot: { 
                            required: "Campo Requerido.",
                            number : "Campo Requerido.",
                            minlength: "Campo Requerido.",
                            maxlength: "Campo Requerido.",
                        },
                        codigot: { 
                            required: "Campo Requerido.",
                            number : "Campo Requerido.",
                            minlength: "Campo Requerido.",
                            maxlength: "Campo Requerido.",
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
                        Month : {
                        	required : "Mes requerido."
                        },
                        Year : {
                        	required : "A&ntilde;o requerido."
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
function cambiaFecha(){
	if(($('#año').val()) != "" && ($('#mes').val()) != ""){
		$('#datepicker').val($('#año').val()+"/"+$('#mes').val());
	}
	else{
		$('#datepicker').val("");
	}
}
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
     <div id="formfiles" class="contend-form">
         <div class="cardAttr"><div class="textCard">Número de Tarjeta de Crédito<span class="purple">*</span>: </div><input type="text" name="numerot" id="numerot" /></div>
         <div class="cardAttr"><div class="textCard">Nombre del Titular<span class="purple">*</span>: </div><input type="text" name="nombre" id="nombre" value="" placeholder="(Tal cual aparece en la tarjeta de Crédito)" /></div>
         <div class="cardAttr"><div class="textCard">Fecha de vencimiento<span class="purple">*</span>: </div>
         <input type="hidden" id="datepicker" name="datepicker" class="date-picker" placeholder="yyyy/mm">
         {html_select_date prefix=NULL end_year="+15" month_format="%m" 
			year_empty="año" year_extra='id="año" onchange="cambiaFecha()"'
			month_empty="mes" month_extra='id="mes" onchange="cambiaFecha()"'
			display_days=false
			field_order="DMY" time=NULL}
         </div>
         <div class="cardAttr">
            <div class="textCard">Número de Seguridad<span class="purple">*</span>: 
                <img src="{$img_dir}mediosp/ayuda.jpg" onmouseover="$('#imagenayuda').show();" onmouseout="$('#imagenayuda').hide();"/>
            </div><input type="password" name="codigot" id="codigot" />
            </div>
         <input type="hidden" name="cuotas" id="cuotas" value="01" />
         {* <div class="cardAttr"><div class="textCard">Número de cuotas<span class="purple">*</span>: </div>
	<select name="cuotas" id="cuotas">
		{for $foo=1 to 24}
    		<option value="{$foo|string_format:'%02d'}">{$foo|string_format:"%02d"}</option>
		{/for}
	</select></div> *}
				<input type="button" onclick="$('#botoncitosubmit').click();" class="paymentSubmit" value="Pagar &raquo;">
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
