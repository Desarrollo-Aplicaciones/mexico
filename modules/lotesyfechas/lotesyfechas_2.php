<?php
/*
*  @author Esteban Rincón COrrea
*  @empresa Farmalisto.com
*/

if (!defined('_PS_VERSION_'))
	exit;

class LotesyFechas extends Module
{
    private $_html = '';
    private $_postErrors = array();
    private $_msg='';

    function __construct()
    {
        $this->name = 'lotesyfechas';
        $this->tab = 'shipping_logistics';
        $this->version = '0.1 Alfa';
        $this->author = 'Farmalisto - Esteban Rincón Correa';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Asignación de Lotes y fechas de vencimiento.');
        $this->description = $this->l('Asignación manual de Lotes y fechas de vencimiento por ICR de producto.');

    }

    public function install()
    {
        if (!$id_tab = Tab::getIdFromClassName('AdminBatchDate'))
        {
        $tab = new Tab();
        $tab->class_name = 'AdminBatchDate';
        $tab->module = 'lotesyfechas';
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminStock');
        foreach (Language::getLanguages(false) as $lang)
        $tab->name[(int)$lang['id_lang']] = 'Lotes y fechas de vencimiento';
        if (!$tab->save())
        return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
        }

        $this->_clearCache('updateprice.tpl');
        Configuration::updateValue('HOME_FEATURED_NBR', 8);

        if (!parent::install()) {   
            return false;
        }

        return true;
    }
	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}
    public function displayForm()
    {
        return $this->display(__FILE__, 'lotesyfechas.tpl', $this->getCacheId());
    }
	public function getContent()
    {
        $output = '<h2>'.$this->displayName.'</h2>';
        if (Tools::isSubmit('submitUpdateDate'))
        {
            if (Tools::getValue('icr') &&
                Tools::getValue('batch') &&
                Tools::getValue('Month') &&
                Tools::getValue('Year'))
            {
                $vence = Tools::getValue('Year')."-".Tools::getValue('Month');
                $sql = 'SELECT id_icr AS id FROM '._DB_PREFIX_.'icr WHERE cod_icr="'.Tools::getValue('icr').'";';
                $idicr = Db::getInstance()->executeS($sql);
                if ($idicr)
                {
                    $idicr = $idicr[0]["id"];
                    $sql2 = 'UPDATE '._DB_PREFIX_.'supply_order_icr
                            SET lote="'.Tools::getValue('batch').'", fecha_vencimiento="'.$vence.
                            '" WHERE id_icr = '.$idicr;
                    if(Db::getInstance()->execute($sql2))
                    {
                        $output .= $this->displayConfirmation(
                        "Producto ".Tools::getValue('icr').
                        " actualizado: lote ".Tools::getValue('batch').
                        " Fecha ".$vence);
                    }
                    else{
                        $output .= $this->displayError("Error en el acceso a la base de datos");
                    }
                }
                else{
                    $output .= $this->displayError("Icr Inexistente");
                }
            }
            else
            {
                $output .= $this->adminDisplayWarning("Falta por llenar un campo.");
            }
        }
        return $output.$this->displayForm();
    }
}
?>
