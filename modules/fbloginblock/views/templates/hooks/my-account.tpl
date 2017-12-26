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
    {if $fbloginblockis17 == 0}
    <li>
    {/if}

	
	<a href="{$fbloginblockaccount_url|escape:'htmlall':'UTF-8'}" {if $fbloginblockis17 == 1}class="col-lg-4 col-md-6 col-sm-6"{/if}
	   title="{l s='Social account linking' mod='fbloginblock'}">

       {if $fbloginblockis17 == 1}<span class="link-item">{/if}


	   {if $fbloginblockis16 == 1}<i {if $fbloginblockis17 == 1}class="material-icons"{/if} >{/if}
            <img class="icon" src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/link.png" />
	   {if $fbloginblockis16 == 1}</i>{/if}




	    {if $fbloginblockis16 == 1 && $fbloginblockis17 == 0}<span>{/if}
	   	    {l s='Social account linking' mod='fbloginblock'}
	   	{if $fbloginblockis16 == 1 && $fbloginblockis17 == 0}</span>{/if}


        {if $fbloginblockis17 == 1}</span>{/if}

	   	</a>


    {if $fbloginblockis17 == 0}
    </li>
    {/if}

{/if}
{/if}

