<?php

class Tax extends TaxCore
{
	public function getTaxIdByTaxPercent( $taxPercent )
	{
		$idTax = Db::getInstance()->executeS("SELECT id_tax FROM ps_tax WHERE rate = ".$taxPercent." AND active = 1");
		$idTax = $idTax[0];

		return $idTax;
	}
}