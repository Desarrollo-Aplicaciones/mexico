<div>
	<p><b>{l s='Gestión de códigos ICR' mod='generacodigo'}</b></p>
	<fieldset>
		<legend>
			<img src="{$module_template_dir}icon/config.gif" alt="" title="" />{l s='Configuración ICRs' mod='generacodigo'}
		</legend>
		<form enctype="multipart/form-data" method="post">
			<div class="opciones">
				<input type="radio" name="botonGrupo" id="botonesGrupo4" value="ReporteLibres" checked>
				Reporte de codigos sin asignar.
			</div>
			<div class="opciones">
				<input type="radio" name="botonGrupo" id="botonesGrupo1" value="generarCodigo" onkeyup="ValidaSoloNumeros(event)">
				Generar Nuevos ICR's
			</div>
			<div class="opciones">
				<input type="radio" name="botonGrupo" id="botonesGrupo2" value="buscarCodigo" >
				Anular codigo ICR
			</div>
			<div class="opciones">
				<input type="radio" name="botonGrupo" id="botonesGrupo3" value="buscarReporte" >
				Reporte de codigos ICR.
			</div>
			<div class="campos">
				<div id="Grupo1"  style="display:none;">
					Ingrese la cantidad de códigos ICR a generar
					<input name="cantidad" type="text" onkeypress="ValidaSoloNumeros(event)"/>
				</div>
				<div id="Grupo2" style="display:none;">
					Ingrese el código ICR que desea anular
					<input name="buscar" type="text" onkeypress="ValidaLetrasNumeros(event)" id="buscar">
				</div>
			</div>
			<div class="enviar"><input type="submit" name="submitUpdateCod" id="botonValida" value="Aceptar" class="button"/></div>
		</form>
	</fieldset>
</div>
<style type="text/css" >
	.opciones {
		float: left;
		margin: 15px;
	}
	.enviar{
		float:left;
		width:100%;
		text-align: center;
	}
	.campos{
		float:left;
		width:100%;
	}
</style>
<script type='text/javascript'>
	$(document).ready(function()
	{
		$('#botonValida').click(function () {
			var variableValida = ($('input:radio[name=botonGrupo]:checked').val());
			$('#generar').submit();
		});
	});
	function ocultar(){
		$('#Grupo1').hide("slow");
		$('#Grupo2').hide("slow");
	}
	$('#botonesGrupo1').click(function(){
		ocultar();
		$('#Grupo1').show("slow");
	})
	$('#botonesGrupo2').click(function(){
		ocultar();
		$('#Grupo2').show("slow");
	})
	$('#botonesGrupo3').click(function(){
		ocultar();
	})
	$('#botonesGrupo4').click(function(){
		ocultar();
	})
	function validaRadio(){
		opciones = document.getElementsByName('generacion');
		var seleccionado = false;
		for(var i=0; i<opciones.length; i++) {
			if(opciones[i].checked) {
				seleccionado = true;
				break;
			}
		}
		if(!seleccionado) {
			return false;
		}
	}
	function ValidaSoloNumeros(event) {
		var code =event.charCode || event.keyCode;
		if (code != 8 && code != 37 && code != 39 && code != 46 && code != 13 && ((code< 48) || (code> 57))){
			if(window.event){
				event.returnValue = false;
			}else{
				event.preventDefault();
			}
		}
	}
	function ValidaLetrasNumeros(event) {
		var code =event.charCode || event.keyCode;
		if (code != 8 && code != 37 && code != 39 && code != 46 && code != 13 && ((code< 48) || (code> 57) && (code< 65) || (code> 90) && (code< 97) || (code> 122))){
			if(window.event){
				event.returnValue = false;
			}else{
				event.preventDefault();
			}
		}
	}
</script>