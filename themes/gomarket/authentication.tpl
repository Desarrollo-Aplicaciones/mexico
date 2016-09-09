{capture name=path} {if !isset($email_create)}{l s='Ingreso al
Sistema'}{else}
<a href="{$link->getPageLink('authentication', true)|escape:'html'}"
	rel="nofollow" title="{l s='Ingreso al Sistema'}">{l s='Ingreso al
	Sistema'}</a>
<span class="navigation-pipe">{$navigationPipe}</span>
{l s='Create your account'} {/if} {/capture}
<script src="{$base_uri}/js/jquery/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
// <![CDATA[
var idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}false{/if};
var countries = new Array();
var countriesNeedIDNumber = new Array();
var countriesNeedZipCode = new Array(); 
var base_uri_ajax = "{$base_uri}";
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
</div></div></div>
	<div class="auth_header">
		<div class="block_permanent_links">
				<p id="header_link_contact">
					<span> Línea de atención y ventas nacional sin costo: </span>
					<a class="number" href="tel:+5567321100">55 6732 1100</a>
				</p>
		</div>
		<div style="position:relative">
		<div class="header_bg1"></div>
		<div class="header_bg2 gradient"></div>
		<div class="container_24 gradient">
			<div class="auth_logo"><a href="{$base_uri}"><img src="{$logo_url}" /></a></div>
		</div>
		</div>
	</div>
<div class="container_24">
{assign var='stateExist' value=false}
{assign var="postCodeExist" value=false}
{*if !isset($email_create)*}
<div class="contenido">
	{$HOOK_LOGINIZQ}
	<div class="contenedor" id="tercerHole_resp">
		<span class="current_resp">Ingreso</span>
		{if !isset($smarty.cookies.validamobile)}
			<a href="javascript:void(0);" class="fb_connect_button">
				<img src="{$base_uri}/themes/gomarket/img/my-account/fb+.png" alt="iniciar sesión con Facebook"/>Iniciar sesión
			</a>
			<a href="javascript:void(0);" class="g_connect_button">
				<img src="{$base_uri}/themes/gomarket/img/my-account/G+.png" alt="iniciar sesión con Google"/>Iniciar sesión
			</a>
		{/if}
	</div>
	<div id="errorHole">{include file="$tpl_dir./errors.tpl"}</div>
	<a href="javascript:void(0);" class="resp_button">Ya estoy registrado</a>
	<div class="contenedor" id="primerHole">
		<div class="current">
			<span class="titulo">Ya estoy registrado</span>
		</div>
		 <div class="obliga">(<span class="purpura">*</span>) Campo requerido</div>
		<form action="{$link->getPageLink('authentication', true)}"
			method="post" id="login_form">
			<fieldset>
				<div style="text-align:left;">
					<label class="etiqueta">Ingresa tu correo electrónico<span class="purpura">*</span>
					</label><br /> <input type="text" id="email" name="email"
						value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" /><br>
					<div id="erroremail" class="rterror"></div>
					<label class="etiqueta">Ingresa tu contraseña<span class="purpura">*</span>
					</label><br /> <input type="password" id="passwd" name="passwd"
						value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|stripslashes}{/if}" />
					<div id="errorpasswd" class="rterror"></div>
				</div>
				<div class="olvidaSpace">
					<a class="olvida" href="{$link->getPageLink('password')}">{l s='Forgot your password?'} <span class="resaltar">Haz clic aquí</span></a>
				</div>
				{if isset($back)}
					<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />
				{/if}
				<div class="submitSpace">
					<div class="submit">
						<input type="submit" id="SubmitLogin" name="SubmitLogin"
							value="Ingresar">
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<a href="javascript:void(0);" class="resp_button">No estoy registrado</a>
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
			<div class="titulo">Crear una cuenta</div>
		</div>
		<div class="obliga">(<span class="purpura">*</span>) Campo requerido</div>
		{/if}
	<div id="create_acount">
		<form action="{$link->getPageLink('authentication', true)}"
			method="post" id="account-creation_form">
			{$HOOK_CREATE_ACCOUNT_TOP}
			<fieldset>
				{*<div class="tituloreg">
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
				</div>*}
				<div class="emailreg">
					<p class="form-registro" id="label-email">Ingresa tu correo electrónico<span class="purpura">*</span></p>
					<input type="text" id="reg-email" name="email"
						value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
					<div id="errorreg-email" class="rterror"></div>
				</div>
				<div class="regform">
					<p class="form-registro" id="label-first">Ingresa tu nombre<span class="purpura">*</span></p>
					<input onkeyup="$('#firstname').val(this.value);" type="text"
						id="customer_firstname" name="customer_firstname"
						value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
					<div id="errorcustomer_firstname" class="rterror"></div>
				</div>
				<div class="regform">
					<p class="form-registro" id="label-passwd">Crea una contraseña<span class="purpura">*</span></p>
					<input type="password" name="passwd" id="passwdr" selector="clave" />
					<div id="errorpasswdr" class="rterror"></div>
				</div>
				<div class="regform">
					<p class="form-registro" id="label-conf">Confirma la contraseña<span class="purpura">*</span></p>
					<input type="password" name="conf-passwd" id="conf-passwd"
						selector="confirma" />
					<div id="errorconf-passwd" class="rterror"></div>
				</div>
				<div class="TOS">
					<div class="TOSreg">
						<input type="checkbox" value="None" id="TOSreg" name="check" />
						<label for="TOSreg"></label>
					</div>
					<div class="TOSlegend">Confirmo que soy mayor de edad y acepto <a href="{$base_uri}?id_cms=3&controller=cms" target="blank">los términos y las condiciones</a> legales.</div>
					<div id="errorTOSreg" class="rterror"></div>
				</div>
				{*<div class="regform">
					<p class="form-registro" id="label-last">
						{l s='Last name'}<span class="purpura">*</span>:
					</p>
					<input onkeyup="$('#lastname').val(this.value);" type="text"
						id="customer_lastname" name="customer_lastname"
						value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
					<div id="errorcustomer_lastname" class="rterror"></div>
				</div>
				<div class="regform">
					<p class="form-registro" id="label-id">
						{l s='Tipo documento'}<span class="purpura">*</span>:
					</p>
					<select id="id" name="id">
						<option value="">-</option>
						{foreach from=$document_types item=tipo_documento}
							<option value="{$tipo_documento.id_document}" {if !$tipo_documento.active}disabled{/if}>{$tipo_documento.document}</option>
						{/foreach}
					</select>
					<div id="errorid" class="rterror"></div>
				</div>
				<div class="regform">
					<p class="form-registro" id="label-dni">
						{l s='Identificación'}<span class="purpura">*</span>:
					</p>
					<input type="text" id="dni" name="dni" />
					<div id="errordni" class="rterror"></div>
				</div>
				<div class="regform">
					<p class="form-registro" id="label-birth">{l s='Date of Birth'}<span class="purpura">*</span>:</p>
					<div>
						<select id="days" name="days">
							<option disabled {if !($sl_day)} selected{/if} value="">día</option> {foreach from=$days item=day}
							<option value="{$day}" {if ($sl_day== $day)} selected="selected"{/if}> {$day}</option>
							{/foreach}
						</select> <select id="months" name="months">
							<option disabled {if !($sl_month)} selected{/if} value="">mes</option> {foreach from=$months key=k
							item=month}
							<option value="{$k}" {if ($sl_month== $k)} selected="selected"{/if}> {$k}</option> {/foreach}
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
				*}
			</fieldset>
			{*if isset($PS_REGISTRATION_PROCESS_TYPE) &&
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
			{/if*}
			{$HOOK_CREATE_ACCOUNT_FORM}
			{*if $newsletter}
			<div class="forma">
				<div class="lastOptions">
					<p class="form-registro">
						<input type="checkbox" name="newsletter" id="newsletter"
							value="1" {if
							isset($smarty.post.newsletter) AND $smarty.post.newsletter==
							1} checked="checked" {/if} autocomplete="off" /> <label
							for="newsletter">{l s='Inscribirse al Boletín'}</label>
					</p><br />
					<p> <input type="checkbox" name="optin" id="optin" value="1" {if
					isset($smarty.post.optin) AND $smarty.post.optin == 1}
					checked="checked"{/if} autocomplete="off"/> <label for="optin"
					style="font-size:8pt;">{l s='Recibir Ofertas especiales'}</label>
					</p>
					<p class="form-registro">
						<input type="checkbox" name="sms" id="sms" value="1"
							autocomplete="off" /> <label for="sms">{l s='Recibir avisos y
							ofertas a tú celular'}</label>
					</p>
				</div>
			</div>
			{/if*}
				<div class="required submit">
					<input type="hidden" name="email_create" value="1" /> <input
						type="hidden" name="is_new_customer" value="1" /> {if
					isset($back)}<input type="hidden" class="hidden" name="back"
						value="{$back|escape:'htmlall':'UTF-8'}" />{/if} <input
						type="submit" name="submitAccount" id="submitAccount"
						value="{l s='Crear cuenta'}" />
				</div>
		</form>
	</div>
		<!-- quest form --  formulario  modo invitado-->
		{if isset($GUEST_FORM_ENABLED) &&$GUEST_FORM_ENABLED }
		{include file="$tpl_dir./quest_form.tpl"}
	</div>
	{/if}
</div>
<div class="contenedor" id="tercerHole">
	<a href="javascript:void(0);" class="toggleHoles"><span>No</span> estoy registrado</a>
	{if !isset($smarty.cookies.validamobile)}
		<a href="javascript:void(0);" class="fb_connect_button">
			<img src="{$base_uri}/themes/gomarket/img/my-account/fb-.png" alt="iniciar sesión con Facebook"/>Iniciar sesión
		</a>
		<a href="javascript:void(0);" class="g_connect_button">
			<img src="{$base_uri}/themes/gomarket/img/my-account/G-.png" alt="iniciar sesión con Google"/>Iniciar sesión
		</a>
	{/if}
</div>
</div>
