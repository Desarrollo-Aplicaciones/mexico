{*
 *
 * 2011 - 2017 StorePrestaModules SPM LLC.
 *
 * MODULE fbloginblock
 *
 * @author    SPM <kykyryzopresto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   1.7.7
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=61669
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 *
*}

<h4><img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/settings_{$fbloginblockstprefix|escape:'htmlall':'UTF-8'}.png"/>&nbsp;{$fbloginblockapi_one_1|escape:'htmlall':'UTF-8'} {$fbloginblockstname|escape:'htmlall':'UTF-8'} {$fbloginblockapi_one_2|escape:'htmlall':'UTF-8'}</h4>
<br/>
<p class="p-txt-fbloginblock">{$fbloginblockapi_two1|escape:'htmlall':'UTF-8'} {$fbloginblockstname|escape:'htmlall':'UTF-8'} {$fbloginblockapi_two2|escape:'htmlall':'UTF-8'} </p>
<br/>
<label for="api-email">{l s='Your email' mod='fbloginblock'}</label>&nbsp;<input type="text" {if $fbloginblockis17 == 1}class="form-control17-fbloginblock"{/if} value="" id="api-email" name="api-email">
<br/>
<br/>
<a class="{if $fbloginblockis17 == 1}btn btn-danger{/if} button margin-0-auto" onclick="setCookiePopupBlock_fbloginblock(\'popup-{$fbloginblockstprefix|escape:'htmlall':'UTF-8'}\',\'hidden\');return false;" value="{l s='Do not show again' mod='fbloginblock'}"><b>{l s='Do not show again' mod='fbloginblock'}</b></a>
&nbsp;
<a class="{if $fbloginblockis17 == 1}btn btn-primary{/if} button margin-0-auto" onclick="update_social_api_email();return false;" value="{l s='Send ' mod='fbloginblock'}"><b>{l s='Send ' mod='fbloginblock'}</b></a>

