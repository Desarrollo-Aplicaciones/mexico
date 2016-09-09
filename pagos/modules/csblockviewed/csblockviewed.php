<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class CsBlockViewed extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'csblockviewed';
		$this->tab = 'other';
		$this->version = 0.9;
		$this->author = 'codespot';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('CS Viewed products block.');
		$this->description = $this->l('Adds a block displaying recently viewed products.');
	}

	public function install()
	{
		if (!parent::install()
			|| !$this->registerHook('leftColumn')
			|| !$this->registerHook('header')
			|| !Configuration::updateValue('PRODUCTS_VIEWED_NBR', 2))
			return false;
		return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockViewed'))
		{
			if (!($productNbr = Tools::getValue('productNbr')) || empty($productNbr))
				$output .= '<div class="alert error">'.$this->l('You must fill in the \'Products displayed\' field.').'</div>';
			elseif ((int)($productNbr) == 0)
				$output .= '<div class="alert error">'.$this->l('Invalid number.').'</div>';
			else
			{
				Configuration::updateValue('PRODUCTS_VIEWED_NBR', (int)$productNbr);
				$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Products displayed').'</label>
				<div class="margin-form">
					<input type="text" name="productNbr" value="'.(int)Configuration::get('PRODUCTS_VIEWED_NBR').'" />
					<p class="clear">'.$this->l('Define the number of products displayed in this block.').'</p>
				</div>
				<center><input type="submit" name="submitBlockViewed" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	public function hookRightColumn($params)
	{
		$id_product = (int)Tools::getValue('id_product');
		$productsViewed = (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) ? array_slice(explode(',', $params['cookie']->viewed), 0, Configuration::get('PRODUCTS_VIEWED_NBR')) : array();

		if (count($productsViewed))
		{
			$defaultCover = Language::getIsoById($params['cookie']->id_lang).'-default';

			$productIds = implode(',', $productsViewed);
			$productsImages = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT image_shop.id_image, p.id_product,p.out_of_stock, il.legend, product_shop.active, pl.name, pl.description_short, pl.link_rewrite, cl.link_rewrite AS category_rewrite, p.id_category_default ,p.ean13 
			FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').')
			LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
			LEFT JOIN '._DB_PREFIX_.'image_lang il ON (il.id_image = i.id_image)
			'.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
			WHERE p.id_product IN ('.$productIds.')
			AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')
			AND pl.id_lang = '.(int)($params['cookie']->id_lang).'
			AND cl.id_lang = '.(int)($params['cookie']->id_lang)
			);
			
			$productsImagesArray = array();
			foreach ($productsImages as $pi)
				$productsImagesArray[$pi['id_product']] = $pi;

			$productsViewedObj = Product::getProductsProperties((int)($params['cookie']->id_lang), $productsImagesArray);


			if ($id_product && !in_array($id_product, $productsViewed))
			{
				// Check if the user to the right of access to this product
				$product = new Product((int)$id_product);
				if ($product->checkAccess((int)$this->context->customer->id))
					array_unshift($productsViewed, $id_product);
			}
			$viewed = '';
			foreach ($productsViewed as $id_product_viewed)
				$viewed .= (int)($id_product_viewed).',';
			$params['cookie']->viewed = rtrim($viewed, ',');

			if (!count($productsViewedObj))
				return;

			$this->smarty->assign(array(
				'productsViewedObj' => $productsViewedObj,
				'mediumSize' => Image::getSize('medium')));

			return $this->display(__FILE__, 'blockviewed.tpl');
		}
		elseif ($id_product)
			$params['cookie']->viewed = (int)($id_product);
		return;
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		global $smarty;
		if ($smarty->tpl_vars['page_name']->value != 'index')
		{
			$this->context->controller->addCSS(($this->_path).'blockviewed.css', 'all');
		}
	}
}
