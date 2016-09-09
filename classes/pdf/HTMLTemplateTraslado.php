<?php


class HTMLTemplateTrasladoCore extends HTMLTemplate
{
public $order;
public $header_info;

	public function __construct($info_fecha, $smarty) {

		$this->smarty = $smarty;

		// header informations
		$this->header_info = $info_fecha;
		$this->title = "Información de traslado del ".$info_fecha['date'];		

	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent() {

		$query_info_body = "SELECT 
			GROUP_CONCAT(
			DISTINCT CONCAT(i.cod_icr,': ',soi.fecha_vencimiento) 
			  ORDER BY i.cod_icr
			  SEPARATOR ' | '
			) AS icrs, sod.reference, 
			sod.`name` product
			FROM ps_supply_order_icr_history soih
			LEFT JOIN ps_icr i ON ( soih.id_icr = i.id_icr )
			LEFT JOIN ps_supply_order_icr soi ON ( i.id_icr = soi.id_icr )
			LEFT JOIN ps_supply_order_detail sod ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
			WHERE soih.date = '".$this->header_info['date']."'
			GROUP BY soih.date, sod.id_product";

		$info_body = Db::getInstance()->executeS($query_info_body);

		$this->smarty->assign(array(
			'header_inf' => $this->header_info,
			'info_body' => $info_body
		));

		return $this->smarty->fetch($this->getTemplate('traslado'));
	}




	/**
	 * @see HTMLTemplate::getHeader()
	 */
	public function getHeader()
	{
		$shop_name = Configuration::get('PS_SHOP_NAME');
		$path_logo = $this->getLogo();
		$width = $height = 0;
		
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);
		
		$this->smarty->assign(array(
			'logo_path' => $path_logo,
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'reference' => 'Comprobante traslado',
			'title' => $this->title,
			'date' => $this->date,
			'shop_name' => $shop_name,
			'img_physical_uri' => _PS_IMG_DIR_,
			'width_logo' => $width,
			'height_logo' => $height,
			'header_inf' => $this->header_info
		));

		return $this->smarty->fetch($this->getTemplate('header-traslado'));
	}

	/**
	 * @see HTMLTemplate::getFooter()
	 */
	public function getFooter()
	{
		return false;
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'traslado.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename() {		
		$date=date_create($this->date);		
		return "ComprobanteTraslado_".date_format($date,"YmdHis").'.pdf';
	}
}