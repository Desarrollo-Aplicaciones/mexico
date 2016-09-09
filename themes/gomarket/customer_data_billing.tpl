{literal}
<style type="text/css">

	#nueva-direccion_rfc {
		display: none;
		margin: 20px 20px 0;
		width: 375px;
	}

	#titulo-rfc {
		margin-top: 15px;
	}

</style>
{/literal}



<div class="rfc_container">

	
		<div class="titulo" id="titulo-rfc">
			<input type="checkbox" id="rfc_register" onclick="javascript:void(0)"> Selecciona si requieres Factura 
		</div>


	<div class="contenedor" id="nueva-direccion_rfc">

		<div class="campoCorto">
			<div class="etiqueta" id="label-rfc"><label>RFC<span class="purpura">*</span>:<br /></label>
				
					<input class="entrada" type="text" id="rfc" name="rfc" value="{if isset($dataaddressrfc.dni)}{$dataaddressrfc.dni}{/if}" placeholder="RFC*"><br />
					<input type="hidden"  id="rfc_id_address" name="rfc_id_address" value="{if isset($dataaddressrfc.id_address)}{$dataaddressrfc.id_address}{/if}" />
			</div>
		</div>

		<br>

		<div class="campoCorto">
			<div class="etiqueta" id="label-rfc_name"><label>Nombre<span class="purpura">*</span>:<br /></label>
				<input class="entrada" type="text" id="rfc_name" name="rfc_name" value="{$dataaddressrfc.alias}" placeholder="Nombre*"><br />
			</div>
		</div>

		<div class="campoLargo">
			<div class="etiqueta" id="label-rfc_address"><label>Dirección fiscal<span class="purpura">*</span>:<br /></label>						
				<input class="entrada larga" type="text" id="rfc_address" name="rfc_address" value="{$dataaddressrfc.address1}" placeholder="Dirección fiscal*"><br />
			</div>
		</div>

		<div class="campoCorto">
			<div class="etiqueta" id="label-postcode"><label>Código postal<span class="purpura">*</span>:<br /></label>						
				<input class="entrada" type="text" id="rfc_postcode" name="rfc_postcode" value="{$dataaddressrfc.postcode}" maxlength="5" placeholder="Código postal*"><br />

				<span class="hidden">Campo requerido.</span>
				<input type="hidden" id="rfc_ciudad" name="rfc_ciudad" value="{$dataaddressrfc.id_city}" />
				<input type="hidden"  id="rfc_estado" name="rfc_estado" value="{$dataaddressrfc.id_state}" />
				<input type="hidden"  id="rfc_id_colonia" name="rfc_id_colonia" value="{$dataaddressrfc.id_colonia}" />
				<input type="hidden"  id="rfc_id_city" name="rfc_id_city" value="{$dataaddressrfc.id_city}" />
			</div>
		</div>

		<br>

		<div class="campoCorto">
			<div class="etiqueta" id="label-rfc_phone"><label>Teléfono de contacto<span class="purpura">*</span>:<br /></label>						
				<input class="entrada" type="text" id="rfc_phone" name="rfc_phone" value="{$dataaddressrfc.phone}" maxlength="10" placeholder="Teléfono, fijo o celular"><br />
			</div>
		</div>
{*
		<tr>
			<td>Nombre:</td>
			<td>
				{if isset($rfc.alias)}{$rfc.alias}
				{else}<input type="text" id="rfc_name"/><span class="hidden">Campo requerido.</span>{/if}
			</td>
		</tr>
		<tr>
			<td>Dirección fiscal:</td>
			<td>
				{if isset($rfc.address1)}{$rfc.address1}
				{else}<input type="text" id="rfc_address"/><span class="hidden">Campo requerido.</span>{/if}
			</td>
		</tr>
		<tr>
			<td>Código postal:</td>
			<td>
				{if isset($rfc.postcode)}{$rfc.postcode}
				{else}
				<input type="text" id="postcode"/>
				<span class="hidden">Campo requerido.</span>
				<select class="hidden" id="ciudad"></select>
				<input type="hidden" id="estado"/>
				<input type="hidden" id="id_colonia"/>
				<input type="hidden" id="id_city"/>
				{/if}
			</td>
		</tr>
		<tr>
			<td>RFC:</td>
			<td>
				{if isset($rfc.dni)}{$rfc.dni}
				{else}<input type="text" id="rfc"/><span class="hidden">Campo requerido.</span>{/if}
			</td>
		</tr>
		<tr>
		<td>Teléfono:</td>
			<td>
				{if isset($rfc.phone)}{$rfc.phone}
				{else}<input type="text" id="rfc_phone"/><span class="hidden">Campo requerido.</span>{/if}
			</td>
		</tr>
	</table>
	
	{if !isset($rfc.dni)}<a id="rfc_save" href="javascript:void(0)">Guardar</a>{/if}*}
	</div>
