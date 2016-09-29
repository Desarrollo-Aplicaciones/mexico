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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Your personal information'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
{include file="$tpl_dir./my-account-menu.tpl"}
<h1>{l s='Your personal information'}</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation) && $confirmation}
	<p class="success">
		{l s='Your personal information has been successfully updated.'}
		{if isset($pwd_changed)}<br />{l s='Your password has been sent to your email:'} {$email}{/if}
	</p>
{/if}
<div class="user_info">
	<p>{l s='Please be sure to update your personal information if it has changed.'}</p>
	<form action="{$link->getPageLink('identity', true)|escape:'html'}" method="post">
		<table class="update">
			<tr class="data_row">
				<td><label for="email">{l s='Email'}</label></td>
				<td colspan="2"><label for="email_value">{$smarty.post.email}</label>
				<input type="hidden" name="email" id="email" value="{$smarty.post.email}" readonly="true" /></td>
			</tr>
			<tr class="radio">
				<td><label for="title">{l s='Title'}</label></td>
				<td><label for="title_value">{foreach from=$genders key=k item=gender}{if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}{l s=$gender->name}{/if}{/foreach}</label>
				<span class="hidden">
				{foreach from=$genders key=k item=gender}
					<input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id|intval}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
					{l s=$gender->name}<br />
				{/foreach}
				</span>
				</td>
				<td><a href="javascript:void(0);" class="edit" ><span>Editar</span><span class="hidden">&#x270E;</span></a></td>
			</tr>
			<tr class="data_row">
				<td><label for="firstname">{l s='First name'}</label></td>
				<td><label for="firstname_value">{$smarty.post.firstname}</label>
				<span class="hidden">
					<input type="text" id="firstname" name="firstname" value="{$smarty.post.firstname}" />
					<span class="hidden"><br />{l s='Required field'}</span>
				</span></td>
				<td><a href="javascript:void(0);" class="edit" ><span>Editar</span><span class="hidden">&#x270E;</span></a></td>
			</tr>
			<tr class="data_row">
				<td><label for="lastname">{l s='Last name'}</label></td>
				<td><label for="lastname_value">{$smarty.post.lastname}</label>
				<span class="hidden">
					<input type="text" name="lastname" id="lastname" value="{$smarty.post.lastname}" />
					<span class="hidden"><br />{l s='Required field'}</span>
				</span></td>
				<td><a href="javascript:void(0);" class="edit" ><span>Editar</span><span class="hidden">&#x270E;</span></a></td>
			</tr>
			<tr class="data_row">
				<td><label for="passwd">{l s='New Password'}</label></td>
				<td><label for="passwd_value">&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;</label>
				<span class="hidden">
					<input type="password" name="passwd" id="passwd" /><br />
					<p class="confirm">
						<input type="password" name="confirmation" id="confirmation"/><br />
						<span class="confirm">{l s='Confirmation'}</span>
					</p>
				</span></td>
				<td><a href="javascript:void(0);" class="edit" ><span>Editar</span><span class="hidden">&#x270E;</span></a></td>
			</tr>
			<tr class="select">
				<td><label>{l s='Date of Birth'}</label></td>
				<td><label for="birth_value">{$sl_day} de {l s=$months[$sl_month*1]} de {$sl_year}</label>
				<span class="hidden">
					<select name="days" id="days">
						<option value="">-</option>
						{foreach from=$days item=v}
							<option value="{$v}" {if ($sl_day == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
						{/foreach}
					</select>
					<select id="months" name="months">
						<option value="">-</option>
						{foreach from=$months key=k item=v}
							<option value="{$k}" {if ($sl_month == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
						{/foreach}
					</select>
					<select id="years" name="years">
						<option value="">-</option>
						{foreach from=$years item=v}
							<option value="{$v}" {if ($sl_year == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
						{/foreach}
					</select>
				</span></td>
				<td><a href="javascript:void(0);" class="edit" ><span>Editar</span><span class="hidden">&#x270E;</span></a></td>
			</tr>
			{if $newsletter}
			<tr class="checkbox">
				<td><label for="newsletter">{l s='Boletín'}</label></td>
				<td><label for="newsletter_value">
					{if !isset($smarty.post.newsletter) || $smarty.post.newsletter != 1}{l s='Dont'}{/if}
					{l s='Newsletter'}
				</label>
				<span class="hidden"><input type="checkbox" id="newsletter" name="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if} autocomplete="off"/>
				{l s='Sign up for our newsletter!'}</span></td>
				<td><a href="javascript:void(0);" class="edit" ><span>Editar</span><span class="hidden">&#x270E;</span></a></td>
			</tr>
			<tr class="checkbox">
				<td><label for="newsletter">{l s='Offers'}</label></td>
				<td><label for="offers_value">
					{if !isset($smarty.post.optin) || $smarty.post.optin != 1}{l s='Dont'}{/if}
					{l s='subscribed'}
				</label>
				<span class="hidden"><input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if} autocomplete="off"/>
				{l s='Receive special offers from our partners!'}</span></td>
				<td><a href="javascript:void(0);" class="edit" ><span>Editar</span><span class="hidden">&#x270E;</span></a></td>
			</tr>
			{/if}
                </table>
			<div class="save_changes">
				<input type="password" name="old_passwd" id="old_passwd" /><br />
				<label for="old_passwd">{l s='Current Password'}</label>
			</div>
			<div class="submit">
				<p class="required"></p>
				<input type="submit" class="button" name="submitIdentity" id="submitIdentity" value="{l s='Save'}" />
			</div>
	</form>
        {if isset($set_prog_apego) && $set_prog_apego}
            {foreach item=obj from=$prog_apego}
                <div class="programas_apego">
                    <div class="line"><b>{$obj['name_apego']}</b></div>
                    <div class="line">
                        {if isset($obj['access_value']) && !empty($obj['access_value'])}
                            {$obj['access_value']}
                        {else}
                            <span class="comment-value-access">Ingresa tu tarjeta</span>
                        {/if}
                        <input type="text" name="pa_{$obj['name_apego']}" id="pa_{$obj['name_apego']}" class="input-access-value" autocomplete="off" style="display: none;"/>
                    </div>
                    {if $obj['access_value'] == false}
                        <span class="float line btn-access-value-edit">Editar</span>
                        <span class="float line btn-access-value-save" style="display: none;" name="{$obj['name_apego']}" id="{$obj['name_apego']}">Guardar</span>
                    {/if}
                </div>                     
            {/foreach}
        {/if}                
</div>
                        
<div class="more_options">
	<div class="identity_title">
		Datos de facturación
	</div>
	<div>
	<div class="rfc_container">
		{if !isset($rfc.dni)}
		<div class="hide_rfc">
			<a id="rfc_register" href="javascript:void(0)">Deseo ingresar mis datos de facturación</a>
		</div>
		{/if}
		<table>
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
		{if !isset($rfc.dni)}<a id="rfc_save" href="javascript:void(0)">Guardar</a>{/if}
	</div>
	<div class="information">
		<img src="{$img_dir}ayuda.png" class="imagenAyuda"/> 
		<p class="tituloAyuda">¿Necesitas Ayuda?</p>
		<p><br /></p>
		<p class="ciudadAyuda">Líneas de Atención y Ventas Nacional sin costo:</p>
		<p>(55) 6732.1100</p>
		<p> contacto@farmalisto.com.mx</p>
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
	$('.edit').unbind('click').live('click', function(){
		$(this ).parent().prev().children('label').hide();
		$(this ).parent().prev().children('.hidden').show();
		$(this ).html("");

		$('.submit').slideDown();
	});
	function isCodPostal(elem){
		if( isNaN($(elem).val()) || $(elem).val().length < 4 || $(elem).val().length > 5 ){
			$(elem).next('span').slideDown();
			$(elem).css('border-color', '#A5689C')
			return false;
		}else{
			$(elem).next('span').slideUp();
			$(elem).css('border-color', '#3a9b37')
			return true;
		}
	}
	function isTelefono(elem){
		if( isNaN($(elem).val()) || $(elem).val().length != 10){
			$(elem).next('span').slideDown();
			$(elem).css('border-color', '#A5689C')
			return false;
		}else{
			$(elem).next('span').slideUp();
			$(elem).css('border-color', '#3a9b37')
			return true;
		}
	}
	$('#firstname').keyup(function(){
		requerir(this);
	});
	$('#lastname').keyup(function(){
		requerir(this);
	});
	$('#passwd').keyup(function(){
		$('.confirm').slideDown();
	});
	$('#submitIdentity').unbind('click').live('click', function(){
		$('.save_changes').slideDown();
		if ($('#old_passwd').val().length > 5 && requerir($('#firstname')) && requerir($('#lastname'))){
			return true;
		}else{
			return false;
		}
	});
	$('#rfc_register').unbind('click').live('click', function(){
		$('.hide_rfc').fadeOut();
	});
	$('#rfc').focusout(function(){
		ValidaRfc(this.value);
	});
	{literal}
	function ValidaRfc(rfcStr) {
		var strCorrecta;
		strCorrecta = rfcStr;	
		if (rfcStr.length == 12){
		var valid = '^(([A-Z]|[a-z]){3})([0-9]{6})((([A-Z]|[a-z]|[0-9]){3}))';
		}else{
		var valid = '^(([A-Z]|[a-z]|\s){1})(([A-Z]|[a-z]){3})([0-9]{6})((([A-Z]|[a-z]|[0-9]){3}))';
		}
		var validRfc=new RegExp(valid);
		var matchArray=strCorrecta.match(validRfc);
		if (matchArray==null) {
			$('#rfc').next('span').slideDown();
			$('#rfc').css('border-color','#a5689c');
			return false;
		}
		else
		{
			$('#rfc').next('span').slideUp();
			$('#rfc').css('border-color','#3a9b37');
			return true;
		}
		
	}
	function asignarPostcode(){
	var valor = $('#postcode').val();
	if ( valor.length == 5) {
		$.ajax({
			type: "GET",
			url: "ajaxs/postcode.php?postcode="+valor,
			success: function(isApplicable){
				if ( isApplicable != 0) {
					vals=isApplicable.split(";");
					//alert(vals[0] + '-' + vals[1] + '-' + vals[2]);

					$('#estado').val(vals[2]);

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
							$('#ciudad').html(response.results);

								var city = vals[1];
								$('#ciudad').val(vals[1]);
								$('#id_city').val(vals[1]);

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
										$('#id_colonia').html(response.results);
										$('#ciudad').val(vals[1]);
										$('#id_colonia').val(vals[0]);												
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

					$('#ciudad').val(vals[1]);
					$('#id_colonia').val(vals[0]);
					
				}
				else{
					$('#postcode').val("");
				}
			}
		});
	}
}
	{/literal}
	function enviar(){
            if(requerir('#rfc_name') && requerir('#rfc_address') && isCodPostal('#postcode') && ValidaRfc($('#rfc').val()) && isTelefono('#rfc_phone')){
                var id_customer={$cart->id_customer};
                var alias=$('#rfc_name').val();
                var address1=$('#rfc_address').val();
                var postcode = $('#postcode').val();
                var id_state = $('#estado').val();
                var id_city = $('#id_city').val();
                var ciudad = $( "#ciudad option:selected" ).text();
                var id_colonia = $('#id_colonia').val();
                var phone=$('#rfc_phone').val();
                var rfc= $('#rfc').val();
                var is_rfc = 1;
                $.ajax({
                        type:"post",
                        url:"{$base_dir}ajax_address_order.php",
                        data:{
                                "id_customer":id_customer,
                                "alias":alias,
                                "address1":address1,
                                "postcode":postcode,
                                "city_id":id_city,
                                "id_state":id_state,
                                "city":ciudad,
                                "id_colonia": id_colonia,
                                "phone":phone,
                                "rfc":rfc,
                                "is_rfc":is_rfc,
                        },
                        beforeSend: function(ev) {
                                //beforeSend
                        },
                        success: function(response){
                                location.reload();
                        }
                });
            }
            return false;
	}
        
        
    function ajaxAddProgramaApego( programa ) {
        
        var access_value = $('#pa_' + programa).val();       
        console.log('val acceso:' + access_value);
        
        $.post( "{$base_dir}ajaxs/ajax_programa_apego.php", { nombre_apego: programa, access_value: access_value })
            .done(function( data ) {
                console.log("Respuesta del ajax:   "+data);
                var jsonObject = JSON.parse(data);
                location.reload();
            }, "json");
    }

    $('#postcode').focusout(function() {
            asignarPostcode();
    });
    
    $('#rfc_save').click(function(){
            enviar();
    });
    
    // Editar Inputs programa apego
    $('.btn-access-value-edit').click(function(){
        // Reinicia configuración inicial
        $(".programas_apego").find(".btn-access-value-edit, .comment-value-access").show();
        $(".programas_apego").find('.btn-access-value-save, .input-access-value').hide();

        $this = $(this);
        $thisProgramaApego = $this.parent();
        $this.hide();
        $thisProgramaApego.find(".comment-value-access").hide();
        $thisProgramaApego.find('.btn-access-value-save, .input-access-value').show();
    });
    
    // Envia ajax
    $('.btn-access-value-save').click(function() {
        
        var id = $(this).attr('id');

        console.log( "id: "+id ) ;
        //$thisProgramaApego = $(this).parent();
        //if ( $thisProgramaApego.find('.input-access-value').val() ) {
            ajaxAddProgramaApego( id );
        //}
    });
</script>

