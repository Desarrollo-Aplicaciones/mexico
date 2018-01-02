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

{if $fbloginblockis16 == 1 && $fbloginblockis17 ==0}
    {capture name=path}<a href="{$fbloginblockmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='fbloginblock'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{l s='Social Account Linking' mod='fbloginblock'}{/capture}
{/if}

{if $fbloginblockis17 == 1}
    <a href="{$fbloginblockmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='fbloginblock'}</a>
    <span class="navigation-pipe"> > </span>{l s='Social Account Linking' mod='fbloginblock'}
{/if}

{if $fbloginblockis16 == 1}
    <h3 class="page-product-heading">{l s='Social Account Linking' mod='fbloginblock'}</h3>

{else}
    <div class="breadcrumb">
    <a href="{$fbloginblockmy_account|escape:'htmlall':'UTF-8'}"><img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/users/home.gif" class="icon" />{l s='My account' mod='fbloginblock'}</a>
    <span class="navigation-pipe"> > </span>{l s='Social Account Linking' mod='fbloginblock'}
    </div>

{/if}




<p>{l s='Here you will connect your social accounts to login easily in our shop.' mod='fbloginblock'}</p>


{if $fbloginblockis_linked == 'del'}
<p class="alert alert-success">&nbsp;{l s='Account connection deleted successfully.' mod='fbloginblock'}</p>
{/if}

