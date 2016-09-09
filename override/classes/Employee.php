<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer
 *
 * @author German.peralta
 */
class Employee extends EmployeeCore{
    
    
    public function getEmployeeById($id_employee = 0) {
        if ( $id_employee != null &&  $id_employee != 0 ) {
            return Db::getInstance()->executeS('
                SELECT  `firstname`, `lastname`
                FROM `'._DB_PREFIX_.'employee`
                WHERE `active` = 1
                AND `id_employee` = '.$id_employee.'
            '); 
    }
}

	/**
	 * Return list of employees
	 */
	public static function getEmployeesByProfileList($parametro)
	{
		$query ="SELECT emp.`id_employee`, emp.`firstname`, emp.`lastname`
				FROM `ps_employee` emp INNER JOIN ps_profile_lang prof ON(emp.id_profile = prof.id_profile )
				WHERE `active` = 1 "; 

		if(is_integer($parametro)){
			$query.=' AND prof.id_profile = '.$parametro;

		}elseif(is_string($parametro)){
			$query.=" AND prof.`name`='".$parametro."'";
		}
		$query.=' ORDER BY `lastname` ASC;';
		return Db::getInstance()->executeS($query);
	}

}
