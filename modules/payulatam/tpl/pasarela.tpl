	<div class="payu-module-wrapper">
		<ul id="menuTab">
			<li id="menuTab3" class="menuTabButton selected">
				<img alt="Cómo configurar" src="../modules/payulatam/img/info-icon.gif">
				Pasarelas y medios de pago 
			</li>
			<li id="menuTab4" class="menuTabButton">
				<img alt="Credeciales" src="../modules/payulatam/img/credential.png">
				Crear nueva pasarela
			</li>
			<li id="menuTab5" class="menuTabButton">
				<img alt="Credeciales" src="../modules/payulatam/img/credential.png">
				Medios de pago soportados por pasarela.
			</li>
			<li id="menuTab2" class="menuTabButton"><img src="../modules/payulatam/img/credential.png" alt="Modo de trabajo"/>Modo de trabajo</li>
		</ul>


		<div id="tabList">
			<div id="menuTab3Sheet" class="tabItem selected">
				<fieldset class="tab-configure">
					<h4 class="first"> SELECCIONAR PASARELA DE PAGO POR MEDIO DE PAGO</h4>
					<p>Seleccione la pasarela de pago que desea utilizar para cada medio de pago.</p>
					<form name="Pasarelasel" action="" method="post">
							{foreach key=key item=item from=$mediosp_pasarelas}
								<p><label>{$item['nombre']}</label>
									<select name="{$key}|mediop">
										<option value=''> -- Seleccione -- </option>
										 {if isset($item['pasarela'])}
										{html_options options=$pasarelas selected=$item['pasarela']}
										{else}
										{html_options options=$pasarelas}
										{/if}	}
									</select>
							 	</p>														 	 
  							{/foreach}
							<br><br>
							<input type="hidden" option="mediop_pasarela"> 
						<label>
							 
						</label> <input type="submit" value="Guardar" name="SaveSelectedPasarela">
					</form>
					<p class="note"><sup>*</sup> Se habilitará en las futuras transacciones inmediatamente. </p>
				</fieldset>
			</div>

			<div id="menuTab4Sheet" class="tabItem">

			{literal}	
			<script type="text/javascript">
					$(function() {

						$("#nueva_pasarela").on("click", function() {

						var nombre_psarela = $("#nombre_pasarela").val();
						var estado_pasarela = $("input:radio[name=activar_pasarela]:checked").val();
							
    						$.ajax({
                				url: "{/literal}{$current}&token={$token}&configure={$module_name}{literal}",
                				//dataType: 'script',
                				cache: false,
                				//contentType: false,
                				//processData: false,
                				data: {nombre : nombre_psarela, estado : estado_pasarela, option_ajax : 'nueva_pasarela'},
                				//dataType: "html",                         
                				method: 'POST',
                				type: 'post',
                				success: function(){
                				   //location.reload();
                				},
                				 complete: function(resp){
                				 	//var json = $.parseJSON(resp);
	                				//if(json.code == '200')
                				 	//	location.reload();
                				 	// alert('Error al crear la pasarela: '+json.message);
                				 	//alert(resp.getResponseHeader('some_header'));
 									 //alert("complete"); 
  								 }
     						}).done(function(data, status, xhr){
                					var json = $.parseJSON(data);
	                				if(json.code == '200'){
	                					$("#nombre_pasarela").val("");
	                					location.reload();
	                				}else{
	                					alert('Error al crear la pasarela: '+json.message);
	                				}
                				}).fail(function() {
 									alert('Error al crear la pasarela');
                				});	
						});

						

					});

				</script>
{/literal}
					<fieldset>
						<p>Crear un nuevo nombre de pasarela de pago</p>

						<label from="nombre_pasarela">Nombre de la pasarela:</label>
						<div class="margin-form">
						<input type="text" id="nombre_pasarela" name="nombre_pasarela"> <span style="color:red">*</span> 
						</div>
						<label from="activar_pasarela">Activar Pasarela:</label>
						<div class="margin-form">
						<input type="radio" name="activar_pasarela" value="1" checked="true"> Si
						<input type="radio" name="activar_pasarela" value="0"> No 
						</div>
						<div class="margin-form">
						<input type="hidden" option="credencieles_pasarela"> 
						<button id="nueva_pasarela">Crear Pasarela</button>
						
						</div>
					</fieldset>
			
					<br>
				<form method="POST" id="frm_data_mediosp" action="">
					<fieldset>
						<p>Diligencie unicamente los datos que requiere su pasarela de pago, para mas información sobre pasarelas soportadas, comuníquese con el personal administrativo de la plataforma.</p>
						<label from="select_pasarela">Pasarela de pago:</label>
						<div class="margin-form">
							{literal}	
							<script type="text/javascript">
							$(function() {

								$("#select_pasarela").change(function() {
									
									if($("#select_pasarela").val() != "" )
										{ 
											var tipo_credenciales = $("input:radio[name=tipo_credenciales]:checked").val();
    										$.ajax({
                								url: "{/literal}{$current}&token={$token}&configure={$module_name}{literal}",
                								cache: false,
                								data: {nombre : $("#select_pasarela").val(), option_ajax : 'datos_pasarela',tipo_credenciales:tipo_credenciales},
                								method: 'POST',
                								type: 'post',
                								success: function(){
                								   //location.reload();
                								},
                								 complete: function(resp){
 													 //alert("complete"); 
  												 }
     										}).done(function(data, status, xhr){
                									var json = $.parseJSON(data);
	                								if(json.code == '200'){
	                									$.each( json.results, function( key, value ) {
	                										$("#"+key).val(value);
														});
	                								       // actualizar campos
	                								}else{
	                									alert('Error al consultar datos de pasarela: '+json.message);
	                									 $('.files_mediosp').val(''); 
	                								}
                								}).fail(function() {
 													alert('Error al consultar los datos de la pasarela seleccionada');
 													 $('.files_mediosp').val(''); 
 												
                								});
                						}
											});
										});
							</script>
							{/literal}
						<select name="select_pasarela" id="select_pasarela">
							<option value=''> -- Seleccione -- </option>
								{html_options options=$pasarelas}
						</select>
						</div>
						<label from="tipo_credenciales">Tipo de credenciales:</label>
						<div class="margin-form">
						<input type="radio" name="tipo_credenciales" value="1"> Producción
						<input type="radio" name="tipo_credenciales" value="0" checked="true"> Pruebas 
						</div>							
						 	{foreach key=key item=item from=$model_Datos_pasarelas}
						 		{if $item['Field'] == 'id_dato_pasarela' || $item['Field'] == 'id_pasarela' || $item['Field'] == 'produccion' || $item['Field'] == 'medios_pago'}
									<input type="hidden" placeholder="{$item['Field']}" id="{$item['Field']}" name="{$item['Field']}" class="files_mediosp"> 
						 		{else}
						 		<label from="{$item['Field']}">{$item['Field']}</label>
								<div class="margin-form">
									<input type="text" placeholder="{$item['Field']}" id="{$item['Field']}" name="{$item['Field']}" class="files_mediosp">  
								</div>
								{/if}							 	 
  							{/foreach}

  								
						<div class="margin-form">
							<input type="hidden" name="datos_pasarela"> 
							<input type="submit" value="Guardar crdecieles de pasarela" class="button">
						</div>
				</fieldset>
				</form>

			</div>


			<div id="menuTab5Sheet" class="tabItem">

				<form method="POST" action="">
					<fieldset>
						<p>Seleccione los medios de pagos soportados por cada pasarela de pago</p>
						{foreach from=$pasarelas item=pasarela}
							<p><label><b>{$pasarela}:</b></label>	{foreach key=key item=item from=$mediosp_pasarelas}
									<input type="checkbox" name="{$pasarela}" value="{$key}">{$item['nombre']}  								 	 
  								{/foreach}
								</p>
							{/foreach}
					
								<div class="margin-form">
									<input type="hidden" name="credencieles_pasarela"> 
							<input type="submit" value="Guardar credecieles de pasarela" class="button">
						</div>
					</fieldset>
				</form>

			</div>

			<div id="menuTab6Sheet" class="tabItem">

				<form method="POST" action="">
					<fieldset>
						<p> Para utilizar este módulo, por favor  llene el siguiente formulario con la información para  iniciar sesión proporcionada por OpenPay.</p>
						<input type="hidden" value="Por_ingresar" name="dato_pasarela">
							<label from="mercid">Identificacion del Comerciante</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="mercid" name="mercid"> <span style="color:red">*</span> La identificación del comerciante (usuario) que le ha suministro PayU Latam duarntre  la creación de su cuenta.
						</div>
							<label from="apiKey">Clave Api</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="apiKey" name="apiKey"> <span style="color:red">*</span> La clave de API que le ha suministro  PayU Latam para en la creación de su cuenta.
						</div>
							<label from="accountid">ID de la cuenta:</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="accountid" name="accountid">  ID de la cuenta que le  suministro  PayU Latam cuando creo  su cuenta.
						</div>
							<label from="pseco">Pse-Co:</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="pseco" name="pseco">  Pse para Colombia.
						</div>
							<label from="apilogin">Api Login:</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="apilogin" name="apilogin">  ApiLogin otorgado por PayuLatam para la integración.
						</div>					
								<div class="margin-form">
							<input type="submit" value="salvar" class="button">
						</div>
					</fieldset>
				</form>

			</div>






			<div id="menuTab7Sheet" class="tabItem">

				<form method="POST" action="">
					<fieldset>
						<p> Para utilizar este módulo, por favor  llene el siguiente formulario con la información de <b>pruebas</b> para  iniciar sesión proporcionada por OpenPay.</p>
						<input type="hidden" value="Por_ingresar" name="dato_pasarela">
							<label from="mercid">Identificacion del Comerciante</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="mercid" name="mercid"> <span style="color:red">*</span> La identificación del comerciante (usuario) que le ha suministro PayU Latam duarntre  la creación de su cuenta.
						</div>
							<label from="apiKey">Clave Api</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="apiKey" name="apiKey"> <span style="color:red">*</span> La clave de API que le ha suministro  PayU Latam para en la creación de su cuenta.
						</div>
							<label from="accountid">ID de la cuenta:</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="accountid" name="accountid">  ID de la cuenta que le  suministro  PayU Latam cuando creo  su cuenta.
						</div>
							<label from="pseco">Pse-Co:</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="pseco" name="pseco">  Pse para Colombia.
						</div>
							<label from="apilogin">Api Login:</label>
						<div class="margin-form">
							<input type="text" value="Por_ingresar" id="apilogin" name="apilogin">  ApiLogin otorgado por PayuLatam para la integración.
						</div>					
								<div class="margin-form">
							<input type="submit" value="salvar" class="button">
						</div>
					</fieldset>
				</form>

			</div>
	  	</div>
		
	</div>
