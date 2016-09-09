{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA

* 1 <a href="{$pathSsl|escape:'htmlall':'UTF-8'}modules/payulatam/payment.php" name="ref1"  id="ref1">
* 3 <a href="{$pathSsl|escape:'htmlall':'UTF-8'}modules/payulatam/payment.php">
*}
<div class="separador"><img src="{$img_dir}hor_sep.jpg" width="100%" height="1px"/></div>
<div id="divs">
 <div id="div4" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
     
     <div class="contendfrom">
	    <div style=" width: auto; height: auto; text-justify: auto;">
	        <p class="textapoyo">Este metodo de pago, no esta disponible para tu ciudad, por favor utiliza otro.</p>
	    </div>


	<!--  -->
	</div>
     
 </div>
 </div>
{*
<div>
<div name="opcion1" id="opciones">
    <p class="payment_module" style="text-transform: capitalize;" id="textradiobaloto">
    <input type="radio" value="div1" name="mediopago" id="mediopagob"   onclick="mouse_overd('div1');" >
    <img src="{$img_dir}mediosp/baloto.png" id="imgbaloto" alt="Pagos con Baloto" style="width:35px"/>
		Vía Baloto
</p> 

</div>
<div class="separador"><img src="{$img_dir}hor_sep.jpg" width="100%" height="1px"/></div>
<div id="divs" name="divs" >
    
 <!-- formula medica -->  
   {if isset($disableBaloto) and !$disableBaloto and (isset($isblockmpb) and !$isblockmpb)}
   <div id="div1" style="float: left;" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
 
     <div class="contendfrom">
	    <div style=" width: auto; height: auto;">
	        <p   class="textapoyo"  style="text-justify: distribute;">Finaliza tu compra para recibir los datos con los que podras acercarte a un punto Baloto y realizar tu pago.</p>
	    
             
	    {include file="$tpl_dir../../modules/payulatam/tpl/payuBaloto.tpl"}
	    </div>
     </div>
                       
</div>
  {else if isset($isblockmpb) and $isblockmpb}
      
      <div id="div1" style="float: left;" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
 
     <div class="contendfrom">
	    <div style=" width: auto; height: auto;">
	        <p   class="textapoyo"  style="text-justify: distribute;">El monto para BALOTO no debe superar los 500.000 pesos. Por favor intenta con otro medio de pago.</p>
	    
             
	      </div>
     </div>
                       
</div>   
      
      
      {else}         
          
          
      
   <div id="div1" style="float: left;" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
 
     <div class="contendfrom">
	    <div style=" width: auto; height: auto;">
	        <p   class="textapoyo"  style="text-justify: distribute;">El pago con Baloto no esta disponible para tu ciudad, por favor intenta con otro medio de pago.</p>
	    
             
	      </div>
     </div>
                       
</div>      
      
               
  {/if}
  
 <!-- fin formula -->
</div>
</div> *}



<div>
<div name="opcion2" id="opciones">
<p class="payment_module" style="text-transform: capitalize;">
<input type="radio" value="div2" name="mediopago" id="mediopagot" onclick="mouse_overd('div2');" >
		<img src="{$img_dir}mediosp/credito.png" alt="Tarjetas Farmalisto" style="width:35px"/>
		Tarjeta de crédito
</p> 
</div>
<div class="separador"><img src="{$img_dir}hor_sep.jpg" width="100%" height="1px"/></div>
<div id="divs">
<div id="div2" style="display: none; ">              
	<div class="contendfrom">
	 <div style=" width: auto; height: auto; text-justify: auto;">
	    {include file="$tpl_dir../../modules/payulatam/tpl/process.tpl"}
	 </div>

	</div>
</div>
</div>
</div>
{*
<div>
<div name="opcion3" id="opciones">
<p class="payment_module" style="text-transform: capitalize;">
	<input type="radio" value="div3" name="mediopago" id="mediopagop" onclick="mouse_overd('div3');" >
		<img src="{$img_dir}mediosp/pse.png" alt="pagos con PSE" style="width:35px"/>
		Cuenta corriente o ahorros
</p> 
</div>
<div class="separador"><img src="{$img_dir}hor_sep.jpg" width="100%" height="1px"/></div>
<div id="divs">
			<div id="div3" style="display: none; ">              
				<div class="contendfrom">
				 <div style=" width: auto; height: auto; text-justify: auto;">
				    {include file="$tpl_dir../../modules/payulatam/tpl/payuPse.tpl"}
				 </div>

				</div>
			</div>

			 <div id="div3" style="display: none; ">              
				<div class="contendfrom">
				 <div style=" width: auto; height: auto; text-justify: auto;">
				    {include file="$tpl_dir../../modules/payulatam/tpl/payuPse.tpl"}
				 </div>

				</div>
			</div>
</div>


 </div>

<div>
<div name="opcion5" id="opciones">
   
<p class="payment_module" style="text-transform: capitalize;" id="textradioefecty">
<input type="radio" value="div5" name="mediopago" id="mediopagoe" onclick="mouse_overd('div5');" >
<img src="{$img_dir}mediosp/efecty.jpg" alt="Pago con Efecty" id="imgEfecty" style="width:35px"/>
		Pago con Efecty
</p> 
</div>
<div class="separador"><img src="{$img_dir}hor_sep.jpg" width="100%" height="1px"/></div>
<div id="divs">
   {if isset($disableBaloto) and !$disableBaloto and (isset($isblockmpb) and !$isblockmpb)}   
<div id="div5" style="display: none; ">              
	<div class="contendfrom">
	 <div style=" width: auto; height: auto; text-justify: auto;">
              <p   class="textapoyo"  style="text-justify: distribute;">Finaliza tu compra para recibir los datos con los que podras acercarte a un punto Efecty y realizar tu pago.</p>
	    
	    {include file="$tpl_dir../../modules/payulatam/tpl/payuEfecty.tpl"}
	 </div>

	</div>
</div>
 {else if isset($isblockmpb) and $isblockmpb}
      
      <div id="div5" style="float: left;" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
 
     <div class="contendfrom">
	    <div style=" width: auto; height: auto;">
	        <p   class="textapoyo"  style="text-justify: distribute;">El monto para Efecty no debe superar los 500.000 pesos. Por favor intenta con otro medio de pago.</p>
	    
             
	      </div>
     </div>
                       
</div>   
      
      
      {else}         
          
          
      
   <div id="div5" style="float: left;" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
 
     <div class="contendfrom">
	    <div style=" width: auto; height: auto;">
	        <p   class="textapoyo"  style="text-justify: distribute;">El pago con Efecty no esta disponible para tu ciudad, por favor intenta con otro medio de pago.</p>
	    
             
	      </div>
     </div>
                       
</div>      
      
               
  {/if}
  
  
  
  
  
  
</div>
</div>
  *}
    


