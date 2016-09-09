{*
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category social_networks
 * @package fbloginblock
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{if $fbloginblock_rightcolumnf == "rightcolumnf" || $fbloginblock_rightcolumng == "rightcolumng" 
	|| $fbloginblock_rightcolumnt == "rightcolumnt" 
	|| $fbloginblock_rightcolumny == "rightcolumny" || $fbloginblock_rightcolumnl == "rightcolumnl"
	|| $fbloginblock_rightcolumnm == "rightcolumnm" || $fbloginblock_rightcolumni == "rightcolumni"
	|| $fbloginblock_rightcolumnfs == "rightcolumnfs" || $fbloginblock_rightcolumngi == "rightcolumngi"
	|| $fbloginblock_rightcolumnd == "rightcolumnd" || $fbloginblock_rightcolumna == "rightcolumna"}

{if !$cookie->isLogged()}

<div class="block">
		<h4 style="text-align:left;">{l s='Your account' mod='fbloginblock'}</h4>
<form action="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}{if $fbloginblockis15 == 1}login{else}authentication{/if}{else}authentication.php{/if}" method="post">
		<fieldset class="block_content" style="border-bottom:1px none #D0D3D8;border-top:none">
			<p class="text">
				<br/>
				<label for="email"><b>{l s='E-mail:' mod='fbloginblock'}</b></label>
				<span><input type="text" id="email" name="email" 
							 value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" 
							 class="account_input" style="width:14em"/>
				</span>
			</p>
			<p class="text">
				<br/>
				<label for="passwd"><b>{l s='Password:' mod='fbloginblock'}</b></label>
				<span><input type="password" id="passwd" name="passwd" 
							 value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|escape:'htmlall':'UTF-8'|stripslashes}{/if}" 
							 class="account_input" style="width:14em"/>
				</span>
			</p>
			<p class="submit">
				{if isset($back)}
					<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />
				{/if}
				<div class="fbtwgblock-columns">
				<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" 
						value="{l s='Log in' mod='fbloginblock'}" style="margin-left:0px;float:left"/>
				<div style="clear:both"></div>
				{if $fbloginblock_rightcolumnf == "rightcolumnf" && $fbloginblockf_on == 1}
				<a href="javascript:void(0)" onclick="return fblogin();" 
				   title="Facebook" >
	   				<img src="{$fbloginblockfrightimg|escape:'htmlall':'UTF-8'}" alt="Facebook"  />
	 			</a>
	 			{/if}
	 			{if $fbloginblock_rightcolumnt == "rightcolumnt" && $fbloginblockt_on == 1}
	 			<a href="javascript:void(0)" title="Twitter" 
	 			{if $fbloginblocktconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/twitter.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'login', 'location,width=600,height=600,top=0'); popupWin.focus();"
		   		 {else}
		   			onclick="alert('{$terror|escape:'htmlall':'UTF-8'}')"
				{/if}  
		   		   >
						<img src="{$fbloginblocktrightimg|escape:'htmlall':'UTF-8'}" alt="Twitter" />
				</a>
				{/if}
				
				{if $fbloginblock_rightcolumna == "rightcolumna" && $fbloginblocka_on == 1}
				<a href="javascript:void(0)" onclick="return amazonlogin();" 
				   title="Amazon" >
	   				<img src="{$fbloginblockarightimg|escape:'htmlall':'UTF-8'}" alt="Amazon"  />
	 			</a>
	 			{/if}
				
	 			{if $fbloginblock_rightcolumng == "rightcolumng" && $fbloginblockg_on == 1}
	 			<a href="javascript:void(0)" title="Google" 
	 			{if $fbloginblockgconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/login.php{if $fbloginblockorder_page == 1}?p=google&http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();"
			   	{else}
					onclick="alert('{$gerror|escape:'htmlall':'UTF-8'}')"
				{/if}   
		   		   >
						<img src="{$fbloginblockgrightimg|escape:'htmlall':'UTF-8'}" alt="Google" />
				</a>
				{/if}
				{if $fbloginblock_rightcolumny == "rightcolumny" && $fbloginblocky_on == 1}
				<a href="javascript:void(0)" title="Yahoo" 
		   			onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/login.php?p=yahoo{if $fbloginblockorder_page == 1}&http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=400,height=300,top=0');popupWin.focus();">
					<img src="{$fbloginblockyrightimg|escape:'htmlall':'UTF-8'}" alt="Yahoo"  />
				</a>
	 			{/if}
				
				{if $fbloginblock_rightcolumnl == "rightcolumnl" && $fbloginblockl_on == 1}
	 			<a href="javascript:void(0)" title="LinkedIn"
	 			{if $fbloginblocklconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/linkedin.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
					onclick="alert('{$lerror|escape:'htmlall':'UTF-8'}')">
					
				{/if}   
						<img src="{$fbloginblocklrightimg|escape:'htmlall':'UTF-8'}"  alt="LinkedIn" />
				</a>
				{/if}
				
				{if $fbloginblock_rightcolumnm == "rightcolumnm" && $fbloginblockm_on == 1}
	 			<a href="javascript:void(0)" title="Microsoft Live" 
		   		{if $fbloginblockmconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/microsoft.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		        	onclick="alert('{$merror|escape:'htmlall':'UTF-8'}')">
				{/if}
		        		<img src="{$fbloginblockmrightimg|escape:'htmlall':'UTF-8'}"  alt="Microsoft Live" />
				</a>
				{/if}
				
				{if $fbloginblock_rightcolumni == "rightcolumni" && $fbloginblocki_on == 1}
	 			<a href="javascript:void(0)" title="Instagram" 
		   		{if $fbloginblockiconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/instagram.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		        	onclick="alert('{$ierror|escape:'htmlall':'UTF-8'}')">
				{/if}
		        		<img src="{$fbloginblockirightimg|escape:'htmlall':'UTF-8'}"  alt="Instagram" />
				</a>
				{/if}
				
				
				{if $fbloginblock_rightcolumnfs == "rightcolumnfs" && $fbloginblockfs_on == 1}
	 			<a href="javascript:void(0)" title="Foursquare" 
		   		{if $fbloginblockfsconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/foursquare.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		        	onclick="alert('{$fserror|escape:'htmlall':'UTF-8'}')">
				{/if}
		        		<img src="{$fbloginblockfsrightimg|escape:'htmlall':'UTF-8'}"  alt="Foursquare" />
				</a>
				{/if}
				
				
				{if $fbloginblock_rightcolumngi == "rightcolumngi" && $fbloginblockgi_on == 1}
	 			<a href="javascript:void(0)" title="Github" 
		   		{if $fbloginblockgiconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/github.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		        	onclick="alert('{$gierror|escape:'htmlall':'UTF-8'}')">
				{/if}
		        		<img src="{$fbloginblockgirightimg|escape:'htmlall':'UTF-8'}"  alt="Github" />
				</a>
				{/if}
				
				
				{if $fbloginblock_rightcolumnd == "rightcolumnd" && $fbloginblockd_on == 1}
	 			<a href="javascript:void(0)" title="Disqus" class="fbloginblock-last"
		   		{if $fbloginblockdconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/disqus.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		        	onclick="alert('{$derror|escape:'htmlall':'UTF-8'}')">
				{/if}
		        		<img src="{$fbloginblockdrightimg|escape:'htmlall':'UTF-8'}"  alt="Disqus" />
				</a>
				{/if}
				
				<div style="clear:both"></div>
				</div>
			</p>
			<p class="lost_password" style="margin-top:10px;padding-left:0px">
				<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}password-recovery{else}password.php{/if}">{l s='Forgot your password?' mod='fbloginblock'}</a>
			</p>
		</fieldset>
</form>
</div>

{else}
<div class="block">
		<h4 style="text-align:left;">{l s='Your account' mod='fbloginblock'}</h4>
		<div class="block_content">
		<br/>
		<p>
			{l s='Welcome' mod='fbloginblock'},<br/> <b>{$customerName|escape:'htmlall':'UTF-8'}</b> (<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}{else}index.php{/if}?mylogout" 
											 title="{l s='Log out' mod='fbloginblock'}"
											 style="text-decoration:underline">{l s='Log out' mod='fbloginblock'}</a>)
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