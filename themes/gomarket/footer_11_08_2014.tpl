{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

			{if !$content_only}
					</div><!-- /Center -->
			{if isset($settings)}
				{if $page_name != 'index'}
					{if (($settings->column == '2_column_right' || $settings->column == '3_column'))}
						<!-- Left -->
						<div id="right_column" class="{$settings->right_class} omega">
							{$HOOK_RIGHT_COLUMN}
						</div>
					{/if}
				{/if}
			{/if}
				</div><!--/columns-->
			</div><!--/container_24-->
			</div>
<!-- Footer -->
			
			<div class="mode_footer">
				<div class="container_24">
					<div id="footer" class="grid_24 clearfix  omega alpha">
						{if isset($HOOK_CS_FOOTER_TOP) && $HOOK_CS_FOOTER_TOP}{$HOOK_CS_FOOTER_TOP}{/if}
						{$HOOK_FOOTER}
						{if isset($HOOK_CS_FOOTER_BOTTOM) && $HOOK_CS_FOOTER_BOTTOM}{$HOOK_CS_FOOTER_BOTTOM}{/if}
						{if $PS_ALLOW_MOBILE_DEVICE}
							<p class="center clearBoth"><a href="{$link->getPageLink('index', true)}?mobile_theme_ok">{l s='Browse the mobile site'}</a></p>
						{/if}
					</div>
				</div>
			</div>
			<div id="toTop">top</div>
		</div><!--/page-->
	{/if}
        
        
{if isset($iexplorerold)}

<div id="popup" style="display: none;">
    <div class="content-popup">
        <div class="close"><a href="#" id="close" onclick="closePopaUp()"><img src="{$base_dir}img/close.png"/></a></div>
        <div>
        	<h2>Actualiza tu navegador </h2>
                <p>Hemos detectado que utilizas una versi&oacute;n vieja de Internet Explorer, te recomendamos actualizar tu navegador para obtener la mejor experiencia de uso.</p>
                <p> Deseo actualizar mi navegador y obtener la mejor experiencia de uso. 
                <div style="text-align: center;"> <a href="http://windows.microsoft.com/es-es/internet-explorer/download-ie"><img src="{$base_dir}img/internet-explorer-11.png" style="width: 64px;"> </a></p>
                </div>
                
                <p> Ahora no quiero tener la mejor experiencia de uso, tal vez en otro momento.
                <div style="text-align: center;" > <a href="#" id="close2" onclick="closePopaUp()"><img src="{$base_dir}img/botonContinuar.gif" style="height: 48px;"> </a></div>
                </p>
               
                
            <div style="float:left; width:100%;">
    	
    </div>
        </div>
    </div>
</div>
<div class="popup-overlay" ></div>

 {/if} 
 
 
        
 {if isset($newsletter)}
   
 <div id="news" style="display: none;">
    <div class="content-news">
        <div class="close-news"><a href="#" id="close-news"><img src="{$base_dir}img/close.png"/></a></div>
        <div style="margin-top: 267px; margin-left: 67px">
            
        <link rel="stylesheet" type="text/css" href="{$css_dir}modules/blocktopmenu/css/opt-in.css" />
        <form method="post" action="{$base_dir}ajax_newsletter.php" >       
        
            <div >
        
          <div class="bloque">
              <input type="text" placeholder="Digita aqu&iacute; t&uacute; correo." name="mail" id="mail" style=" margin: 5px; width: 319px; height: 30px; font-size: 20px; text-align: left; " />
        </div>
                <div class="bloque"  style="margin: 0 0 0 -2px;" >
            <div class="celda"><input type="submit" name="hombre" id="hombre1" value=""  /></div>
            <div class="celda"><input type="submit" name="mujer" id="mujer1" value="" /></div>           
           </div>
            </div>
            
        </form>
        
    </div>
        </div>
    </div>       
        <div class="news-overlay" ></div>
       
{/if}

{if isset($lightbox1)}
   
{/if} 
        
        <!-- Start Alexa Certify Javascript -->
        <script type="text/javascript">
            _atrk_opts = { atrk_acct:"J+pRi1a8Dy00yS", domain:"farmalisto.com.co",dynamic: true};
            (function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
        </script>
        <noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=J+pRi1a8Dy00yS" style="display:none" height="1" width="1" alt="" /></noscript>
        <!-- End Alexa Certify Javascript -->



	</body>
</html>
