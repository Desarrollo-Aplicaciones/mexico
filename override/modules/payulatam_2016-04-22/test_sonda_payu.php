<?php
include(realpath(dirname(__FILE__).'/SondaPayu.php'));

// instancia el objeto link en el contexto si no viene inicializado
if (empty(Context::getContext()->link))
	Context::getContext()->link = new Link();

$sonda = new SondaPayu();
//$sonda->updatePendyngOrdesConfirmation();
//$sonda->updatePendyngOrdes();
$response = null;
$payu_id = '';
if($_POST['payu_id']){
	$payu_id = $_POST['payu_id'];
	$response = $sonda->getByOrderId($payu_id);	
}

?>
<form method="POST">
	<table>
		<tr>
			<td></td>
			<td></td>
				</tr>
		<tr>
			<td>payu_id</td>
			<td><input type="text" name="payu_id" value="<?php echo $payu_id; ?>"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Enviar"></td>
		</tr>
	</table>
</form>

<?php echo ('<pre>'.print_r($response,true).'</pre>'); ?>