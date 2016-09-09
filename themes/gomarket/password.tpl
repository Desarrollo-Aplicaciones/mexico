{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}<a href="{$link->getPageLink('authentication', true)}" title="{l s='Authentication'}" rel="nofollow">{l s='Authentication'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Forgot your password'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Forgot your password?'}</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation) && $confirmation == 1}
<p class="success">Su contrase&ntilde;a ha sido reiniciada con &eacute;xito y una confirmaci&oacute;n se ha enviado a su direcci&oacute;n de correo electr&oacute;nico</p>
{elseif isset($confirmation) && $confirmation == 2}
<p class="success">La confirmaci&oacute;n de correo electr&oacute;nico ha sido enviada a la direcci&oacute;n: {$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}</p>
{else}
<p>Por favor ingresa la direcci&oacute;n de correo que registraste al momento de crear tu cuenta. Te enviaremos tu nueva contrase&ntilde;a a esa direcci&oacute;n.</p>
<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std" id="form_forgotpassword">
	<fieldset>
		<p class="text">
			<label for="email">{l s='E-mail:'}</label>
			<input type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
		</p>
		<p class="submit">
			<input type="submit" class="button" value="{l s='Retrieve Password'}" />
		</p>
	</fieldset>
</form>
{/if}
<p class="clear">
	<a href="{$link->getPageLink('authentication', true)}" title="{l s='Return to Login'}" rel="nofollow"><img src="{$img_dir}icon/my-account.gif" alt="{l s='Return to Login'}" class="icon" /></a><a href="{$link->getPageLink('authentication')}" title="{l s='Back to Login'}" rel="nofollow">{l s='Back to Login'}</a>
</p>
