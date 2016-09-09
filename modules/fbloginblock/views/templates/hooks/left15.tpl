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

{if $fbloginblock_leftcolumnf == "leftcolumnf" || $fbloginblock_leftcolumng == "leftcolumng" 
    || $fbloginblock_leftcolumnt == "leftcolumnt" 
	|| $fbloginblock_leftcolumny == "leftcolumny" || $fbloginblock_leftcolumnl == "leftcolumnl"
	|| $fbloginblock_leftcolumnm == "leftcolumnm" || $fbloginblock_leftcolumni == "leftcolumni"
	|| $fbloginblock_leftcolumnfs == "leftcolumnfs" || $fbloginblock_leftcolumngi == "leftcolumngi"
	|| $fbloginblock_leftcolumnd == "leftcolumnd" || $fbloginblock_leftcolumna == "leftcolumna"}

{if !$fbloginblockislogged}

<div id="fbloginblock_block_left" class="block">
		<h4 class="title_block" style="text-align:left;{if $fbloginblockis16 == 1}margin-bottom:0px{/if}">{l s='Your account' mod='fbloginblock'}</h4>
<div class="block_content">		
<form action="{if $fbloginblockis_rewrite == 1}{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}{else}{$base_dir_ssl|escape:'htmlall':'UTF-8'}index.php?controller=authentication{/if}" method="post">
		<fieldset style="border-bottom:1px none #D0D3D8;border-top:none;background-color:#F1F2F4">
		<div class="form_content clearfix">
			<p class="text" style="padding-bottom:10px">
				<br/>
				<label for="email" style="margin-left:10px"><b>{l s='E-mail:' mod='fbloginblock'}</b></label>
				<br/>
				<span  style="margin-left:10px"><input type="text" id="email" name="email" 
							 value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" 
							 class="account_input" style="width:14em"/>
				</span>
			</p>
			<p class="text" style="padding-bottom:10px">
				<br/>
				<label for="passwd"  style="margin-left:10px"><b>{l s='Password:' mod='fbloginblock'}</b></label>
				<br/>
				<span  style="margin-left:10px"><input type="password" id="passwd" name="passwd" 
							 value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|escape:'htmlall':'UTF-8'|stripslashes}{/if}" 
							 class="account_input" style="width:14em"/>
				</span>
			</p>
				{if isset($back)}
					<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}"  style="margin-left:10px" />
				{/if}
				
				<div class="fbtwgblock-columns15">
				<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" 
						value="{l s='Log in' mod='fbloginblock'}" />
				<div style="clear:both"></div>
				{if $fbloginblock_leftcolumnf == "leftcolumnf" && $fbloginblockf_on == 1}
				<a href="javascript:void(0)" onclick="return fblogin();" 
				   title="Facebook" >
	   				<img src="{$fbloginblockfleftimg|escape:'htmlall':'UTF-8'}" alt="Facebook"  />
	 			</a>
	 			{/if}
	 			{if $fbloginblock_leftcolumnt == "leftcolumnt" && $fbloginblockt_on == 1}
	 			<a href="javascript:void(0)" title="Twitter" 
				{if $fbloginblocktconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/twitter.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'login', 'location,width=600,height=600,top=0'); popupWin.focus();"
		   		{else}
					onclick="alert('{$terror|escape:'htmlall':'UTF-8'}')"
				{/if}  
		   		>
						<img src="{$fbloginblocktleftimg|escape:'htmlall':'UTF-8'}" alt="Twitter" />
				</a>
				{/if}
				
				{if $fbloginblock_leftcolumna == "leftcolumna" && $fbloginblocka_on == 1}
				<a href="javascript:void(0)" onclick="return amazonlogin();" 
				   title="Amazon" >
	   				<img src="{$fbloginblockaleftimg|escape:'htmlall':'UTF-8'}" alt="Amazon"  />
	 			</a>
	 			{/if}
				
				
	 			{if $fbloginblock_leftcolumng == "leftcolumng" && $fbloginblockg_on == 1}
	 			<a href="javascript:void(0)" title="Google" 
	 			{if $fbloginblockgconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/login.php?p=google{if $fbloginblockorder_page == 1}&http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();"
			   	{else}
					onclick="alert('{$gerror|escape:'htmlall':'UTF-8'}')"
				{/if} 
		   		   >
						<img src="{$fbloginblockgleftimg|escape:'htmlall':'UTF-8'}" alt="Google" />
				</a>
				{/if}
				 {if $fbloginblock_leftcolumny == "leftcolumny" && $fbloginblocky_on == 1}
				<a href="javascript:void(0)" title="Yahoo"
		   			onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/login.php?p=yahoo{if $fbloginblockorder_page == 1}&http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=400,height=300,top=0');popupWin.focus();">
					<img src="{$fbloginblockyleftimg|escape:'htmlall':'UTF-8'}" alt="Yahoo"  />
				</a>
	 			{/if}
				
				{if $fbloginblock_leftcolumnl == "leftcolumnl" && $fbloginblockl_on == 1}
	 			<a href="javascript:void(0)" title="LinkedIn" 
		   		    {if $fbloginblocklconf == 1}  
		   		   		onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/linkedin.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   			{else}
				   		onclick="alert('{$lerror|escape:'htmlall':'UTF-8'}')">
					{/if}
		 			<img src="{$fbloginblocklleftimg|escape:'htmlall':'UTF-8'}" alt="LinkedIn" />
				</a>
				{/if}
				
				{if $fbloginblock_leftcolumnm == "leftcolumnm" && $fbloginblockm_on == 1}
	 			<a href="javascript:void(0)" title="Microsoft Live" 
		   		{if $fbloginblockmconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/microsoft.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		       		onclick="alert('{$merror|escape:'htmlall':'UTF-8'}')">
				 {/if}
		        
		        	<img src="{$fbloginblockmleftimg|escape:'htmlall':'UTF-8'}" alt="Microsoft Live" />
				</a>
				{/if}
				
				{if $fbloginblock_leftcolumni == "leftcolumni" && $fbloginblocki_on == 1}
	 			<a href="javascript:void(0)" title="Instagram"
		   		{if $fbloginblockiconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/instagram.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		       		onclick="alert('{$ierror|escape:'htmlall':'UTF-8'}')">
				 {/if}
		        
		        	<img src="{$fbloginblockileftimg|escape:'htmlall':'UTF-8'}" alt="Instagram" />
				</a>
				{/if}
				
				{if $fbloginblock_leftcolumnfs == "leftcolumnfs" && $fbloginblockfs_on == 1}
	 			<a href="javascript:void(0)" title="Foursquare" 
		   		{if $fbloginblockfsconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/foursquare.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		       		onclick="alert('{$fserror|escape:'htmlall':'UTF-8'}')">
				 {/if}
		        
		        	<img src="{$fbloginblockfsleftimg|escape:'htmlall':'UTF-8'}" alt="Foursquare" />
				</a>
				{/if}
				
				
				{if $fbloginblock_leftcolumngi == "leftcolumngi" && $fbloginblockgi_on == 1}
	 			<a href="javascript:void(0)" title="Github" 
		   		{if $fbloginblockgiconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/github.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		       		onclick="alert('{$gierror|escape:'htmlall':'UTF-8'}')">
				 {/if}
		        
		        	<img src="{$fbloginblockgileftimg|escape:'htmlall':'UTF-8'}" alt="Github" />
				</a>
				{/if}
				
				{if $fbloginblock_leftcolumnd == "leftcolumnd" && $fbloginblockd_on == 1}
	 			<a href="javascript:void(0)" title="Disqus" class="fbloginblock-last"
		   		{if $fbloginblockdconf == 1}
		   		   onclick="javascript:popupWin = window.open('{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/disqus.php{if $fbloginblockorder_page == 1}?http_referer={$fbloginblockhttp_referer|urlencode}{/if}', 'openId', 'location,width=512,height=512,top=0');popupWin.focus();">
		   		{else}
		       		onclick="alert('{$derror|escape:'htmlall':'UTF-8'}')">
				 {/if}
		        
		        	<img src="{$fbloginblockdleftimg|escape:'htmlall':'UTF-8'}" alt="Disqus" />
				</a>
				{/if}
				
				<div style="clear:both"></div>
				</div>
			<p class="lost_password" style="margin-top:10px;">
				<a style="margin-left:10px" href="{if $fbloginblockis_rewrite == 1}{$link->getPageLink('password')|escape:'html':'UTF-8'}{else}{$base_dir_ssl|escape:'htmlall':'UTF-8'}index.php?controller=password{/if}">{l s='Forgot your password?' mod='fbloginblock'}</a>
			</p>
			</div>
		</fieldset>
</form>
</div>
</div>

{else}
<div id="fbloginblock_block_left"  class="block">
		<h4 class="title_block" style="text-align:left;{if $fbloginblockis16 == 1}margin-bottom:0px{/if}">{l s='Your account' mod='fbloginblock'}</h4>
		<div class="block_content" style="background-color:#F1F2F4">
		<br/>
		<p style="padding-left:10px">
			{l s='Welcome' mod='fbloginblock'},<br/> <b>{$customerName|escape:'htmlall':'UTF-8'}</b> (<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $fbloginblockis_rewrite == 1}{$fbloginblockiso_lang|escape:'htmlall':'UTF-8'}{else}index.php{/if}?mylogout" 
											 title="{l s='Log out' mod='fbloginblock'}"
											 style="text-decoration:underline">{l s='Log out' mod='fbloginblock'}</a>)
		</p>
		<br/>
		
		<div style="padding-left:10px">
				<img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/icon/my-account.gif" alt="{l s='Your Account' mod='fbloginblock'}"/>
				<a href="{if $fbloginblockis_rewrite == 1}{$link->getPageLink('my-account', true)|escape:'html'}{else}{$base_dir_ssl|escape:'htmlall':'UTF-8'}index.php?controller=my-account{/if}" 
				   title="{l s='Your Account' mod='fbloginblock'}"><b>{l s='Your Account' mod='fbloginblock'}</b></a>
		</div>  
		 <br/> 
		<div style="padding-left:10px">
			<img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/fbloginblock/views/img/icon/cart.gif" alt="{l s='Your Shopping Cart' mod='fbloginblock'}"/>
			<a href="{if $fbloginblockis_rewrite == 1}{$link->getPageLink('order', true)|escape:'html'}{else}{$base_dir_ssl|escape:'htmlall':'UTF-8'}index.php?controller=order{/if}" title="{l s='Your Shopping Cart' mod='fbloginblock'}"><b>{l s='Cart:' mod='fbloginblock'}</b></a>
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