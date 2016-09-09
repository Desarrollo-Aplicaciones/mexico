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
            if ( Tools::getValue('icr') && ( Tools::getValue('batch') || ( Tools::getValue('Month') && Tools::getValue('Year') && Tools::getValue('Day')) || Tools::getValue('regsani') || Tools::getValue('fechanoaplica') ) )
            {
                $vence = Tools::getValue('Year')."-".Tools::getValue('Month')."-".Tools::getValue('Day');
                $sql = 'SELECT id_icr AS id FROM '._DB_PREFIX_.'icr WHERE cod_icr="'.Tools::getValue('icr').'" AND id_estado_icr IN (2,3,9) ;';
                $idicr = Db::getInstance()->executeS($sql);
                $correcto_date = 1;
                if ($idicr)
                {
                    $idicr = $idicr[0]["id"];
                    $sql2 = 'UPDATE '._DB_PREFIX_.'supply_order_icr
                            SET ';

                    if ( Tools::getValue('batch') ) {
                        $sql2 .= ' lote="'.Tools::getValue('batch').'",';
                    }

                    if( Tools::getValue('fechanoaplica') ) {
                        $vence = ' No Aplica ';
                        $sql2 .= ' fecha_vencimiento = "1969-12-31",';
                        
                    } elseif ( ( Tools::getValue('Year') && Tools::getValue('Month') && Tools::getValue('Day') ) || ( Tools::getValue('Year') && Tools::getValue('Month') ) ) {
                        
                        if ( strlen(Tools::getValue('Day') ) == 0  ) {
                            $dia_s = '01';
                            $vence .= $dia_s;
                        } elseif ( strlen(Tools::getValue('Day') ) == 1  ) {
                            $dia_s = '0'.Tools::getValue('Day');
                        } else {
                            $dia_s = Tools::getValue('Day');
                        }

                        if ( checkdate(Tools::getValue('Month'), $dia_s, Tools::getValue('Year') ) ) {
                            $sql2 .= ' fecha_vencimiento = "'.Tools::getValue('Year')."-".Tools::getValue('Month')."-".$dia_s.'",';
                        } else {
                            $correcto_date = 0;
                            $output .= $this->displayError("Error en la fecha ingresada, no es correcta - ".Tools::getValue('Year')."-".Tools::getValue('Month')."-".$dia_s);
                            return $output.$this->displayForm();
                        }

                         
                    }

                    /*if ( Tools::getValue('regsani') ) {
                         $sql2 .= ' registro_sanitario="'.Tools::getValue('regsani').'",';
                    }*/
                    
                    $sql2 = trim($sql2, ",");

                    $sql2 .= ' WHERE id_icr = '.$idicr;

                    if($correcto_date == 1 && Db::getInstance()->execute($sql2))
                    {
                        $output .= $this->displayConfirmation(
                        "Producto con ICR ".Tools::getValue('icr').
                        " actualizado ==> Lote: ".Tools::getValue('batch').
                        " - Fecha: ".$vence.
                        " - Registro Sanitario Invima: ".Tools::getValue('regsani'));
                    }
                    else{
                        $output .= $this->displayError("Error en el acceso a la base de datos<br> 2 ".$sql2." - ");
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
