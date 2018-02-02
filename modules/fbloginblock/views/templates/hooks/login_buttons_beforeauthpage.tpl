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

{if ($fbloginblock_beforeauthpagef == "beforeauthpagef" && $fbloginblockf_on == 1) ||
($fbloginblock_beforeauthpaget == "beforeauthpaget" && $fbloginblockt_on == 1) ||
($fbloginblock_beforeauthpageg == "beforeauthpageg" && $fbloginblockg_on == 1) ||
($fbloginblock_beforeauthpagey == "beforeauthpagey" && $fbloginblocky_on == 1) ||
($fbloginblock_beforeauthpagel == "beforeauthpagel" && $fbloginblockl_on == 1) ||
($fbloginblock_beforeauthpagem == "beforeauthpagem" && $fbloginblockm_on == 1) ||
($fbloginblock_beforeauthpagefs == "beforeauthpagefs" && $fbloginblockfs_on == 1) ||
($fbloginblock_beforeauthpagegi == "beforeauthpagegi" && $fbloginblockgi_on == 1) ||
($fbloginblock_beforeauthpaged == "beforeauthpaged" && $fbloginblockd_on == 1) ||
($fbloginblock_beforeauthpagea == "beforeauthpagea" && $fbloginblocka_on == 1) ||
($fbloginblock_beforeauthpagedb == "beforeauthpagedb" && $fbloginblockdb_on == 1) ||
($fbloginblock_beforeauthpagew == "beforeauthpagew" && $fbloginblockw_on == 1) ||
($fbloginblock_beforeauthpagetu == "beforeauthpagetu" && $fbloginblocktu_on == 1) ||
($fbloginblock_beforeauthpagepi == "beforeauthpagepi" && $fbloginblockpi_on == 1) ||
($fbloginblock_beforeauthpagep == "beforeauthpagep" && $fbloginblockp_on == 1) ||
($fbloginblock_beforeauthpagev == "beforeauthpagev" && $fbloginblockv_on == 1)}
    {*|| $fbloginblock_beforeauthpageo == "beforeauthpageo" || $fbloginblock_beforeauthpagema == "beforeauthpagema"
    || $fbloginblock_beforeauthpageya == "beforeauthpageya" || $fbloginblock_beforeauthpagei == "beforeauthpagei"
    || $fbloginblock_beforeauthpages == "beforeauthpages" *}


    <div class="clear"></div><div id="fbloginblock-beforeauthpage" class="wrap text-align-center fbloginblock-connects margin-bottom-20">


    {if $fbloginblock_authpagetxt == "authpagetxt"}
    
        <div class="auth-page-txt-before-logins">{$fbloginblockauthp|escape:'quotes':'UTF-8'}</div>
    
    {/if}





    {foreach from=$fbloginblockallcon key=prefix_short item=prefix_full name=myLoop}
        {assign var=prefix_full value=$prefix_full.prefix}


        {if $fbloginblock_beforeauthpage{$prefix_short} == "beforeauthpage{$prefix_short}" && $fbloginblock{$prefix_short}_on == 1}

        
            <a  href="javascript:void(0)"

        {if is_int($fbloginblock{$prefix_short}beforeauthpageimg)}
            class="{$prefix_full|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-{$fbloginblock{$prefix_short}beforeauthpageimg|escape:'htmlall':'UTF-8'}"
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


        {if $fbloginblock{$prefix_short}beforeauthpageimg == 1}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}beforeauthpageimg == 2}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {elseif $fbloginblock{$prefix_short}beforeauthpageimg == 3}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}beforeauthpageimg == 4}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {else}
            <img src="{$fbloginblock{$prefix_short}beforeauthpageimg|escape:'htmlall':'UTF-8'}" class="img-top-custom" alt="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"  />
        {/if}

            </a>&nbsp;
        
        {/if}




    {/foreach}


    {if $fbloginblockiauth == 1}
    
        <div class="auth-page-txt-info-block">{$fbloginblocktxtauthp|escape:'quotes':'UTF-8'}</div>
    
    {/if}






    </div>



{/if}