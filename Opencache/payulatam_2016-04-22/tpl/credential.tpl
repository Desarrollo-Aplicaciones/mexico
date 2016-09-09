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

<form action="{$formCredential|escape:'htmlall':'UTF-8'}" method="POST">
	<fieldset>
		<p>Seleccione el modo de funcionamiento del modulo.<br></p>
		<input type="hidden" name="submitPayU" value="1" />
		<label from="demo">Modo de prueba:</label>
		<div class="margin-form">
			<input type="radio" value="si" id="demoyes" name="demo" {if $credentialInputVar['demo']['value'] == 'yes'}checked="checked"{/if}>  Si
			<input type="radio" value="no" id="demono" name="demo" {if $credentialInputVar['demo']['value'] != 'yes'}checked="checked"{/if}>  No
		</div>
		<div class="margin-form">
			<input type="submit" class="button" value="{l s='Save' mod='payulatam'}" />
		</div>
	</fieldset>
	</form>