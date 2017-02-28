<?php
class OrderInvoice extends OrderInvoiceCore
{
	public function getProductsDetail()
	{
		$query = '
		SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product` p
		ON p.id_product = od.product_id
		LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
		WHERE od.`id_order` = '.(int)$this->id_order;
		if(isset($this->id) && $this->id){
			$query .= '
			AND od.`id_order_invoice` = '.(int)$this->id;
		}
		
                error_log("\n\n\n\n\n\t****** Este es el query: ******\n\n".$query."\n\n\n\n\n\n\n\n\n",3,"/tmp/progresivo.log");
                return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        }
}
?>