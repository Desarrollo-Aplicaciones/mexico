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

{if ($fbloginblock_authpagef == "authpagef" && $fbloginblockf_on == 1) ||
($fbloginblock_authpaget == "authpaget" && $fbloginblockt_on == 1) ||
($fbloginblock_authpageg == "authpageg" && $fbloginblockg_on == 1) ||
($fbloginblock_authpagey == "authpagey" && $fbloginblocky_on == 1) ||
($fbloginblock_authpagel == "authpagel" && $fbloginblockl_on == 1) ||
($fbloginblock_authpagem == "authpagem" && $fbloginblockm_on == 1) ||
($fbloginblock_authpagefs == "authpagefs" && $fbloginblockfs_on == 1) ||
($fbloginblock_authpagegi == "authpagegi" && $fbloginblockgi_on == 1) ||
($fbloginblock_authpaged == "authpaged" && $fbloginblockd_on == 1) ||
($fbloginblock_authpagea == "authpagea" && $fbloginblocka_on == 1) ||
($fbloginblock_authpagedb == "authpagedb" && $fbloginblockdb_on == 1) ||
($fbloginblock_authpagew == "authpagew" && $fbloginblockw_on == 1) ||
($fbloginblock_authpagetu == "authpagetu" && $fbloginblocktu_on == 1) ||
($fbloginblock_authpagepi == "authpagepi" && $fbloginblockpi_on == 1) ||
($fbloginblock_authpagep == "authpagep" && $fbloginblockp_on == 1) ||
($fbloginblock_authpagev == "authpagev" && $fbloginblockv_on == 1)}
    {*|| $fbloginblock_authpageo == "authpageo" || $fbloginblock_authpagema == "authpagema" || $fbloginblock_authpageya == "authpageya" || $fbloginblock_authpagei == "authpagei" || $fbloginblock_authpages == "authpages" *}


    <div class="clear"></div><div id="fbloginblock-authpage" class="text-align-center fbloginblock-connects {if $fbloginblockis17 == 1}margin-top-20{/if}">


    {if $fbloginblock_authpagetxt == "authpagetxt"}
    
        <div class="auth-page-txt-before-logins">{$fbloginblockauthp|escape:'quotes':'UTF-8'}</div>
    
    {/if}


    {foreach from=$fbloginblockallcon key=prefix_short item=prefix_full name=myLoop}
        {assign var=prefix_full value=$prefix_full.prefix}


        {if $fbloginblock_authpage{$prefix_short} == "authpage{$prefix_short}" && $fbloginblock{$prefix_short}_on == 1}

        
            <a  href="javascript:void(0)"

        {if is_int($fbloginblock{$prefix_short}authpageimg)}
            class="{$prefix_full|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-{$fbloginblock{$prefix_short}authpageimg|escape:'htmlall':'UTF-8'}"
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


        {if $fbloginblock{$prefix_short}authpageimg == 1}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}authpageimg == 2}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {elseif $fbloginblock{$prefix_short}authpageimg == 3}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}authpageimg == 4}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {else}
            <img src="{$fbloginblock{$prefix_short}authpageimg|escape:'htmlall':'UTF-8'}" class="img-top-custom" alt="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"  />
        {/if}

            </a>&nbsp;
        
        {/if}




    {/foreach}


    {if $fbloginblockiauth == 1}
    
        <div class="auth-page-txt-info-block">{$fbloginblocktxtauthp|escape:'quotes':'UTF-8'}</div>
    
    {/if}






    </div>




{/if}
