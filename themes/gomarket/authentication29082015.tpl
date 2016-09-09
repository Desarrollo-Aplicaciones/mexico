{capture name=path}
	{if !isset($email_create)}{l s='Ingreso al Sistema'}{else}
		<a href="{$link->getPageLink('authentication', true)|escape:'html'}" rel="nofollow" title="{l s='Ingreso al Sistema'}">{l s='Ingreso al Sistema'}</a>
		<span class="navigation-pipe">{$navigationPipe}</span>{l s='Create your account'}
	{/if}
{/capture}

<script type="text/javascript">
// <![CDATA[
var idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}false{/if};
var countries = new Array();
var countriesNeedIDNumber = new Array();
var countriesNeedZipCode = new Array(); 
{if isset($countries)}
	{foreach from=$countries item='country'}
		{if isset($country.states) && $country.contains_states}
			countries[{$country.id_country|intval}] = new Array();
			{foreach from=$country.states item='state' name='states'}
				countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state|intval}', 'name' : '{$state.name|addslashes}'{rdelim});
			{/foreach}
		{/if}
		{if $country.need_identification_number}
			countriesNeedIDNumber.push({$country.id_country|intval});
		{/if}
		{if isset($country.need_zip_code)}
			countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
		{/if}
	{/foreach}
{/if}
$(function(){ldelim}
	$('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}{if isset($address)}{$address->id_state|intval}{/if}{/if}]').attr('selected', true);
{rdelim});
//]]>
	{literal}
	$(document).ready(function() {
	$('#company').on('input',function(){
			vat_number();
		});
		vat_number();
		function vat_number()
		{
			if ($('#company').val() != '')
				$('#vat_number').show();
			else
				$('#vat_number').hide();
		}
	});
	{/literal}