{if $fbloginblockis_linked == 'link'}
<p class="alert alert-success">&nbsp;{l s='Customer connected successfully.' mod='fbloginblock'}</p>
{/if}

    <div class="panel panel-default">
        <table class="table {if $fbloginblockis16 == 0}std{/if}" >
            <thead>
            <tr>
                <th class="width-30-perc">{l s='Network'  mod='fbloginblock'}</th>
                <th >{l s='Action'  mod='fbloginblock'}</th>
            </tr>
            </thead>
            <tbody>


            {foreach from=$fbloginblockallcon key=prefix_short item=prefix_full name=myLoop}


                {if $fbloginblock{$prefix_short}_on == 1}
                <tr>
                <td>


                    <div class="fbloginblock-connects">
                    {assign var=prefix_full_custom value=$prefix_full.prefix}

                        <a class="{$prefix_full_custom|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-1">
                            <i class="fa fa-{$prefix_full_custom|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full_custom|escape:'htmlall':'UTF-8'|capitalize}</i>
                        </a>&nbsp;

                    </div>

                </td>

                    <td>
                        {if $prefix_short == "y"}

                            {if isset($fbloginblockdata_linked[$prefix_full.type])}
                                <a class="btn-custom-fbloginblock btn-danger-custom-fbloginblock"
                                           onclick="is_confirm_disconnect = confirm('{l s='Confirm that you want to disconnect' mod='fbloginblock'} {$prefix_full_custom|escape:'htmlall':'UTF-8'|capitalize} {l s='from your account' mod='fbloginblock'}'); if(is_confirm_disconnect) { window.location.href = '{$fbloginblockdelete_url|escape:'htmlall':'UTF-8'}?type={$prefix_full.type|escape:'htmlall':'UTF-8'}'; return true;} else { return false; } "
                                        >
                                    <i class="icon-remove"></i> {l s='Disconnect' mod='fbloginblock'}
                                </a>
                            {else}

                                <a href="javascript:void(0)" title="Yahoo" class="btn-custom-fbloginblock btn-success-custom-fbloginblock"
                                   onclick="is_confirm_connect = confirm('{l s='Confirm that you want connect' mod='fbloginblock'} {$prefix_full_custom|escape:'htmlall':'UTF-8'|capitalize} {l s='to your account' mod='fbloginblock'}'); if(is_confirm_connect) { javascript:popupWin = window.open('{$fbloginblockredurly|escape:'htmlall':'UTF-8'}&linksocialaccount=1', 'openId', 'location,width=600,height=600,top=0');popupWin.focus(); return true;} else { return false; } "
                                        >
                                    <i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp;{l s='Connect and link social account' mod='fbloginblock'}
                                </a>
                            {/if}

                        {elseif $prefix_short == "a"}
                            {if isset($fbloginblockdata_linked[$prefix_full.type])}

                                <a class="btn-custom-fbloginblock btn-danger-custom-fbloginblock"

                                   onclick="is_confirm_disconnect = confirm('{l s='Confirm that you want to disconnect' mod='fbloginblock'} {$prefix_full_custom|escape:'htmlall':'UTF-8'|capitalize} {l s='from your account' mod='fbloginblock'}'); if(is_confirm_disconnect) { window.location.href = '{$fbloginblockdelete_url|escape:'htmlall':'UTF-8'}?type={$prefix_full.type|escape:'htmlall':'UTF-8'}'; return true;} else { return false; } "

                                        >
                                    <i class="icon-remove"></i> {l s='Disconnect' mod='fbloginblock'}
                                </a>



                            {else}

                                <a title="{l s='Connect and link social account' mod='fbloginblock'}" class="btn-custom-fbloginblock btn-success-custom-fbloginblock"
                                   href="javascript:void(0)"

                                   onclick="is_confirm_connect = confirm('{l s='Confirm that you want connect' mod='fbloginblock'} {$prefix_full_custom|escape:'htmlall':'UTF-8'|capitalize} {l s='to your account' mod='fbloginblock'}'); if(is_confirm_connect) { return amazonlogin();} else { return false; } "

                                        >
                                    <i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp;{l s='Connect and link social account' mod='fbloginblock'}
                                </a>

                                {literal}
                                <script type="text/javascript">
                                    {/literal}
                                    {if $fbloginblockamazonci != '' && $fbloginblockis_ssl == 1}
                                    {literal}

                                    // amazon connect

                                    document.addEventListener("DOMContentLoaded", function(event) {
                                    $(document).ready(function(){

                                        //add div amazon-root
                                        if ($('div#amazon-root').length == 0)
                                        {
                                            FBRootDomAmazon = $('<div>', {'id':'amazon-root'});
                                            $('body').prepend(FBRootDomAmazon);
                                        }

                                        window.onAmazonLoginReady = function() {
                                            amazon.Login.setClientId('{/literal}{$fbloginblockamazonci|escape:'htmlall':'UTF-8'}{literal}');
                                        };
                                        (function(d) {
                                            var a = d.createElement('script'); a.type = 'text/javascript';
                                            a.async = true; a.id = 'amazon-login-sdk';
                                            a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
                                            d.getElementById('amazon-root').appendChild(a);
                                        })(document);


                                    });
                                    });

                                    function amazonlogin(){
                                        options = { scope : 'profile' };
                                        amazon.Login.authorize(options, '{/literal}{$fbloginblockamazon_url nofilter}{literal}?linksocialaccount=1');
                                        return false;
                                    }


                                    // amazon connect

                                    {/literal}
                                    {else}
                                    {literal}

                                    function amazonlogin(){

                                        {/literal}{if $fbloginblockis_ssl == 0}{literal}
                                        alert("{/literal}{$fbloginblockssltxt|escape:'htmlall':'UTF-8'}{literal}");
                                        {/literal}{else}{literal}
                                        alert("{/literal}{$aerror|escape:'htmlall':'UTF-8'}{literal}");
                                        {/literal}{/if}{literal}
                                        return;

                                    }

                                    {/literal}
                                    {/if}
                                    {literal}
                                </script>
                                {/literal}

                            {/if}
                        {else}

                            {if isset($fbloginblockdata_linked[$prefix_full.type])}
                                <a class="btn-custom-fbloginblock btn-danger-custom-fbloginblock"

                                    onclick="is_confirm_disconnect = confirm('{l s='Confirm that you want to disconnect' mod='fbloginblock'} {$prefix_full_custom|escape:'htmlall':'UTF-8'|capitalize} {l s='from your account' mod='fbloginblock'}'); if(is_confirm_disconnect) { window.location.href = '{$fbloginblockdelete_url|escape:'htmlall':'UTF-8'}?type={$prefix_full.type|escape:'htmlall':'UTF-8'}'; return true;} else { return false; } "

                                        >
                                    <i class="icon-remove"></i> {l s='Disconnect' mod='fbloginblock'}
                                </a>
                            {else}

                                <a class="btn-custom-fbloginblock btn-success-custom-fbloginblock" href="javascript:void(0)"
                                        {if isset($fbloginblockis_ssl{$prefix_short}) && $fbloginblockis_ssl{$prefix_short} == 0}
                                            onclick="alert('{$fbloginblockssltxt{$prefix_short}|escape:'htmlall':'UTF-8'}')"
                                        {elseif $fbloginblock{$prefix_short}conf == 1}
                                            onclick="is_confirm_connect = confirm('{l s='Confirm that you want connect' mod='fbloginblock'} {$prefix_full_custom|escape:'htmlall':'UTF-8'|capitalize} {l s='to your account' mod='fbloginblock'}'); if(is_confirm_connect) { javascript:popupWin = window.open('{$fbloginblockredurl{$prefix_short}|escape:'htmlall':'UTF-8'}{if $fbloginblockis16 == 1}&{else}?{/if}linksocialaccount=1', 'login', 'location,width=600,height=600,top=0'); popupWin.focus(); return true;} else { return false; } "
                                        {else}
                                            onclick="alert('{${$prefix_short}error|escape:'htmlall':'UTF-8'}')"
                                        {/if}
                                   title="{$prefix_full.prefix|escape:'htmlall':'UTF-8'|capitalize}">
                                    <i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp;{l s='Connect and link social account' mod='fbloginblock'}

                                </a>
                            {/if}

                        {/if}

                        {if strlen($prefix_full.link)>0}
                            <a class="btn-custom-fbloginblock {if $fbloginblockis17 == 1}btn-custom-fbloginblock17{/if} btn-link" href="{$prefix_full.link|escape:'htmlall':'UTF-8'}" title="{l s='Configure' mod='fbloginblock'} {$prefix_full.prefix|escape:'htmlall':'UTF-8'|capitalize}" target="_blank">
                                <i class="fa fa-cogs"></i>&nbsp;{l s='Configure' mod='fbloginblock'} {$prefix_full.prefix|escape:'htmlall':'UTF-8'|capitalize}
                            </a>
                        {/if}
                    </td>
                </tr>
                {/if}
            {/foreach}



            </tbody>
        </table>
    </div>
    <p>&nbsp;</p>
    <p class="alert-custom-fbloginblock alert-warning-custom-fbloginblock">
        {l s='You can connect your social accounts, but if you have your email in other registered customer account you will login in the other account. Remember that duplicated account is prohibited in our terms and conditions.' mod='fbloginblock'}
    </p>





{if $fbloginblockis16 == 1}
    <br/>
    <ul class="footer_links clearfix">
        <li class="float-left margin-right-10">
            <a href="{$fbloginblockmy_account|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-fbloginblock">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='fbloginblock'}
			</span>
            </a>
        </li>
        <li class="float-left">
            <a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-fbloginblock">
                <span><i class="icon-chevron-left"></i> {l s='Home' mod='fbloginblock'}</span>
            </a>
        </li>
    </ul>
{else}
    <ul class="footer_links">
        <li><a href="{$fbloginblockmy_account|escape:'htmlall':'UTF-8'}"><img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/users/my-account.gif"  class="icon" /></a>
            <a href="{$fbloginblockmy_account|escape:'htmlall':'UTF-8'}">{l s='Back to Your Account' mod='fbloginblock'}</a></li>
        <li><a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}"><img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/users/home.gif" class="icon" /></a>
            <a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}">{l s='Home' mod='fbloginblock'}</a></li>
    </ul>
{/if}

