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
			));

		$this->context->smarty->assign('newsletter', (int)Module::getInstanceByName('blocknewsletter')->active);

		$this->setTemplate(_PS_THEME_DIR_.'identity.tpl');
	}
}
?>