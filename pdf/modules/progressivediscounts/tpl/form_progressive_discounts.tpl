<form id="formadd_progressive_discounts" enctype="multipart/form-data" method="post" >
	
	<div id="block_error" style="background: #ffbaba url('{$module_template_dir}/icon/cancel.png') no-repeat scroll 6px 6px;"></div>

	<div id="block_information">

		<div id="field_id" class="margin_field">
			ID Descuento Progresivo: <input id="txt_id_pd" name="txt_id_pd" type="text">
		</div>

		<label>Nombre: </label>
		<div id="field_name" class="margin_field">
			<input id="txt_name" name="txt_name" type="text" size="50"> <sup>*</sup>
		</div>

		<label>Descripción: </label>
		<div id="field_description" class="margin_field">
			<textarea id="txt_description" style="height: 100px;width: 80%;"></textarea> <sup>*</sup>
		</div>

		<label>Frecuencia: </label>
		<div id="field_frequency" class="margin_field">
			<input id="txt_frequency" name="txt_frequency" type="text" size="3" maxlength="3" onkeypress="return validateNumbers(event);"> Día(s) <sup>*</sup>
			<p class="preference_description">Rango de días en el que el cliente debe comprar para obtener el beneficio</p>
		</div>

		<label>Periodos: </label>
		<div id="field_periods" class="margin_field">
			<input id="txt_periods" name="txt_periods" type="text" size="3" maxlength="3" onkeypress="return validateNumbers(event);"> <sup>*</sup>
			<p class="preference_description">Cantidad de veces que se repetira la frecuencia</p>
		</div>

		<label>Limite Compras Por Cliente: </label>
		<div id="field_limit_shopping_customer" class="margin_field">
			<input id="txt_limit_shopping_customer" name="txt_limit_shopping_customer" type="text" size="3" maxlength="3" onkeypress="return validateNumbers(event);"> Orden(es) <sup>*</sup>
			<p class="preference_description">Cantidad de compras con descuento para el cliente por cada periodo</p>
		</div>

		<label>Reinicio: </label>
		<div id="field_reset" class="margin_field">
			<input id="txt_reset" name="txt_reset" type="text" size="3" maxlength="3" onkeypress="return validateNumbers(event);"> Orden(es) <sup>*</sup>
			<p class="preference_description">Cantidad de descuentos antes de volver a iniciar el ciclo</p>
		</div>

		<label>Ciclos: </label>
		<div id="field_cycles" class="margin_field">
			<input id="txt_cycles" name="txt_cycles" type="text" size="3" maxlength="3" onkeypress="return validateNumbers(event);"> <sup>*</sup>
			<p class="preference_description">Numero de frecuencias por periodos en el cual se ejecutaran los descuentos progresivos (1 Ciclo = frecuencia * periodo)</p>
		</div>

		<label>Estado: </label>
		<div id="field_state" class="margin_field">
			<input id="chk_state" name="chk_state" type="radio" value="1" checked>
			<label class="t">
				<img title="Activo" alt="Activo" src="{$module_template_dir}/icon/enabled.gif">
			</label>

			<input id="chk_state" name="chk_state" type="radio" value="0">
			<label class="t">
				<img title="Inactivo" alt="Inactivo" src="{$module_template_dir}/icon/disabled.gif">
			</label>
			<sup>*</sup>
		</div>

		<label>Estados Orden: </label>
		<div id="field_list_states_orders" class="margin_field">
			<div id="list_states_orders"></div>
			<p class="preference_description">Estados de orden, en los cuales sera valido aplicar el descuentos progresivos</p>
		</div>
	</div>

	<div id="block_cart_rules">
		<fieldset id="fielsets_list_coupon">
			<legend id="fielsets_list_coupon">Cupones</legend>

			<div id="block_search_coupon">
				<label>Cupón: </label>
				<div id="field_coupon" class="margin_field">
					<input id="txt_coupon" name="txt_coupon" type="text" size="30">
					<img id="button_search_coupon" title="Agregar" src="{$module_template_dir}icon/new.png"/>
					<sup id='alert_coupon'>Cupón Inactivo o Inexistente</sup>
					<p class="preference_description">ID</p>
					<p class="preference_description">El orden en el que ingrese los cupones, será el orden en el que se aplicaran los descuentos durante el descuento progresivo</p>
				</div>
			</div>
			
			<div id="list_cart_rules" class="list">
				<table id="rows_cart_rules">
					<tr id="header_table_coupon">
						<th>Escala</th>
						<th>ID Cupón</th>
						<th>Nombre</th>
						<th>Descuento Porcentaje</th>
						<th>Descuento Importe</th>
						<th>Producto Especifico</th>
						<th>Transporte Gratuito</th>
						<th>Regalo Producto</th>
						<th>Quitar</th>
					</tr>
				</table>
			</div>

		</fieldset>
	</div>
	<br>
	<div id="block_products">
		<fieldset id="fielsets_list_product">
			<legend id="fielsets_list_product">Productos</legend>

			<div id="block_search_product">
				<label>Producto: </label>
				<div id="field_product" class="margin_field">
					<input id="txt_product" name="txt_product" type="text" size="30">
					<img id="button_search_product" title="Agregar" src="{$module_template_dir}icon/new.png"/>
					<sup id='alert_product'>Producto Inactivo o Inexistente</sup>
					<sup id='alert_productExistList'>El producto ya existe en la lista</sup>
					<sup id='alert_productExistPD'>El producto ya se encuentra incluido en un descuento progresivo</sup>
					<p class="preference_description">ID o Referencia</p>
				</div>
			</div>
			
			<div id="list_products" class="list">
				<table id="rows_products">
					<tr id="header_table_product">
						<th>ID Producto</th>
						<th>Referencia</th>
						<th>Nombre</th>
						<th>IVA</th>
						<th>Precio Base</th>
						<th>Precio Final</th>
						<th>Quitar</th>
					</tr>
				</table>
			</div>

		</fieldset>
	</div>

</form>