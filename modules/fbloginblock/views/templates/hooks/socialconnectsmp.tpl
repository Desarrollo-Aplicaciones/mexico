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

{if !$fbloginblockislogged}
<span id="socialConnectSpm" class="fbloginblock-connects">



    {foreach from=$fbloginblockallcon key=prefix_short item=prefix_full name=myLoop}

        {assign var=prefix_full value=$prefix_full.prefix}


        {if $fbloginblock_chook{$prefix_short} == "chook{$prefix_short}" && $fbloginblock{$prefix_short}_on == 1}

            <a  href="javascript:void(0)"

                    {if is_int($fbloginblock{$prefix_short}chookimg)}
                        class="{$prefix_full|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-{$fbloginblock{$prefix_short}chookimg|escape:'htmlall':'UTF-8'}"
                    {/if}

                    {if $prefix_short == "a"}
                        onclick="return amazonlogin();" title="Amazon"
                    {elseif $prefix_short == "y"}
                        onclick="javascript:popupWin = window.open('{$fbloginblockredurly|escape:'htmlall':'UTF-8'}', 'openId', 'location,width=600,height=600,top=0');popupWin.focus();"
                    {else}

                        {if isset($fbloginblockis_ssl{$prefix_short}) && $fbloginblockis_ssl{$prefix_short} == 0}
                            onclick="alert('{$fbloginblockssltxt{$prefix_short}|escape:'htmlall':'UTF-8'}')"
                        {elseif $fbloginblock{$prefix_short}conf == 1}
                            onclick="javascript:popupWin = window.open('{$fbloginblockredurl{$prefix_short}|escape:'htmlall':'UTF-8'}', 'login', 'location,width=600,height=600,top=0'); popupWin.focus();"
                        {else}
                            onclick="alert('{${$prefix_short}error|escape:'htmlall':'UTF-8'}')"
                        {/if}
                        title="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"

                    {/if}
                    >


                {if $fbloginblock{$prefix_short}chookimg == 1}
                    <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
                {elseif $fbloginblock{$prefix_short}chookimg == 2}
                    <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
                {elseif $fbloginblock{$prefix_short}chookimg == 3}
                    <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
                {elseif $fbloginblock{$prefix_short}chookimg == 4}
                    <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
                {else}
                    <img src="{$fbloginblock{$prefix_short}chookimg|escape:'htmlall':'UTF-8'}" class="img-top-custom" alt="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"  />
                {/if}

            </a>&nbsp;

        {/if}




    {/foreach}


</span>

{/if}




