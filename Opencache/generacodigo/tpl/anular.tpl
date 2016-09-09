<div>
	<p><b>{l s='Gestión de códigos ICR' mod='generacodigo'}</b></p>
	<fieldset>
		<legend>
			<img src="{$module_template_dir}icon/void.gif" alt="" title="" />{l s='Anular Código' mod='generacodigo'}
		</legend>
		<form enctype="multipart/form-data" method="post">
			<div>¿Desea cambiar el estado del código {$cod_icr} de activo a anulado?<br /><br />
			<table><tr>
				<td style="padding:0 30px;"><input type='hidden' name='cambiarEstado' value="{$id_icr}">
				<input type='submit' id='submita' name= 'submita' value='Anular' class="button"></label></td>
				<td style="padding:0 30px;"><a href='' class="button">Cancelar</a></td>
				</tr></table>
			</div>
			<br>
		</form>
	</fieldset>
</div>