</div>



<script>
	function requerir(elem){
		if( $(elem).val().length < 2 ){
			$(elem).next('span').slideDown();
			$(elem).css('border-color', '#A5689C')
			return false;
		}else{
			$(elem).next('span').slideUp();
			$(elem).css('border-color', '#3a9b37')
			return true;
		}
	}

	$('#rfc_register').unbind('click').live('click', function(){
		if ( $('#rfc_register').attr('checked') ) {

			if ( $('#rfc_id_address').val() == "" ) {
				$('#rfc_name').val("");
				$('#rfc_address').val( $('#direccion').val() );
				$('#rfc_postcode').val( $('#postcode').val() );
				$('#rfc').val("");
				$('#rfc_phone').val( $('#fijo').val() );
			}			

			$('#nueva-direccion_rfc').slideUp();
			$('#nueva-direccion_rfc').slideDown();
			asignarPostcoderfc('rfc_postcode');
		} else {

			if ( $('#rfc_id_address').val() == "" ) {
				$('#rfc_name').val("");
				$('#rfc_address').val("");
				$('#rfc_postcode').val("");
				$('#rfc').val("");
				$('#rfc_phone').val("");
			}
			
			$('#nueva-direccion_rfc').slideUp();
		}
	});

	$('#rfc').focusout(function(){
		ValidaRfc(this.value);
	});

	$('#rfc_name').focusout(function(){
		validarVacio('rfc_name');
	});

	$('#rfc_address').focusout(function(){
		validarDireccion('rfc_address');
	});

	$('#rfc_postcode').focusout(function(){		
		asignarPostcoderfc('rfc_postcode');
	});

	$('#rfc_phone').focusout(function(){
		validarTelefono('rfc_phone');
	});

	{literal}

	function asignarPostcoderfc(){		
		var valor = $('#rfc_postcode').val();
		if ( valor.length == 5) {
			$.ajax({
				type: "GET",
				url: "ajaxs/postcode.php?postcode="+valor,
				success: function(isApplicable){
					if ( isApplicable != 0) {
						vals=isApplicable.split(";");
						//alert(vals[0] + '-' + vals[1] + '-' + vals[2]);

						$('#rfc_estado').val(vals[2]);

						/* $.when( updateCity() ).done(function(a1){
							$('#city').val(vals[1]);
						}); */

						var id_state	= vals[2];
						var ruta_abs	= getAbsolutePath();

						$.ajax({
							type: "POST",
							url: "ajax_formulario_cities.php",
							dataType: 'json',
							data: 'id_state='+id_state,
							beforeSend: function(objeto){
								$('#errors_login').slideUp(200);
								//$('#loading_forms').fadeIn(500);
							},
							success: function(response) {
								//response.data[0].id
								$('#rfc_ciudad').html(response.results);

									var city = vals[1];
									$('#rfc_ciudad').val(vals[1]);
									$('#rfc_id_city').val(vals[1]);

									$.ajax({
										type: "POST",
										url: "ajax_formulario_colonia_no_carry.php",
										dataType: 'json',
										data: 'city='+city,
										beforeSend: function(objeto){
											$('#errors_login').slideUp(200);
											//$('#loading_forms').fadeIn(500);
										},
										success: function(response) {
											//response.data[0].id
											$('#rfc_id_colonia').html(response.results);
											$('#rfc_ciudad').val(vals[1]);
											$('#rfc_id_colonia').val(vals[0]);												
										},
										complete: function(objeto, sexito){
											//$('#loading_forms').fadeOut(1000);
										},
										error: function(jqXHR, textStatus, errorThrown) {
										
										}
									});


							},
							complete: function(objeto, exito){
								//$('#loading_forms').fadeOut(1000);
							},
							error: function(jqXHR, textStatus, errorThrown) {
							
							}
						});

						$('#rfc_ciudad').val(vals[1]);
						$('#rfc_id_colonia').val(vals[0]);
						
					}
					else{
						$('#rfc_postcode').val("");
					}
				}
			});
		}
	}
	{/literal}


	$('#rfc_save').click(function(){
		enviar();
	});
</script>