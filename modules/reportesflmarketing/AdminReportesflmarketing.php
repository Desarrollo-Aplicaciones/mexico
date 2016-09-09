<?php
	class AdminReportesflmarketing extends ModuleAdminController
	{
		public function initContent() {
			global $cookie;
			$token=md5(pSQL(_COOKIE_KEY_.'AdminModules'.(int)Tab::getIdFromClassName('AdminModules').(int)$cookie->id_employee));
			header('Location: index.php?controller=AdminModules&token='.$token.'&configure=reportesflmarketing');
			exit;
		}
	}