</script>
<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		// Retrocompatibility with 1.4
		if (typeof baseUri === "undefined" && typeof baseDir !== "undefined")
		baseUri = baseDir;
		$('#create-account_form').submit(function(){
			submitFunction();
			return false;
		});
	});
	function submitFunction()
	{
		$('#create_account_error').html('').hide();
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			url: baseUri,
			async: true,
			cache: false,
			dataType : "json",
			data: {
				controller: 'authentication',
				SubmitCreate: 1,
				ajax: true,
				email_create: $('#email_create').val(),
				back: $('input[name=back]').val(),
				token: token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#create_account_error').html('<ol>'+errors+'</ol>').show();
				}
				else
				{
					// adding a div to display a transition
					$('#center_column').html('<div id="noSlide">'+$('#center_column').html()+'</div>');
					$('#noSlide').fadeOut('slow', function(){
						$('#noSlide').html(jsonData.page);
						// update the state (when this file is called from AJAX you still need to update the state)
						bindStateInputAndUpdate();
						$(this).fadeIn('slow', function(){
					document.location = '#account-creation';
						});
					});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert("TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}

	{/literal}
</script>
<script	src="{$js_dir}/aut-validacion.js" type="text/javascript"></script>
{* <div class="titulo-pasos" > Registro </div> *} {if !isset($back) ||
$back != 'my-account'}{assign var='current_step' value='login'}{include
file="$tpl_dir./order-steps.tpl"}{/if} {include
file="$tpl_dir./errors.tpl"} {assign var='stateExist' value=false}
{assign var="postCodeExist" value=false} {if !isset($email_create)}
<div class="contenido">
	<div class="contenedor" id="primerHole">
		<div class="current">
			<span class="titulo">Iniciar sesión (usuarios registrados)</span> <span
				class="obliga">(<span class="purpura">*</span>) Campos Obligatorios
			</span>
		</div>
		<form action="{$link->getPageLink('authentication', true)}"
			method="post" id="login_form">
			<fieldset>
				<div style="text-align:left;">
					<label class="etiqueta">E-mail<span class="purpura">*</span>:
					</label><br /> <input type="text" id="email" name="email"
						value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" /><br>
					<div id="erroremail" class="rterror"></div>
					<label class="etiqueta">Contraseña<span class="purpura">*</span>:
					</label><br /> <input type="password" id="passwd" name="passwd"
						value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|stripslashes}{/if}" />
					<div id="errorpasswd" class="rterror"></div>
				</div>
				{if isset($back)}<input type="hidden" class="hidden" name="back"
					value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
				<div class="submitSpace">
					<div class="submit">
						<input type="submit" id="SubmitLogin" name="SubmitLogin"
							value="Ingresar">
					</div>
					<div class="olvidaSpace">
						<div class="TOS3">
							<input type="checkbox" id="TOSlogin"/>
								Acepto <a href="{$base_uri}?id_cms=3&controller=cms" target="blank">términos y condiciones</a> legales.<span class="purpura">*</span>
							<div id="errorTOSlogin" class="rterror"></div>
						</div>
						<a class="olvida" href="{$link->getPageLink('password')}">{l
							s='Forgot your password?'}</a>
					</div>
				</div>
			</fieldset>
		</form>
	</div>

	<div class="contenedor" id="segundoHole">

		{if isset($GUEST_FORM_ENABLED) &&$GUEST_FORM_ENABLED }
		<div class="currentOpcion" id="titregistrado">
			<div class="titulo">

				<input type="radio" id="opregistrado" name="opcompra"
					checked="checked"> Crear mi cuenta
			</div>
		</div>
		<div class="currentOpcion" id="titinvitado">
			<div class="titulo">
				<input type="radio" id="opinvitado" name="opcompra" {if
					isset($comprarapida)}  checked="checked"{/if}> Compra Rápida
			</div>
		</div>
		{else}
		<div class="current">
			<div class="titulo">Crear mi cuenta</div>
			
		</div>
		{/if}
	<div class="registrarse">
		<div id="create_acount">
			<form action="{$link->getPageLink('authentication', true)}"
				method="post" id="account-creation_form">
				{$HOOK_CREATE_ACCOUNT_TOP}
				<fieldset>
					<div class="emailreg">
						<p class="form-registro" id="label-email">
							{l s='E-mail'}<span class="purpura">*</span>:
						</p>
						<input type="text" id="reg-email" name="email"
							value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
						<div id="errorreg-email" class="rterror"></div>
					</div>
					<div class="regform">
						<p class="form-registro" id="label-passwd">
							{l s='Crear una Contraseña'}<span class="purpura">*</span>:
						</p>
						<input type="password" name="passwd" id="passwdr" selector="clave" />
						<div id="errorpasswdr" class="rterror"></div>
					</div>
					<div class="regform">
						<p class="form-registro" id="label-conf">
							{l s='Confirmar Contraseña'}<span class="purpura">*</span>:
						</p>
						<input type="password" name="conf-passwd" id="conf-passwd"
							selector="confirma" />
						<div id="errorconf-passwd" class="rterror"></div>
					</div>
					<div class="tituloreg">
						<p class="form-registro" id="label-gender">
							Género<span class="purpura">*</span>:
						</p>
						<select name="id_gender" id="genero">
						<option value="" disabled {if !(isset($smarty.post.id_gender))} selected{/if}>--</option>
						{foreach from=$genders key=k item=gender}
						<option value="{$gender->id}" id="id_gender{$gender->id}"
							{if isset($smarty.post.id_gender) &&
								$smarty.post.id_gender== $gender->id} selected{/if}>
							{if $gender->name==M}Hombre{/if}
							{if $gender->name==F}Mujer{/if}
						</option>
						{/foreach}
						</select>
						<div id="errortit" class="rterror"></div>
					</div>
					<div class="regform">
						<p class="form-registro" id="label-first">
							{l s='First name'}<span class="purpura">*</span>:
						</p>
						<input onkeyup="$('#firstname').val(this.value);" type="text"
							id="customer_firstname" name="customer_firstname"
							value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
						<div id="errorcustomer_firstname" class="rterror"></div>
					</div>
					<div class="regform">
						<p class="form-registro" id="label-last">
							{l s='Last name'}<span class="purpura">*</span>:
						</p>
						<input onkeyup="$('#lastname').val(this.value);" type="text"
							id="customer_lastname" name="customer_lastname"
							value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
						<div id="errorcustomer_lastname" class="rterror"></div>
					</div>
{*					<div class="regform">
						<p class="form-registro" id="label-id">
							{l s='Tipo documento'}:
						</p>
						<select id="id" name="id">
							<option value="">-</option>
							<option value="1">IFE</option>
							<option value="2">VISA</option>
							<option value="3">Pasaporte</option>
						</select>
						<div id="errorid" class="rterror"></div>
					</div>
					<div class="regform">
						<p class="form-registro" id="label-dni">
							{l s='Identificación'}:
						</p>
						<input type="text" id="dni" name="dni" />
						<div id="errordni" class="rterror"></div>
					</div>
*}					<div class="regform">
						<p class="form-registro" id="label-birth">{l s='Date of Birth'}<span class="purpura">*</span>:</p>
						<div>
							<select id="days" name="days">
								<option disabled {if !($sl_day)} selected{/if} value="">día</option> {foreach from=$days item=day}
								<option value="{$day}" {if ($sl_day== $day)} selected="selected"{/if}> {$day}</option>
								{/foreach}
							</select> <select id="months" name="months">
								<option disabled {if !($sl_month)} selected{/if} value="">mes</option> {foreach from=$months key=k
								item=month}
								<option value="{$k}" {if ($sl_month== $k)} selected="selected"{/if}> {$k}{* l s=$month *}</option> {/foreach}
							</select> <select id="years" name="years">
								<option disabled {if !($sl_year)} selected{/if} value="">año</option> {foreach from=$years item=year}
								<option value="{$year}" {if ($sl_year== $year)} selected="selected"{/if}> {$year}</option>
								{/foreach}
							</select>
						</div>
						<div id="errordays" class="rterror"></div>
						<div id="errormonths" class="rterror"></div>
						<div id="erroryears" class="rterror"></div>
					</div>
					<div class="TOS">
							<input type="checkbox" id="TOSreg"/>
								Acepto <a href="{$base_uri}?id_cms=3&controller=cms" target="blank">términos y condiciones</a> legales.<span class="purpura">*</span>
							<div id="errorTOSreg" class="rterror"></div>
					</div>
				</fieldset>
				{if isset($PS_REGISTRATION_PROCESS_TYPE) &&
				$PS_REGISTRATION_PROCESS_TYPE}
				<fieldset class="account_creation">
					<h3>{l s='Your address'}</h3>
					{foreach from=$dlv_all_fields item=field_name} {if $field_name eq
					"company"} {if !$b2b_enable}
					<p class="text">
						<label for="company">{l s='Company'}</label> <input type="text"
							class="text" id="company" name="company"
							value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
					</p>
					{/if} {elseif $field_name eq "vat_number"}
					<div id="vat_number" style="display: none;">
						<p class="text">
							<label for="vat_number">{l s='VAT number'}</label> <input
								type="text" class="text" name="vat_number"
								value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
						</p>
					</div>
					{elseif $field_name eq "firstname"}
					<p class="required text">
						<label for="firstname">{l s='First name'} <sup>*</sup>
						</label> <input type="text" class="text" id="firstname"
							name="firstname"
							value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
					</p>
					{elseif $field_name eq "lastname"}
					<p class="required text">
						<label for="lastname">{l s='Last name'} <sup>*</sup>
						</label> <input type="text" class="text" id="lastname"
							name="lastname"
							value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
					</p>
					{elseif $field_name eq "address1"}
					<p class="required text">
						<label for="address1">{l s='Address'} <sup>*</sup>
						</label> <input type="text" class="text" name="address1"
							id="address1"
							value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
						<span class="inline-infos">{l s='Street address, P.O. Box, Company
							name, etc.'}</span>
					</p>
					{elseif $field_name eq "address2"}
					<p class="text">
						<label for="address2">{l s='Address (Line 2)'}</label> <input
							type="text" class="text" name="address2" id="address2"
							value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
						<span class="inline-infos">{l s='Apartment, suite, unit, building,
							floor, etc...'}</span>
					</p>
					{elseif $field_name eq "postcode"} {assign var='postCodeExist'
					value=true}
					<p class="required postcode text">
						<label for="postcode">{l s='Zip / Postal Code'} <sup>*</sup>
						</label> <input type="text" class="text" name="postcode"
							id="postcode"
							value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"
							onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</p>
					{elseif $field_name eq "city"}
					<p class="required text">
						<label for="city">{l s='City'} <sup>*</sup>
						</label> <input type="text" class="text" name="city" id="city"
							value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
					</p>
					<!--
if customer hasn't update his layout address, country has to be verified
but it's deprecated
-->
					{elseif $field_name eq "Country:name" || $field_name eq "country"}
					<p class="required select">
						<label for="id_country">{l s='Country'} <sup>*</sup>
						</label> <select name="id_country" id="id_country">
							<option value="">-</option> {foreach from=$countries item=v}
							<option value="{$v.id_country}" {if ($sl_country==
								$v.id_country)} selected="selected"{/if}>{$v.name}</option>
							{/foreach}
						</select>
					</p>
					{elseif $field_name eq "State:name" || $field_name eq 'state'}
					{assign var='stateExist' value=true}
					<p class="required id_state select">
						<label for="id_state">{l s='State'} <sup>*</sup>
						</label> <select name="id_state" id="id_state">
							<option value="">-</option>
						</select>
					</p>
					{/if} {/foreach} {if $postCodeExist eq false}
					<p class="required postcode text hidden">
						<label for="postcode">{l s='Zip / Postal Code'} <sup>*</sup>
						</label> <input type="text" class="text" name="postcode"
							id="postcode"
							value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"
							onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</p>
					{/if} {if $stateExist eq false}
					<p class="required id_state select hidden">
						<label for="id_state">{l s='State'} <sup>*</sup>
						</label> <select name="id_state" id="id_state">
							<option value="">-</option>
						</select>
					</p>
					{/if}
					<p class="textarea">
						<label for="other">{l s='Additional information'}</label>
						<textarea name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
					</p>
					{if isset($one_phone_at_least) && $one_phone_at_least}
					<p class="inline-infos">{l s='You must register at least one phone
						number.'}</p>
					{/if}
					<p class="text">
						<label for="phone">{l s='Home phone'}</label> <input type="text"
							class="text" name="phone" id="phone"
							value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
					</p>
					<p
						class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if} text">
						<label for="phone_mobile">{l s='Mobile phone'}{if
							isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}
						</label> <input type="text" class="text" name="phone_mobile"
							id="phone_mobile"
							value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
					</p>
					<p class="required text" id="address_alias">
						<label for="alias">{l s='Assign an address alias for future
							reference.'} <sup>*</sup>
						</label> <input type="text" class="text" name="alias" id="alias"
							value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
					</p>
				</fieldset>
				<fieldset class="account_creation dni">
					<h3>{l s='Tax identification'}</h3>
					<p class="required text">
						<label for="dni">{l s='Identification number'} <sup>*</sup>
						</label> <input type="text" class="text" name="dni" id="dni"
							value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" /> <span
							class="form_info">{l s='DNI / NIF / NIE'}</span>
					</p>
				</fieldset>
				{/if} {$HOOK_CREATE_ACCOUNT_FORM} {if $newsletter}
				<div class="forma">
					<div class="lastOptions">
						<p class="form-registro">
							<input type="checkbox" name="newsletter" id="newsletter"
								value="1" {if
								isset($smarty.post.newsletter) AND $smarty.post.newsletter==
								1} checked="checked" {/if} autocomplete="off" /> <label
								for="newsletter">{l s='Inscribirse al Boletín'}</label>
						</p><br />
						{*<p> <input type="checkbox" name="optin" id="optin" value="1" {if
						isset($smarty.post.optin) AND $smarty.post.optin == 1}
						checked="checked"{/if} autocomplete="off"/> <label for="optin"
						style="font-size:8pt;">{l s='Recibir Ofertas especiales'}</label>
						</p>*}
						<p class="form-registro">
							<input type="checkbox" name="sms" id="sms" value="1"
								autocomplete="off" /> <label for="sms">{l s='Recibir avisos y
								ofertas a tú celular'}</label>
						</p>
					</div>
					<div class="cart_navigation required submit">
						<input type="hidden" name="email_create" value="1" /> <input
							type="hidden" name="is_new_customer" value="1" /> {if
						isset($back)}<input type="hidden" class="hidden" name="back"
							value="{$back|escape:'htmlall':'UTF-8'}" />{/if} <input
							type="submit" name="submitAccount" id="submitAccount"
							value="{l s='Crear cuenta'}" />
					</div>
				</div>
				{/if}
			</form>
		</div>
		</div>
		<!-- quest form --  formulario  modo invitado-->
		{if isset($GUEST_FORM_ENABLED) &&$GUEST_FORM_ENABLED } {include
		file="$tpl_dir./quest_form.tpl"}
	</div>
	{/if}

</div>
<!-- tercer cuadro-->
<div class="contenedor" id="tercerHole">
	<img src="{$img_dir}ayuda.png" class="imagenAyuda"/> 
	<p class="tituloAyuda">¿Necesitas Ayuda?</p>
	<p class="ciudadAyuda"><br />Línea de Atención y Ventas Nacional<br />sin costo:<br /><span>(01) 55.4170.8434</span></p>
	{* <p class="ciudadAyuda">Nacional:<br /><span>01 800 269.4408</span></p> *}
{* <div class="seguridad"> <div
	class="titulo">Seguridad</div> <div id="seguridadTitulo">Realiza tu
	compra con tranquilidad, contamos con certificación de seguridad.</div>
	<div style="font-size:7pt;display:none;">* <b>Absoluta</b>
	discreción</div> <div style="font-size:7pt;display:none;">* Mejor
	precio <span style="color:#b7689e">Garantizado*</span></div> <div><img
	src="{$img_dir}authentication/g644.png" id="seguridadImagen"/></div>
	</div> <div class="medios_pago"> <div class="titulo">Nuestros Medios de
	pago</div> <div> <div class="imagen_medios"><img
	src="{$img_dir}authentication/amex.png" width="100%"/></div> <div
	class="imagen_medios"><img src="{$img_dir}authentication/visa.png"
	width="100%"/></div> <div class="imagen_medios"><img
	src="{$img_dir}authentication/master.png" width="100%"/></div> <div
	class="imagen_medios"><img src="{$img_dir}authentication/diners.png"
	width="100%"/></div> <div class="imagen_medios"><img
	src="{$img_dir}authentication/pse.png" width="100%"/></div> <div
	class="imagen_medios"><img src="{$img_dir}authentication/baloto.png"
	width="100%"/></div> <div class="imagen_medios"><img
	src="{$img_dir}authentication/cod.png" width="100%"/></div> <div
	class="imagen_medios"><img src="{$img_dir}authentication/efecty.png"
	width="100%"/></div> </div> </div> *} </div> {/if} {literal} <script>
	/* $('#submitAccount').click(function(){ var id_gender
	=$('input[name="id_gender"]').is(':checked'); var email =
	$('#reg-email').val(); var customer_firstname =
	$('#customer_firstname').val(); var customer_lastname =
	$('#customer_lastname').val(); var id = $('#id').val(); var dni =
	$('#dni').val(); var days = $('#days').val(); var months =
	$('#months').val(); var years = $('#years').val(); var passwd =
	$('input[selector="clave"]').val(); var confpasswd =
	$('input[selector="confirma"]').val();
        
            $('.validacion').remove();
        
            if(!id_gender){
                $('#label-gender').append('<span class="validacion" id="obliga-gender">*</span>');
            }else{
                $('#obliga-gender').remove();
            }
           
            if(email==""){
                $('#label-email').append('<span class="validacion" id="obliga-email">*</span>');
            }else{
                if (email.match(/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/)){
                    $('#obliga-email').remove();
                }else{
                    $('#label-email').append('<span class="validacion" id="obliga-email">Email no Válido</span>');
                }
            }
            
            if(customer_firstname==""){
                $('#label-first').append('<span class="validacion" id="obliga-first">*</span>');
            }else{
                $('#obliga-first').remove();           
            }
            
            if(customer_lastname==""){
                $('#label-last').append('<span class="validacion" id="obliga-last">*</span>');
            }else{
                $('#obliga-last').remove();           
            }
            
            if(id==""){
                $('#label-id').append('<span class="validacion" id="obliga-id">*</span>');
            }else{
                $('#obliga-id').remove();
            }
            
            if(dni==""){
                $('#label-dni').append('<span class="validacion" id="obliga-dni">*</span>');
            }else{
                if (dni.match(/^[\w-\.]{5,11}$/)){
                    $('#obliga-dni').remove();
                }else{
                    $('#label-dni').append('<span class="validacion" id="obliga-dni">Documento no Válido</span>');
                }
            }
            
            if((days=="")||(months=="")||(years=="")){
                $('#label-birth').append('<span class="validacion" id="obliga-birth">*</span>');
            }else{
                $('#obliga-birth').remove();
            }
            
            if(confpasswd==""){
                $('#label-conf').append('<span class="validacion" id="obliga-conf">*</span>');
            }else{
                $('#obliga-conf').remove();
                if(passwd==confpasswd){
                    $('#obliga-passwd').remove();
                    $('#obliga-conf').remove();
                }else{
                    $('#obliga-passwd').remove();
                    $('#obliga-conf').remove();
                    $('#label-passwd').append('<span class="validacion" id="obliga-passwd">No Coincide</span>');
                    $('#label-conf').append('<span class="validacion" id="obliga-conf">No Coincide</span>');
                }
            }
            
            if(passwd==""){
                $('#label-passwd').append('<span class="validacion" id="obliga-passwd">*</span>');
            }else{
                $('#obliga-passwd').remove();
            }
            
            var error=$('.validacion').length;
        
            if(error==0){
                $('form').submit();
            }
        });*/
    </script>
{/literal}