<h2>{l s={$displayName} mod='icrlocation'}</h2>
<form id="form-icr-location-add" method="POST">
	<fieldset>
		<legend>
			<img src="{$module_template_dir}views/img/config.gif" alt="{l s='Ubicación de ICRs' mod='icrlocation'}">{l s='Ubicación de ICRs' mod='icrlocation'}
		</legend>
		
		<div class="form-group">
			<label for="warehouse">{l s='Almacén' mod='icrlocation'} </label>
			<div class="margin-form">
				<select name="warehouse" id="warehouse" required>
					<option value="">Seleccione</option>
					{foreach item=warehouse from=$warehouses}
					<option value="{$warehouse.reference}">{$warehouse.name}</option>
					{/foreach}
				</select>
				<sup>*</sup>
			</div>
			<div class="clear"></div>							
		</div>
		
		<div class="form-group">
			<label for="location">{l s='Ubicación' mod='icrlocation'} </label>
			<div class="margin-form">
				<input class="location" type="text" name="location" id="location" maxlength="32" placeholder="A01.02.I.03.D" required>
				<sup>*</sup>	
				<p class="preference_description">{l s='Ubicación física dentro de la Bodega, teniendo en Cuenta: Bodega, Pasillo, Estante.Lado, Nivel. ej: A01.02.I.03.D' mod='icrlocation'}</p>
			</div>
			<div class="clear"></div>
		</div>

		<div class="form-group icr-group">
			<label>{l s='ICR' mod='icrlocation'} </label>
			<div class="margin-form">
				<textarea class="icr" name="icr" rows="6" cols="12" required></textarea>
				<sup>*</sup>
				<p class="preference_description">{l s='Ingrese códigos ICR con salto de línea:' mod='icrlocation'}</p>
				<p class="preference_description">ABC012</p>
				<p class="preference_description">NNN999</p>
			</div>
			<div class="clear"></div>
		</div>

		<div class="margin-form">
		<button type="submit" class="btn btn-default" name="btn-submit">{l s='Enviar' mod='icrlocation'}</button>
		</div>
	</fieldset>
</form>