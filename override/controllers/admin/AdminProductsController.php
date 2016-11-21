<?php

class AdminProductsController extends AdminProductsControllerCore
{
	public function __construct()
	{
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;
		$this->explicitSelect = true;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		if (!Tools::getValue('id_product'))
			$this->multishop_context_group = false;

		AdminController::__construct();

		$this->imageType = 'jpg';
		$this->_defaultOrderBy = 'position';
		$this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
		$this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
		$this->allow_export = true;

		// @since 1.5 : translations for tabs
		$this->available_tabs_lang = array(
			'Informations' => $this->l('Information'),
			'Pack' => $this->l('Pack'),
			'VirtualProduct' => $this->l('Virtual Product'),
			'Prices' => $this->l('Prices'),
			'Seo' => $this->l('SEO'),
			'Images' => $this->l('Images'),
			'Associations' => $this->l('Associations'),
			'Shipping' => $this->l('Shipping'),
			'Combinations' => $this->l('Combinations'),
			'Features' => $this->l('Features'),
			'Customization' => $this->l('Customization'),
			'Attachments' => $this->l('Attachments'),
			'Quantities' => $this->l('Quantities'),
			'Suppliers' => $this->l('Suppliers'),
			'Warehouses' => $this->l('Warehouses'),
		);

		$this->available_tabs = array('Quantities' => 6, 'Warehouses' => 14);
		if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP)
			$this->available_tabs = array_merge($this->available_tabs, array(
				'Informations' => 0,
				'Pack' => 7,
				'VirtualProduct' => 8,
				'Prices' => 1,
				'Seo' => 2,
				'Associations' => 3,
				'Images' => 9,
				'Shipping' => 4,
				'Combinations' => 5,
				'Features' => 10,
				'Customization' => 11,
				'Attachments' => 12,
				'Suppliers' => 13,
			));

		// Sort the tabs that need to be preloaded by their priority number
		asort($this->available_tabs, SORT_NUMERIC);

		/* Adding tab if modules are hooked */
		$modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
		if (is_array($modules_list) && count($modules_list) > 0)
			foreach ($modules_list as $m)
			{
				$this->available_tabs['Module'.ucfirst($m['module'])] = 23;
				$this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
			}

		if (Tools::getValue('reset_filter_category'))
			$this->context->cookie->id_category_products_filter = false;
		if (Shop::isFeatureActive() && $this->context->cookie->id_category_products_filter)
		{
			$category = new Category((int)$this->context->cookie->id_category_products_filter);
			if (!$category->inShop())
			{
				$this->context->cookie->id_category_products_filter = false;
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
			}
		}
		/* Join categories table */
		if ($id_category = (int)Tools::getValue('productFilter_cl!name'))
		{
			$this->_category = new Category((int)$id_category);
			$_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
		}
		else
		{
			if ($id_category = (int)Tools::getValue('id_category'))
			{
				$this->id_current_category = $id_category;
				$this->context->cookie->id_category_products_filter = $id_category;	
			}
			elseif ($id_category = $this->context->cookie->id_category_products_filter)
				$this->id_current_category = $id_category;
			if ($this->id_current_category)
				$this->_category = new Category((int)$this->id_current_category);
			else
				$this->_category = new Category();
		}
			
		$join_category = false;
		if (Validate::isLoadedObject($this->_category) && empty($this->_filter))
			$join_category = true;

		//SELECT  @rownum := @rownum + 1 AS rank, id_product from ps_product, ( select @rownum :=0 ) as pepe LIMIT 15;

