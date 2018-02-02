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

{if ($fbloginblock_welcomef == "welcomef" && $fbloginblockf_on == 1) ||
($fbloginblock_welcomet == "welcomet" && $fbloginblockt_on == 1) ||
($fbloginblock_welcomeg == "welcomeg" && $fbloginblockg_on == 1) ||
($fbloginblock_welcomey == "welcomey" && $fbloginblocky_on == 1) ||
($fbloginblock_welcomel == "welcomel" && $fbloginblockl_on == 1) ||
($fbloginblock_welcomem == "welcomem" && $fbloginblockm_on == 1) ||
($fbloginblock_welcomefs == "welcomefs" && $fbloginblockfs_on == 1) ||
($fbloginblock_welcomegi == "welcomegi" && $fbloginblockgi_on == 1) ||
($fbloginblock_welcomed == "welcomed" && $fbloginblockd_on == 1) ||
($fbloginblock_welcomea == "welcomea" && $fbloginblocka_on == 1) ||
($fbloginblock_welcomedb == "welcomedb" && $fbloginblockdb_on == 1) ||
($fbloginblock_welcomew == "welcomew" && $fbloginblockw_on == 1) ||
($fbloginblock_welcometu == "welcometu" && $fbloginblocktu_on == 1) ||
($fbloginblock_welcomepi == "welcomepi" && $fbloginblockpi_on == 1) ||
($fbloginblock_welcomep == "welcomep" && $fbloginblockp_on == 1) ||
($fbloginblock_welcomev == "welcomev" && $fbloginblockv_on == 1)}
    {*|| $fbloginblock_welcomeo == "welcomeo" || $fbloginblock_welcomema == "welcomema" || $fbloginblock_welcomeya == "welcomeya" || $fbloginblock_welcomei == "welcomei" || $fbloginblock_welcomes == "welcomes"*}


    &nbsp;<span class="fbloginblock-connects" id="fbloginblock-welcome">


    {foreach from=$fbloginblockallcon key=prefix_short item=prefix_full name=myLoop}
        {assign var=prefix_full value=$prefix_full.prefix}


        {if $fbloginblock_welcome{$prefix_short} == "welcome{$prefix_short}" && $fbloginblock{$prefix_short}_on == 1}

        
            <a  href="javascript:void(0)"

        {if is_int($fbloginblock{$prefix_short}welcomeimg)}
            class="{$prefix_full|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-{$fbloginblock{$prefix_short}welcomeimg|escape:'htmlall':'UTF-8'}"
        {/if}

        {if $prefix_short == "a"}
             onclick="return amazonlogin();" title="Amazon"
        {elseif $prefix_short == "y"}
             onclick="javascript:popupWin = window.open(\'{$fbloginblockredurly|escape:'htmlall':'UTF-8'}\', \'openId\', \'location,width=600,height=600,top=0\');popupWin.focus();"
        {else}

        {if isset($fbloginblockis_ssl{$prefix_short}) && $fbloginblockis_ssl{$prefix_short} == 0}
            onclick="alert(\'{$fbloginblockssltxt{$prefix_short}|escape:'htmlall':'UTF-8'}\')"
        {elseif $fbloginblock{$prefix_short}conf == 1}
            onclick="javascript:popupWin = window.open(\'{$fbloginblockredurl{$prefix_short}|escape:'htmlall':'UTF-8'}\', \'login\', \'location,width=600,height=600,top=0\'); popupWin.focus();"
        {else}
            onclick="alert(\'{${$prefix_short}error|escape:'htmlall':'UTF-8'}\')"
        {/if}
            title="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"

        {/if}
            >


        {if $fbloginblock{$prefix_short}welcomeimg == 1}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}welcomeimg == 2}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {elseif $fbloginblock{$prefix_short}welcomeimg == 3}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}welcomeimg == 4}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {else}
            <img src="{$fbloginblock{$prefix_short}welcomeimg|escape:'htmlall':'UTF-8'}" class="img-top-custom" alt="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"  />
        {/if}

            </a>&nbsp;
        
        {/if}




    {/foreach}


    </span>




{/if}