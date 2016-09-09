{if empty($items_progressive_discounts) }
	<div id="alert_empty_list_pd">No se encontraron elementos</div>
{else}
	<div class="list">
		<table>
			<tr>
				<th>ID</th>
				<th>Nombre</th>
				<th>Frecuencia (# Días)</th>
				<th>Numero De Periodos</th>
				<th>Numero Limite Compras Por Cliente</th>
				<th>Reinicio</th>
				<th>Ciclos</th>
				<th>Fecha De &nbsp;&nbsp;Creación&nbsp;&nbsp;</th>
				<th>Fecha De Modificación</th>
				<th>Activo</th>
				<th>Ver Detalles</th>
			</tr>			
		
			{foreach key=key item=item from=$items_progressive_discounts}
				<tr>
					<td>{$item['id_progressive_discount']}</td>
					<td>{$item['name']}</td>
					<td>{$item['frequency']}</td>
					<td>{$item['periods']}</td>
					<td>{$item['limit_shopping_customer']}</td>
					<td>{$item['shopping_reset']}</td>
					<td>{$item['cycles']}</td>
					<td>{$item['date_create']}</td>
					<td>{$item['date_modify']}</td>
					<td>
						{if $item['active'] == 1}
							<img id="enabledPD" title="Activo" src="{$module_template_dir}icon/enabled.gif" onclick="changeStatus({$item['id_progressive_discount']},0);"/>
						{else}
							<img id="disabledPD" title="Inactivo" src="{$module_template_dir}icon/disabled.gif" />
						{/if}
					</td>
					<td>
						<span onclick="viewDetailProgressiveDiscount('{$item['id_progressive_discount']}')">
							<a href="#detailProgresiveDiscount" class="fancybox">
								<img id="button_view_detail" title="Ver Detalles" src="{$module_template_dir}icon/search.png" />
							</a>
						</span>
					</td>
				</tr>
			{/foreach}
		</table>
	</div>
	<div id='detailProgresiveDiscount'></div>
{/if}