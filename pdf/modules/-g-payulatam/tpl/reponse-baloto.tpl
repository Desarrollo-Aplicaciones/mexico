
{if isset($fecha)}
    
{literal}
<style type="text/css">
  body,td,th { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #666; }


a:visited { color:#1c3a85; }
a:hover { color: #1c3a85; text-decoration:none; }
a:active { color: #00C; }
a { text-decoration:none; }
h1 { color:#1c3a85; font-family:Arial, Helvetica, sans-serif; font-size:30px; font-weight:lighter; }
p {font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #666; }



/* ok*/
.balotoTop 		{ width:691px; height: 20px; margin-top:10px;}
.balotoForm 	{  text-align:center; height:550px; width:631px; padding-left:30px; padding-right: 30px; padding-top:10px;    border-radius: 10px;
  border: 3px solid #E7E7E7; }
.label_bold { font-weight: bolder; display: inline; }
.z-label{}
z-separator-ver{}
.Rojo12Pt { color:#FF0000; font-size:12px; font-weight:bold; }
.btn-blue.z-button-os-disd { color: #666666;}
.blackLink { color:#000000; font-size:12px; font-weight:bold; text-decoration:underline; }  
    
</style>

<script type="text/javascript">
    
function imprimir(){
  var objeto=document.getElementById('zk_comp_48');  //obtenemos el objeto a imprimir
  var ventana=window.open('','_blank');  //abrimos una ventana vacía nueva
  ventana.document.write(objeto.innerHTML);  //imprimimos el HTML del objeto en la nueva ventana
  ventana.document.close();  //cerramos el documento
  ventana.print();  //imprimimos la ventana
  ventana.close();  //cerramos la ventana
}

</script>

{/literal}

<div align="center" id="zk_comp_48">
<div class="balotoTop"></div>
<div class="balotoForm">
<div align="left"><span class="label_bold z-label" id="zk_comp_50">Apreciado Usuario:</span></div>
<p></p>
<div align="justify"><span class="z-label" id="zk_comp_55">Ahora puedes realizar tus pagos en efectivo a nivel nacional a través de los puntos Vía Baloto. Para realizar el pago debes acercarte a un punto de pago Vía Baloto, e indicarle al operador los datos relacionados a continuación (Código de convenio y Número de pago):</span></div>
                        

  <div style="  border-radius: 10px;
  border: 3px solid #F00; margin:  10px 10px 10px 10px; width: 90%; height: 42%;"> 
  
  <div style="float:left; height:auto; width:30%; border: aqua; ">
 <span class="z-label" id="zk_comp_62">Consulta los puntos Vía Baloto haciendo click</span> 
<a href="http://baloto.com" target="_blank" class="z-a" id="zk_comp_64"><span class="z-label" id="zk_comp_65">aqui</span></a>
<img src="{$img_dir}formula-medica/logo.jpg" width="100" height="100" alt="logo"/>

  </div>
   
   
   
  
   <div style="float:left; height:auto; width:37%;">
       <h3 style="font-size: 10pt; color: red; font-family: verdana;">Datos para realizar el pago</h3> 
       <p style="font-size: 10pt;">Codigo de convenio</p>
                        <p style="font-size: 24pt;">950110</p>
  </div>
   
      <div style="float:left; height:auto; width:30%;">
      <p style="font-size: 9pt; margin: 20px;">Número de Pago</p>
      <p style="font-size: 24pt;" >{$codref}</p>
      </div>
      <div style=" width: 99%; float: left;">
          <div style="width: 32%; height: 5%; float:  left; color: #ffffff;">
              ......
          </div>
          
          <div style="width: 65%;  float: left; ">
              <div>
                  <div style="float:  left; width: 40%;">Valor</div>
              <div> {$valor} COP</div>
              </div>
              
              <div>
                  <div style="float:  left; width: 40%;">Nombre Comercio</div>
                  <div>Farmalatam Colombia SAS</div>
              </div>
              
              <div>
                  <div style="float:  left; width: 40%;">Fecha creación</div>
                  <div>{$fecha}</div>
              </div>
              
              <div>
                  <div style="float:  left; width: 40%;">Fecha de expiración</div>
                  <div>{$fechaex}</div>
              </div>
          </div>
      </div>
   </div>
                        

						 <span class="z-separator-ver" id="zk_comp_283">&nbsp;</span>
						 <p></p>
						 <div align="justify"><span class="Rojo12Pt z-label" id="zk_comp_288">MUY IMPORTANTE</span></div>
						 <p></p>
						 <div align="justify">
						 	<span class="z-label" id="zk_comp_293">1. Ahora puede ir a pagar a un punto Vía Baloto de forma inmediata.</span>
						 </div>
						 <p></p>
						 <div align="justify"><span class="z-label" id="zk_comp_298">2. Recuerde que el comprador debe indicarle al operador del punto Vía Baloto que la empresa recaudadora es Pagosonline.net y además debe entregarle el código de convenio junto con el número de pago.</span></div>
						 <p></p>
                                                 <div><button onclick="imprimir();">IMPRIMIR</button>                                                  </div>
						 <p></p>
						 <div align="justify"><span class="z-label" id="zk_comp_308">Una vez recibido y procesado tu pago en el punto Vía Baloto, Pagosonline informará al comercio, el cual procederá a hacer entrega del producto/servicio que estas adquiriendo.</span></div>
						 <p></p>
						 <div align="center" width="100%">
						 		
						 </div>
			 		</div>
			 		<div class="balotoButtom"></div>
			 	</div>
{/if}