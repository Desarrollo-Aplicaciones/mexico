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

{if $fbloginblock_rcblock == "rcblock"}

{if ($fbloginblock_rightcolumnf == "rightcolumnf" && $fbloginblockf_on == 1) ||
    ($fbloginblock_rightcolumng == "rightcolumng" && $fbloginblockg_on == 1) ||
    ($fbloginblock_rightcolumnt == "rightcolumnt" && $fbloginblockt_on == 1) ||
    ($fbloginblock_rightcolumny == "rightcolumny" && $fbloginblocky_on == 1) ||
    ($fbloginblock_rightcolumnl == "rightcolumnl" && $fbloginblockl_on == 1) ||
    ($fbloginblock_rightcolumnm == "rightcolumnm" && $fbloginblockm_on == 1) ||
    ($fbloginblock_rightcolumnfs == "rightcolumnfs" && $fbloginblockfs_on == 1) ||
    ($fbloginblock_rightcolumngi == "rightcolumngi" && $fbloginblockgi_on == 1) ||
    ($fbloginblock_rightcolumnd == "rightcolumnd" && $fbloginblockd_on == 1) ||
    ($fbloginblock_rightcolumna == "rightcolumna" && $fbloginblocka_on == 1) ||
    ($fbloginblock_rightcolumndb == "rightcolumndb" && $fbloginblockdb_on == 1) ||
    ($fbloginblock_rightcolumnw == "rightcolumnw" && $fbloginblockw_on == 1) ||
    ($fbloginblock_rightcolumntu == "rightcolumntu" && $fbloginblocktu_on == 1) ||
    ($fbloginblock_rightcolumnpi == "rightcolumnpi" && $fbloginblockpi_on == 1) ||
    ($fbloginblock_rightcolumnp == "rightcolumnp" && $fbloginblockp_on == 1) ||
    ($fbloginblock_rightcolumnv == "rightcolumnv" && $fbloginblockv_on == 1)}

{*|| $fbloginblock_rightcolumni == "rightcolumni" || $fbloginblock_rightcolumno == "rightcolumno" || $fbloginblock_rightcolumnma == "rightcolumnma" || $fbloginblock_rightcolumnya == "rightcolumnya" || $fbloginblock_rightcolumns == "rightcolumns"*}

{if !$cookie->isLogged()}

<div class="block">
		<h4 class="text-align-left">{l s='Your account' mod='fbloginblock'}</h4>
<form action="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}{if $fbloginblockis15 == 1}login{else}authentication{/if}{else}authentication.php{/if}" method="post">
		<fieldset class="block_content block-fieldset-custom14">
			<p class="text">
				<br/>
				<label for="email"><b>{l s='E-mail:' mod='fbloginblock'}</b></label>
				<span><input type="text" id="email" name="email" 
							 value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" 
							 class="account_input width-14-em"/>
				</span>
			</p>
			<p class="text">
				<br/>
				<label for="passwd"><b>{l s='Password:' mod='fbloginblock'}</b></label>
				<span><input type="password" id="passwd" name="passwd" 
							 value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|escape:'htmlall':'UTF-8'|stripslashes}{/if}" 
							 class="account_input width-14-em"/>
				</span>
			</p>
			<p class="submit">
				{if isset($back)}
					<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />
				{/if}
				<div class="fbtwgblock-columns fbloginblock-connects">
				<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button log-in-13"
						value="{l s='Log in' mod='fbloginblock'}"/>
                <div class="clear"></div>




                {foreach from=$fbloginblockallcon key=prefix_short item=prefix_full name=myLoop}

                    {assign var=prefix_full value=$prefix_full.prefix}


                    {if $fbloginblock_rightcolumn{$prefix_short} == "rightcolumn{$prefix_short}" && $fbloginblock{$prefix_short}_on == 1}

                        <a  href="javascript:void(0)"

                                {if is_int($fbloginblock{$prefix_short}rightcolumnimg)}
                                    class="{$prefix_full|escape:'htmlall':'UTF-8'} custom-social-button-all custom-social-button-{$fbloginblock{$prefix_short}rightcolumnimg|escape:'htmlall':'UTF-8'}"
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


                            {if $fbloginblock{$prefix_short}rightcolumnimg == 1}
                                <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
                            {elseif $fbloginblock{$prefix_short}rightcolumnimg == 2}
                                <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
                            {elseif $fbloginblock{$prefix_short}rightcolumnimg == 3}
                                <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}">&nbsp;{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}</i>
                            {elseif $fbloginblock{$prefix_short}rightcolumnimg == 4}
                                <i class="fa fa-{$prefix_full|escape:'htmlall':'UTF-8'}"></i>
                            {else}
                                <img src="{$fbloginblock{$prefix_short}rightcolumnimg|escape:'htmlall':'UTF-8'}" class="img-top-custom" alt="{$prefix_full|escape:'htmlall':'UTF-8'|capitalize}"  />
                            {/if}

                        </a>&nbsp;

                    {/if}




                {/foreach}

                <div class="clear"></div>
				</div>
			</p>
			<p class="lost_password margin-top-10 padding-left-0">
				<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}password-recovery{else}password.php{/if}">{l s='Forgot your password?' mod='fbloginblock'}</a>
			</p>
		</fieldset>
</form>
</div>

{else}
<div class="block">
		<h4 class="text-align-left">{l s='Your account' mod='fbloginblock'}</h4>
		<div class="block_content">
		<br/>
		<p>
			{l s='Welcome' mod='fbloginblock'},<br/> <b>{$customerName|escape:'htmlall':'UTF-8'}</b> (<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}{else}index.php{/if}?mylogout" 
											 title="{l s='Log out' mod='fbloginblock'}"
                                             class="text-decoration-underline">{l s='Log out' mod='fbloginblock'}</a>)
		</p>
		<br/>
		
		<div>
				<img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/my-account.gif" alt="{l s='Your Account' mod='fbloginblock'}"/>
				<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}my-account{else}my-account.php{/if}" 
				   title="{l s='Your Account' mod='fbloginblock'}"><b>{l s='Your Account' mod='fbloginblock'}</b></a>
		</div>  
		 <br/> 
		<div>
			<img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/cart.gif" alt="{l s='Cart' mod='fbloginblock'}"/>
			<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}order{else}order.php{/if}" title="{l s='Cart' mod='fbloginblock'}"><b>{l s='Cart:' mod='fbloginblock'}</b></a>
			<span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties|escape:'htmlall':'UTF-8'}</span>
			<span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='product' mod='fbloginblock'}</span>
			<span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='products' mod='fbloginblock'}</span>
			
				<span class="ajax_cart_total{if $cart_qties == 0} hidden{/if}">
					{if $priceDisplay == 1}
						{convertPrice price=$cart->getOrderTotal(false, 4)}
					{else}
						{convertPrice price=$cart->getOrderTotal(true, 4)}
					{/if}
				</span>
			<span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='fbloginblock'}</span> 
		</div>
		 <br/>
		</div>
</div>

{/if}

{/if}

{/if}