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
</div>
<div class="more_options">
	<div class="information">
		<img src="{$img_dir}ayuda.png" class="imagenAyuda"/> 
		<p class="tituloAyuda">¿Necesitas Ayuda?</p>
		<p><br /></p>
		<p class="ciudadAyuda">Líneas de Atención y Ventas Nacional sin costo:</p>
		<p>(55) 4170.8434<br />(55) 6732.1100</p>
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
			return true;
		}
	}
	$('.edit').unbind('click').live('click', function(){
		$(this ).parent().prev().children('label').hide();
		$(this ).parent().prev().children('.hidden').show();
		$(this ).html("");

		$('.submit').slideDown();
	});
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
</script>