		$this->_join .= '
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = a.`id_product`) 
		LEFT JOIN `'._DB_PREFIX_.'stock_available_mv` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
		'.StockAvailable::addSqlShopRestriction(null, null, 'sav').') ';

		$alias = 'sa';
		$alias_image = 'image_shop';
				
		$id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'a.id_shop_default';
		$this->_join .= ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = '.$id_shop.') 
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$id_shop.')';
		
		$this->_select .= 'shop.name as shopname, ';
		$this->_select .= 'MAX('.$alias_image.'.id_image) id_image, cl.name `name_category`, '.$alias.'.`price`, 0 AS price_final, sav.`quantity` as sav_quantity, '.$alias.'.`active`';
		
		if ($join_category)
		{
			$this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.') ';
			$this->_select .= ' , cp.`position`, ';
		}

		$this->_group = 'GROUP BY '.$alias.'.id_product';

		$this->fields_list = array();
		$this->fields_list['id_product'] = array(
			'title' => $this->l('ID'),
			'align' => 'center',
			'type' => 'int',
			'width' => 40
		);
		$this->fields_list['image'] = array(
			'title' => $this->l('Photo'),
			'align' => 'center',
			'image' => 'p',
			'width' => 70,
			'orderby' => false,
			'filter' => false,
			'search' => false
		);
		$this->fields_list['name'] = array(
			'title' => $this->l('Name'),
			'filter_key' => 'b!name'
		);
		$this->fields_list['reference'] = array(
			'title' => $this->l('Reference'),
			'align' => 'left',
			'width' => 80
		);

		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
			$this->fields_list['shopname'] = array(
				'title' => $this->l('Default shop:'),
				'width' => 230,
				'filter_key' => 'shop!name',
			);
		else
			$this->fields_list['name_category'] = array(
				'title' => $this->l('Category'),
				'width' => 'auto',
				'filter_key' => 'cl!name',
			);
		$this->fields_list['price'] = array(
			'title' => $this->l('Base price'),
			'width' => 90,
			'type' => 'price',
			'align' => 'right',
			'filter_key' => 'a!price'
		);
		$this->fields_list['price_final'] = array(
			'title' => $this->l('Final price'),
			'width' => 90,
			'type' => 'price',
			'align' => 'right',
			'havingFilter' => true,
			'orderby' => false
		);
		if (Configuration::get('PS_STOCK_MANAGEMENT'))
			$this->fields_list['sav_quantity'] = array(
				'title' => $this->l('Quantity'),
				'width' => 90,
				'type' => 'int',
				'align' => 'right',
				'filter_key' => 'sav!quantity',
				'orderby' => true,
				'hint' => $this->l('This is the quantity available in the current shop/group.'),
			);
		$this->fields_list['active'] = array(
			'title' => $this->l('Status'),
			'width' => 70,
			'active' => 'status',
			'filter_key' => $alias.'!active',
			'align' => 'center',
			'type' => 'bool',
			'orderby' => false
		);

		if ($join_category && (int)$this->id_current_category)
			$this->fields_list['position'] = array(
				'title' => $this->l('Position'),
				'width' => 70,
				'filter_key' => 'cp!position',
				'align' => 'center',
				'position' => 'position'
			);
	}

	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
	{
		if( isset($_REQUEST) && isset($_REQUEST['productOrderby']) && $_REQUEST['productOrderby'] == 'quantity')  {
			$_REQUEST['productOrderby'] = 'sav!quantity';
			$orderBy = 'sav!quantity';
		}

		$orderByPriceFinal = (empty($orderBy) ? ($this->context->cookie->__get($this->table.'Orderby') ? $this->context->cookie->__get($this->table.'Orderby') : 'id_'.$this->table) : $orderBy);
		$orderWayPriceFinal = (empty($orderWay) ? ($this->context->cookie->__get($this->table.'Orderway') ? $this->context->cookie->__get($this->table.'Orderby') : 'ASC') : $orderWay);
		if ($orderByPriceFinal == 'price_final')
		{
			$orderBy = 'id_'.$this->table;
			$orderWay = 'ASC';
		}
		AdminController::getList($id_lang, $orderBy, $orderWay, $start, $limit, $this->context->shop->id);

		/* update product quantity with attributes ...*/
		$nb = count($this->_list);
		if ($this->_list)
		{
			/* update product final price */
			for ($i = 0; $i < $nb; $i++)
			{
				// convert price with the currency from context
				$this->_list[$i]['price'] = Tools::convertPrice($this->_list[$i]['price'], $this->context->currency, true, $this->context);
				$this->_list[$i]['price_tmp'] = Product::getPriceStatic($this->_list[$i]['id_product'], true, null, 2, null, false, true, 1, true);
			}
		}

		if ($orderByPriceFinal == 'price_final')
		{
			if (strtolower($orderWayPriceFinal) == 'desc')
				uasort($this->_list, 'cmpPriceDesc');
			else
				uasort($this->_list, 'cmpPriceAsc');
		}
		for ($i = 0; $this->_list && $i < $nb; $i++)
		{
			$this->_list[$i]['price_final'] = $this->_list[$i]['price_tmp'];
			unset($this->_list[$i]['price_tmp']);
		}
	}

	/* @todo rename to processaddproductimage */
	public function ajaxProcessAddImage()
	{
		self::$currentIndex = 'index.php?tab=AdminProducts';
		$allowedExtensions = array('jpeg', 'gif', 'png', 'jpg');
		// max file size in bytes
		$uploader = new FileUploader($allowedExtensions, $this->max_image_size);
		$result = $uploader->handleUpload();
		if (isset($result['success']))
		{

			$obj = new Image((int)$result['success']['id_image'], Tools::getValue('alt'), Tools::getValue('title'));
			// Associate image to shop from context
			$shops = Shop::getContextListShopID();
			$obj->associateTo($shops);
			$json_shops = array();
			foreach ($shops as $id_shop)
				$json_shops[$id_shop] = true;

			$json = array(
				'name' => $result['success']['name'],
				'status' => 'ok',
				'id'=>$obj->id,
				'path' => $obj->getExistingImgPath(),
				'position' => $obj->position,
				'cover' => $obj->cover,
				'shops' => $json_shops,
			);

			// Sube las imágenes en AWS S3
			$path = _PS_PROD_IMG_DIR_ . $obj->getImgFolder();
			$files = array_diff(scandir($path), array('.', '..'));
			$awsObj = new Aws();
			foreach($files as $img) {
				if (!$awsObj->setObjectImage($path . $img, $img, 'p/')) {
					die(Tools::jsonEncode(array(
						'error' => "Error al subir la imagen $img a AWS S3, por favor contactar a IT."
					)));
				}
			}
			// Elimina las imágenes del local
			$this->deleteDirectory($path);

			@unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$obj->id_product.'.jpg');
			@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$obj->id_product.'_'.$this->context->shop->id.'.jpg');
			die(Tools::jsonEncode($json));
		}
		else
			die(Tools::jsonEncode($result));
	}

	/**
	 * Remove a directory not empty
	 * @see http://stackoverflow.com/questions/1653771/how-do-i-remove-a-directory-that-is-not-empty
	 */
	public function deleteDirectory($dir) {
		system('rm -rf ' . escapeshellarg($dir), $retval);
		return $retval == 0; // UNIX commands return zero on success
	}
}