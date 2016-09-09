<?php

final class Tools
{
	
	private $_taxCalculationMethod = PS_TAX_EXC;
	
	/**
	 * To Calculate the Product Price based on Tax
	 * $$row			- 	Resultset
	 */
 	public function calculateProductPricesWithTax(&$row)
	{
		
		if ($this->_taxCalculationMethod == PS_TAX_EXC)
			$row['product_price'] = $this->ps_round($row['product_price'], 2);
		else
			$row['product_price_wt'] = $this->ps_round($row['product_price'] * (1 + $row['tax_rate'] / 100), 2);

		$group_reduction = 1;
		if ($row['group_reduction'] > 0)
			$group_reduction =  1 - $row['group_reduction'] / 100;

		if ($row['reduction_percent'] != 0)
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = ($row['product_price'] - $row['product_price'] * ($row['reduction_percent'] * 0.01));
			else
			{
				$reduction = $this->ps_round($row['product_price_wt'] * ($row['reduction_percent'] * 0.01), 2);
				$row['product_price_wt'] = $this->ps_round(($row['product_price_wt'] - $reduction), 2);
			}
		}

		if ($row['reduction_amount'] != 0)
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = ($row['product_price'] - ($row['reduction_amount'] / (1 + $row['tax_rate'] / 100)));
			else
				$row['product_price_wt'] = $this->ps_round(($row['product_price_wt'] - $row['reduction_amount']), 2);
		}
		
		if ($row['group_reduction'] > 0)
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = $row['product_price'] * $group_reduction; 
			else
				$row['product_price_wt'] = $this->ps_round($row['product_price_wt'] * $group_reduction , 2); 
		}

		if (($row['reduction_percent'] OR $row['reduction_amount'] OR $row['group_reduction']) AND $this->_taxCalculationMethod == PS_TAX_EXC)
			$row['product_price'] = $this->ps_round($row['product_price'], 2);

		if ($this->_taxCalculationMethod == PS_TAX_EXC)
			$row['product_price_wt'] = $this->ps_round($row['product_price'] * (1 + ($row['tax_rate'] * 0.01)), 2) + $this->ps_round($row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
		else
		{
			$row['product_price_wt_but_ecotax'] = $row['product_price_wt'];
			$row['product_price_wt'] = $this->ps_round($row['product_price_wt'] + $row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
		}

        $row['total_wt'] = $row['product_quantity'] * $row['product_price_wt'];
		$row['total_price'] = $row['product_quantity'] * $row['product_price'];

	}
	
	
 	public static function ps_round($value, $precision = 0)
	{
		global $config;
		
		$method = (int)($config->get('PS_PRICE_ROUND_MODE'));
		if ($method == PS_ROUND_UP)
			return self::ceilf($value, $precision);
		elseif ($method == PS_ROUND_DOWN)
			return self::floorf($value, $precision);
		return round($value, $precision);
	}

	public static function ceilf($value, $precision = 0)
	{
		$precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precisionFactor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;
		return ceil($tmp) / $precisionFactor;
	}

	public static function floorf($value, $precision = 0)
	{
		$precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precisionFactor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;
		return floor($tmp) / $precisionFactor;
	}
}