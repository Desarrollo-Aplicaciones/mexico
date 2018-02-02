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

{if ($fbloginblock_footerf == "footerf" && $fbloginblockf_on == 1) ||
($fbloginblock_footerg == "footerg" && $fbloginblockg_on == 1) ||
($fbloginblock_footert == "footert" && $fbloginblockt_on == 1) ||
($fbloginblock_footerl == "footerl" && $fbloginblockl_on == 1) ||
($fbloginblock_footerm == "footerm" && $fbloginblockm_on == 1) ||
($fbloginblock_footery == "footery" && $fbloginblocky_on == 1) ||
($fbloginblock_footerfs == "footerfs" && $fbloginblockfs_on == 1) ||
($fbloginblock_footergi == "footergi" && $fbloginblockgi_on == 1) ||
($fbloginblock_footerd == "footerd" && $fbloginblockd_on == 1) ||
($fbloginblock_footera == "footera" && $fbloginblocka_on == 1) ||
($fbloginblock_footerdb == "footerdb" && $fbloginblockdb_on == 1) ||
($fbloginblock_footerw == "footerw" && $fbloginblockw_on == 1) ||
($fbloginblock_footertu == "footertu"  && $fbloginblocktu_on == 1) ||
($fbloginblock_footerpi == "footerpi" && $fbloginblockpi_on == 1) ||
($fbloginblock_footerp == "footerp" && $fbloginblockp_on == 1) ||
($fbloginblock_footerv == "footerv" && $fbloginblockv_on == 1)}
    {*|| $fbloginblock_footeri == "footeri" || $fbloginblock_footero == "footero" || $fbloginblock_footerma == "footerma" || $fbloginblock_footerya == "footerya" || $fbloginblock_footers == "footers"*}


<div id="follow-teaser-footer">
    <div class="wrap fbloginblock-connects">

    {if $fbloginblock_footertxt == "footertxt"}
        <div class="auth-page-txt-before-logins padding-top-10">{$fbloginblockauthp|escape:'quotes':'UTF-8'}</div>
    {/if}

    {foreach from=$fbloginblockallcon key=prefix_short item=prefix_full name=myLoop}
        {assign var=prefix_full value=$prefix_full.prefix}


        {if $fbloginblock_footer{$prefix_short} == "footer{$prefix_short}" && $fbloginblock{$prefix_short}_on == 1}

        
            <a  href="javascript:void(0)"

        {if is_int($fbloginblock{$prefix_short}footerimg)}
            class="{$prefix_full|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-{$fbloginblock{$prefix_short}footerimg|escape:'htmlall':'UTF-8'}"
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


        {if $fbloginblock{$prefix_short}footerimg == 1}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}footerimg == 2}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {elseif $fbloginblock{$prefix_short}footerimg == 3}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
        {elseif $fbloginblock{$prefix_short}footerimg == 4}
            <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
        {else}
            <img src="{$fbloginblock{$prefix_short}footerimg|escape:'htmlall':'UTF-8'}" class="img-top-custom" alt="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"  />
        {/if}

            </a>&nbsp;
        
        {/if}




    {/foreach}



    </div>
           </div>



{/if}