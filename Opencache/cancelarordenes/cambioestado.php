<?php

	if( isset($_POST) && isset($_POST['ordencita']) && $_POST['ordencita'] != '') {


		require_once(dirname(__FILE__).'/../../config/config.inc.php');

		$utils_orderr = new Utilities();

		$resultado = $utils_orderr->ChangeStateOrderIcr((int)$_POST['ordencita']);

		echo "<pre> Res: ";
		print_r($resultado);
	} else {
		echo "<br> Nada Enviado";
	}

?>

<html>
	<head>
		<body>
			<form action="" method="post">
				<input type="hidden" name="ordencita" size="6" maxlength="5">
				<input type="submit" name="dela mono" value="dele mono">
			</form>
		</body>
	</head>	
</html>