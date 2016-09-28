<?php
class IdentityController extends IdentityControllerCore
{
	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		FrontController::initContent();

		if ($this->customer->birthday)
			$birthday = explode('-', $this->customer->birthday);
		else
			$birthday = array('-', '-', '-');

		/* Generate years, months and days */
                $prog_apego = new ProgramaApego();
                $name_Apego_active = $prog_apego->getNameProgApegoActive();
//                $id_prog_apego = $prog_apego->getIdProgApegoFromName('Prueba');
                $id_customer = Context::getContext()->customer->id;
                echo "Los programas apego activos son:  ";
                var_dump($name_Apego_active);
//                var_dump($name_Apego_active);
//                echo "<br>El id de customer es:  ";
//                var_dump($id_customer);
//                $access_value = $prog_apego->getAccesValueFromApegoCustomer( (int)$id_prog_apego, (int)$id_customer );
//                echo "<br>El valor de acceso es:  ";
//                var_dump($access_value);
//                exit(0);
		$this->context->smarty->assign(array(
				'years' => Tools::dateYears(),
				'sl_year' => $birthday[0],
				'months' => Tools::dateMonths(),
				'sl_month' => $birthday[1],
				'days' => Tools::dateDays(),
				'sl_day' => $birthday[2],
				'errors' => $this->errors,
				'genders' => Gender::getGenders(),
				'rfc' => Utilities::hasRfc(Context::getContext()->customer->id),
                                'access_value' => $access_value,
			));

		$this->context->smarty->assign('newsletter', (int)Module::getInstanceByName('blocknewsletter')->active);

		$this->setTemplate(_PS_THEME_DIR_.'identity.tpl');
	}
}
?>