{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="row">
    <div class="col-sm-4 col-md-4 col-lg-8">
		<h4>{l s='Carts for first reminder' mod='cartabandonmentpro'}</h4>
		<table class="table table-striped" style="border: 0; width: 100%;">
			<thead>
				<tr>
					<th>{l s='Id cart' mod='cartabandonmentpro'}</th>
					<th>{l s='Firstname' mod='cartabandonmentpro'}</th>
					<th>{l s='Lastname' mod='cartabandonmentpro'}</th>
					<th>{l s='Email' mod='cartabandonmentpro'}</th>
					<th>{l s='Date' mod='cartabandonmentpro'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$carts1 item=cart}
					<tr>
						<td>{$cart.id_cart}</td>
						<td>{$cart.firstname}</td>
						<td>{$cart.lastname}</td>
						<td>{$cart.email}</td>
						<td>{$cart.date_upd}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		
		<h4>{l s='Carts for second reminder' mod='cartabandonmentpro'}</h4>
		<table class="table table-striped" style="border: 0; width: 100%;">
			<thead>
				<tr>
					<th>{l s='Id cart' mod='cartabandonmentpro'}</th>
					<th>{l s='Firstname' mod='cartabandonmentpro'}</th>
					<th>{l s='Lastname' mod='cartabandonmentpro'}</th>
					<th>{l s='Email' mod='cartabandonmentpro'}</th>
					<th>{l s='Date' mod='cartabandonmentpro'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$carts2 item=cart}
					<tr>
						<td>{$cart.id_cart}</td>
						<td>{$cart.firstname}</td>
						<td>{$cart.lastname}</td>
						<td>{$cart.email}</td>
						<td>{$cart.date_upd}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		
		<h4>{l s='Carts for third reminder' mod='cartabandonmentpro'}</h4>
		<table class="table table-striped" style="border: 0; width: 100%;">
			<thead>
				<tr>
					<th>{l s='Id cart' mod='cartabandonmentpro'}</th>
					<th>{l s='Firstname' mod='cartabandonmentpro'}</th>
					<th>{l s='Lastname' mod='cartabandonmentpro'}</th>
					<th>{l s='Email' mod='cartabandonmentpro'}</th>
					<th>{l s='Date' mod='cartabandonmentpro'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$carts3 item=cart}
					<tr>
						<td>{$cart.id_cart}</td>
						<td>{$cart.firstname}</td>
						<td>{$cart.lastname}</td>
						<td>{$cart.email}</td>
						<td>{$cart.date_upd}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		
		<h4>{l s='Mails statistics' mod='cartabandonmentpro'}</h4>
		<table class="table table-striped" style="border: 0; width: 100%;">
			<thead>
				<tr>
					<th>{l s='Mails sent' mod='cartabandonmentpro'}</th>
					<th>{l s='Mails opened' mod='cartabandonmentpro'}</th>
					<th>{l s='Mails clicked' mod='cartabandonmentpro'}</th>
					<th>{l s='Unsubscribe' mod='cartabandonmentpro'}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$stats.0.count}</td>
					<td>{$stats.0.view}</td>
					<td>{$stats.0.click}</td>
					<td>{$unsubscribe}</td>
				</tr>
			</tbody>
		</table>
    </div>
</div>
