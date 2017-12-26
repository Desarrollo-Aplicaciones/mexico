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

{if $fbloginblockislogged !=0}
{if $fbloginblockis_soc_link == 1}
<li>

	<a href="{$fbloginblockaccount_url|escape:'htmlall':'UTF-8'}"
	   title="{l s='Social account linking' mod='fbloginblock'}" rel="nofollow">
        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/link.png" alt="{l s='Product Reviews' mod='fbloginblock'}" />
       	{l s='Social account linking' mod='fbloginblock'}
	   	</a>
</li>
{/if}
{/if}

