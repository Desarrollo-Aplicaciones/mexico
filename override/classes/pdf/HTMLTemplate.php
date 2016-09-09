<?php

abstract class HTMLTemplate extends HTMLTemplateCore
{
	/**
	 * Returns the template's HTML header
	 * @return string HTML header
	 */
	public function getHeader()
	{
		$shop_name = Configuration::get('PS_SHOP_NAME', null, null, (int)$this->order->id_shop);
		$path_logo = $this->getLogo();

		$width = 0;
		$height = 0;
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);

		$this->smarty->assign(array(
			'logo_path' => $path_logo,
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'title' => $this->title,
			'date' => $this->date,
			'shop_name' => $shop_name,
			'width_logo' => $width,
			'height_logo' => $height,
			'img_physical_uri' => _PS_IMG_DIR_,
			'id_order_footer' => $this->order->id,
		));

		return $this->smarty->fetch($this->getTemplate('header'));
	}
}	