<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class PDF extends PDFCore
{

	const TEMPLATE_TRASLADO = 'Traslado';

	public function render($display = true, $back_cancel = false)
	{
		/*echo "<pre>objetos: ";
		print_r($this->objects);
		echo "<br>objetos<hr>";*/
		$render = false;
		$this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
		foreach ($this->objects as $object)
		{

			$template = $this->getTemplateObject($object);

			if (!$template) {
				continue;
			}

			if (empty($this->filename))
			{
				$this->filename = $template->getFilename();
				
				if (count($this->objects) > 1)
					$this->filename = $template->getBulkFilename();
			}

			$template->assignHookData($object);

			$this->pdf_renderer->createHeader($template->getHeader());
			$this->pdf_renderer->createFooter($template->getFooter());
			$this->pdf_renderer->createContent($template->getContent());
			$this->pdf_renderer->writePage($back_cancel);
			$render = true;

			unset($template);
		}

		if ($render)
		{
			// clean the output buffer
			if (ob_get_level() && ob_get_length() > 0)
				ob_clean();
			return $this->pdf_renderer->render($this->filename, $display);
		}
	}


const TEMPLATE_SUPPLY_ORDER_RECEIPT = 'SupplyOrderReceipt';

	public function renderSupplyOrderForm($display = true)
	{
		$this->pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'));
		$render = false;
		$this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
		foreach ($this->objects as $object)
		{
			$template = $this->getTemplateObject($object);
			if (!$template)
				continue;

			if (empty($this->filename))
			{
				$this->filename = $template->getFilename();
				if (count($this->objects) > 1)
					$this->filename = $template->getBulkFilename();
			}

			$template->assignHookData($object);

			$this->pdf_renderer->createHeader($template->getHeader());
			$this->pdf_renderer->createFooter($template->getFooter());
			$this->pdf_renderer->createContent($template->getContent());
			$this->pdf_renderer->SetHeaderMargin(5);
			$this->pdf_renderer->SetFooterMargin(20);
			$this->pdf_renderer->setMargins(10, 30, 10);
			$this->pdf_renderer->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
			$this->pdf_renderer->AddPage();
			$this->pdf_renderer->writeHTML($this->pdf_renderer->content, true, false, true, false, '');
			$render = true;

			unset($template);
		}

		if ($render)
		{
			// clean the output buffer
			if (ob_get_level() && ob_get_length() > 0)
				ob_clean();
			return $this->pdf_renderer->render($this->filename, $display);
		}
	}
	
	public function renderSupplyOrderReceipt($display = true)
	{
		$this->pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'));
		$render = false;
		$this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
		foreach ($this->objects as $object)
		{
			$template = $this->getTemplateObject($object);
			if (!$template)
				continue;

			if (empty($this->filename))
			{
				$this->filename = $template->getFilename();
				if (count($this->objects) > 1)
					$this->filename = $template->getBulkFilename();
			}

			$template->assignHookData($object);

			$this->pdf_renderer->createHeader($template->getHeader());
			$this->pdf_renderer->createFooter($template->getFooter());
			$this->pdf_renderer->createContent($template->getContent());
			$this->pdf_renderer->SetHeaderMargin(5);
			$this->pdf_renderer->SetFooterMargin(20);
			$this->pdf_renderer->setMargins(10, 30, 10);
			$this->pdf_renderer->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
			$this->pdf_renderer->AddPage();
			$this->pdf_renderer->writeHTML($this->pdf_renderer->content, true, false, true, false, '');
			$render = true;

			unset($template);
		}

		if ($render)
		{
			// clean the output buffer
			if (ob_get_level() && ob_get_length() > 0)
				ob_clean();
			return $this->pdf_renderer->render($this->filename, $display);
		}
	}


	public function renderTrasladoForm($display = true)
	{
		$this->pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'));
		$render = false;
		$this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
		foreach ($this->objects as $object)
		{
			$template = $this->getTemplateObject($object);
			if (!$template)
				continue;

			if (empty($this->filename))
			{
				$this->filename = $template->getFilename();
				if (count($this->objects) > 1)
					$this->filename = $template->getBulkFilename();
			}

			$template->assignHookData($object);

			$this->pdf_renderer->createHeader($template->getHeader());
			$this->pdf_renderer->createFooter($template->getFooter());
			$this->pdf_renderer->createContent($template->getContent());
			$this->pdf_renderer->SetHeaderMargin(5);
			$this->pdf_renderer->SetFooterMargin(20);
			$this->pdf_renderer->setMargins(10, 40, 10);
			$this->pdf_renderer->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
			$this->pdf_renderer->AddPage();
			$this->pdf_renderer->writeHTML($this->pdf_renderer->content, true, false, true, false, '');
			$render = true;

			unset($template);
		}

		if ($render)
		{
			// clean the output buffer
			if (ob_get_level() && ob_get_length() > 0)
				ob_clean();
			return $this->pdf_renderer->render($this->filename, $display);
		}
	}
}