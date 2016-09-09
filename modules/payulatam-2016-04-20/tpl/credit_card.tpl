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

 {*############  OpenPay ###########*}
 {if $pasarela_de_pago === 'openpay'}
 {literal} 
    <script type="text/javascript" src="//openpay.s3.amazonaws.com/openpay.v1.min.js"></script>
    <script type='text/javascript' src="//openpay.s3.amazonaws.com/openpay-data.v1.min.js"></script>
{/literal}
 {/if}   


{literal}
<script  type="text/javascript">
// deshabilitar tecla F5
function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };
$(document).on("keydown", disableF5);

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

<script  type="text/javascript">
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


 {if $pasarela_de_pago === 'openpay'}
// inicio validación formulario
        $('#formPayU').validate({
{literal}
                  wrapper: 'div',
            errorPlacement: function (error, element) {
                error.addClass("arrow")
                error.insertAfter(element);
            },
{/literal}            
            rules :{                
                card : {
                    required : true,
                    number : true,   //para validar campo solo numeros
                    minlength : 14 , //para validar campo con minimo 3 caracteres
                    maxlength : 16 //para validar campo con maximo 9 caracteres                                   
                },                
                cvv : {
                    required : true,
                    number : true,   //para validar campo solo numeros
                    minlength : 3 , //para validar campo con minimo 3 caracteres
                    maxlength : 4 //para validar campo con maximo 9 caracteres                                   
                },
                /*
                mediop : {
                  required : true
                },*/
                holder : {
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
                        card: { 
                            required: "Campo Requerido.",
                            number : "Campo Requerido.",
                            minlength: "Campo Requerido.",
                            maxlength: "Campo Requerido.",
                        },
                        cvv: { 
                            required: "Campo Requerido.",
                            number : "Campo Requerido.",
                            minlength: "Campo Requerido.",
                            maxlength: "Campo Requerido.",
                        },/*
                        mediop : {
                            required : "El campo es requerido."
                        },
                        */
                        holder : {
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
// fin validación formulario

 /*
   Tokenización OpenPay
 */
        OpenPay.setId('{$openpay_key['id']}');
        OpenPay.setApiKey('{$openpay_key['public_key']}');
        {if $openpay_key['mode'] === 'yes'}
          OpenPay.setSandboxMode(true);
        {else}
          OpenPay.setSandboxMode(false);
        {/if}

        {literal}
        //Se genera el id de dispositivo
        var deviceSessionId = OpenPay.deviceData.setup("formPayU", "openpay_device_session_id");

        $('#submit_btn').on('click', function(event) {
            var validator = $( "#formPayU" ).validate();
              if(!validator.form()){
                return false;
              }
         
            event.preventDefault();
            //$("#submit_btn").prop( "disabled", true);
            OpenPay.token.extractFormAndCreate('formPayU', success_callbak, error_callbak);                
        });

    var success_callbak = function(response) {
        var token_id = response.data.id;
        $('#token_id').val(token_id);
        $("#submit_btn").attr("disabled", true); 
        console.log('token_id '+response.data.id);
        $("#formPayU").submit();
   
    };

    var error_callbak = function(response) {
    var desc = response.data.description != undefined ? 
        response.data.description : response.message;
        $("#submit_btn").attr("disabled", false); 
        console.log("ERROR [" + response.status + "] " + desc);
        //alert("ERROR [" + response.status + "] " + desc);
    //   $("#submit_btn").prop("disabled", false);

};
/*
    Fin Tokenización OpenPay
*/
{/literal}
{/if}

{if $pasarela_de_pago === 'payulatam'}
// inicio validación formulario
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
                /*
                mediop : {
                  required : true
                },*/
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
                        },/*
                        mediop : {
                            required : "El campo es requerido."
                        },
                        */
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

// fin validación formulario
{/if}

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
 {*############  OpenPay ###########*}
 {if $pasarela_de_pago === 'openpay'}
    <form  method="POST" action="./modules/payulatam/credit_card.php" id="formPayU" autocomplete="off" >
      <div>
        <div id="formfiles" class="contend-form">
          <div class="ctn-vlr-total-pedido">
            El valor total de tu pedido es de <strong class="ctn-vlr-total-pedido-semibold">{displayPrice price=$total_price} (Impuestos incl.)</strong>
          </div>
          <div class="cardAttr">
            {* <div class="textCard">Número de Tarjeta de Crédito o Débito<span class="purple">*</span>:</div> *}
            <input type="text" name="card" autocomplete="off" data-openpay-card="card_number" id="numerot" placeholder="Número de Tarjeta de Crédito o Débito *"/>
          </div>
          <div class="cardAttr">{* <div class="textCard">Nombre del Titular<span class="purple">*</span>: </div> *}
            <input type="text" name="holder" id="nombre" autocomplete="off" data-openpay-card="holder_name" placeholder="Nombre del Titular" />
          </div>
          <div class="cardAttr">
            <div class="textCard">Fecha de vencimiento<span class="purple">*</span>: 
            </div>
            <input type="hidden" id="datepicker" name="datepicker" class="date-picker" placeholder="yy/mm">
            <div class="cont_select">
              {html_select_date prefix=NULL end_year="+15" month_format="%m" year_empty="año" year_extra='id="año" class="select-fecha-tarjetas" onchange="cambiaFecha()"' month_empty="mes" month_extra='id="mes" class="select-fecha-tarjetas" onchange="cambiaFecha()" data-openpay-card="expiration_month"' display_days=false  display_years=false field_order="DMY" time=NULL} {$year_select}
            </div>
          </div>
          <div class="cardAttr">{* <div class="textCard">Número de verificación<span class="purple">*</span>: </div> *}
            <input type="password" name="cvv" autocomplete="off" data-openpay-card="cvv2" id="codigot" placeholder="Número de verificación"/>
          </div>
          <br/>
          <div class="cont-trust-img">
            <img class="trust_img" src="{$img_dir}authentication/seguridad.jpg" />
          </div>
          <input type="hidden" name="cuotas" id="cuotas" value="01" />
          {*} <img src="https://test.farmalisto.com.mx/themes/gomarket/img/mediosp/openpay.png"> {*}
          <div class="cont-trust-img">
            <input type="button" id="submit_btn" onclick="$('#botoncitosubmit').click();" class="paymentSubmit boton-pagos-excep" value="PAGAR">
          </div>
        </div>
      </div>       
      <input type="hidden" name="token_id" id="token_id">
      <input type="hidden" id="openpay_device_session_id" name="openpay_device_session_id">
    </form>
    {*############  PayuLatm ###########*}
    {elseif $pasarela_de_pago === 'payulatam'}
    <form  method="POST" action="./modules/payulatam/credit_card.php" id="formPayU" autocomplete="off" >
      <div>
        <div id="formfiles" class="contend-form">
          <div class="cardAttr">
            <div class="textCard">Número de Tarjeta de Crédito<span class="purple">*</span>: 
            </div>
            <input type="text" name="numerot" id="numerot" />
          </div>
          <div class="cardAttr">
            <div class="textCard">Nombre del Titular<span class="purple">*</span>: 
            </div>
            <input type="text" name="nombre" id="nombre" value="" placeholder="(Tal cual aparece en la tarjeta de Crédito)" />
          </div>
          <div class="cardAttr">
            <div class="textCard">Fecha de vencimiento<span class="purple">*</span>: </div>
              <input type="hidden" id="datepicker" name="datepicker" class="date-picker" placeholder="yyyy/mm">   
                {html_select_date prefix=NULL end_year="+15" month_format="%m" year_empty="año" year_extra='id="año" class="select-fecha-tarjetas"  onchange="cambiaFecha()"' month_empty="mes" month_extra='id="mes" class="select-fecha-tarjetas" onchange="cambiaFecha()"' display_days=false field_order="DMY" time=NULL}
          </div>
          <div class="cardAttr">
            <div class="textCard">Número de verificación<span class="purple">*</span>: 
            </div>
            <input type="password" name="codigot" id="codigot" />
          </div>
          <input type="hidden" name="cuotas" id="cuotas" value="01" />
				  <br/>
          <img class="trust_img" src="{$img_dir}authentication/seguridad.jpg" />
				  <input type="button" id="submit_btn" onclick="$('#botoncitosubmit').click();" class="paymentSubmit boton-pagos-excep" value="Pagar &raquo;">
        </div>
      </div>       
      <input type="hidden" value="{$deviceSessionId}"  name="deviceSessionId" />
      <input type="hidden" id="openpay_device_session_id" name="openpay_device_session_id">
    </form>
  {/if}
</div>
{/if}
