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
*}

<div name="opcion1" onmouseover="mouse_overd('div1');" onmouseout="mouse_outd('div1');">
<p class="payment_module">
	<a href="{$pathSsl|escape:'htmlall':'UTF-8'}payment.php" name="ref1"  id="ref1">
		<img src="{$img_dir}mediosp/baloto.png" alt="Otros metodos de pagos" style="width:35px"/>
		Vía Baloto
	</a>
</p>
</div>

<div name="opcion2" onclick="mouse_overd('div2');" >
<p class="payment_module">
<a href="#">
		<img src="{$img_dir}mediosp/credito.png" alt="Tarjetas Farmalisto" style="width:35px"/>
		Tarjeta de crédito
	</a>
</p>
</div>

<div name="opcion3">
<p class="payment_module">
	<a href="{$pathSsl|escape:'htmlall':'UTF-8'}payment.php">
		<img src="{$img_dir}mediosp/pse.png" alt="Otros metodos de pagos" style="width:35px"/>
		Cuenta corriente o ahorros
	</a>
</p>
</div>

    
    
 




