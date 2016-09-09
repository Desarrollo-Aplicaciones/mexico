<?php

require_once(_PS_ROOT_DIR_.'/modules/generacodigo/GenerandoIcr.php'); //clase que se encarga de procesar el archivo cargado


//comprobación de una constante PHP
if ( !defined( '_PS_VERSION_' ) )
  exit;

class generacodigo extends Module
{
  public function __construct()
  {
    $this->name = 'generacodigo';
    $this->tab = 'Test';
    $this->version = '1.5 Alfa';
    $this->author = 'Farmalisto - Esteban Rincón Correa';
    $this->need_instance = 0;

    parent::__construct();

    $this->displayName = $this->l( 'Generar Codigo ICR' );
    $this->description = $this->l( 'Este codigo genera el cod de barras.' );
  }

  public function install()
  {

   if (!$id_tab = Tab::getIdFromClassName('generacod'))
   {
      $tab = new Tab();
    $tab->class_name = 'generacod';
    $tab->module = 'generacodigo';
    $tab->id_parent = (int)Tab::getIdFromClassName('AdminStock');
    foreach (Language::getLanguages(false) as $lang)
    $tab->name[(int)$lang['id_lang']] = 'Gestión de codigos ICR';
  if (!$tab->save())
    return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
  }
  $this->_clearCache('generacodigo.tpl');
  Configuration::updateValue('HOME_FEATURED_NBR', 8);

  if (!parent::install()) { 
    return false;
  }
  return true;
  }

  public function displayForm()
  {
    return $this->display(__FILE__, 'tpl/generacodigo.tpl');
  }

  public function getContent()
  {

    $output = '<h4>'.$this->displayName.'</h4>';
    if (Tools::isSubmit('submitUpdateCod'))
    {

      if( Tools::getValue('botonGrupo') == "generarCodigo"){          
        if (Tools::getValue('cantidad') == "") 
        { 
          $output .= $this->displayError("Cantidad a generar inválida");
        } 
        else {
          $cantidad = Tools::getValue('cantidad');
          $ultimo_generado = GenerandoIcr::campostabla();
          $result = GenerandoIcr::generandocodigo($cantidad, $ultimo_generado);
          if(isset($result['error'])){
            $output .= $this->displayError($result['error']);
          }
        }
      }
      if( Tools::getValue('botonGrupo') == "buscarCodigo"){   
        if (Tools::getValue('buscar') == "") 
        {  
          $output .= $this->displayError("Código a buscar inválido");
        } 
        else{
          $codigo = Tools::getValue('buscar');
          $result = GenerandoIcr::traerParametro($codigo);
          if(isset($result['error'])){
            $output .= $this->displayError($result['error']);
          }else{
            $this->context->smarty->assign($result);
            return $this->display(__FILE__, 'tpl/anular.tpl');
          }
        } 
      }
    }
    if(Tools::isSubmit('submita') || Tools::getValue('cambiarEstado')){
      $result = GenerandoIcr::actualizarICR(Tools::getValue('cambiarEstado'));
      if(isset($result['error'])){
        $output .= $this->displayError($result['error']);
      }else{
        $output .= $this->displayConfirmation('código ICR Anulado');
      }
    }

    if( Tools::getValue('botonGrupo') == "buscarReporte"){  
      $result = GenerandoIcr::reporteIcr();
      if ($result){
        $this->context->smarty->assign(array('listado'=> $result));
        return $this->display(__FILE__, 'tpl/list.tpl');
      }else{
        $output .= $this->displayError("no hay movimientos asociados con éste usuario");
      }
    }
    if( Tools::getValue('botonGrupo') == "ReporteLibres"){
      GenerandoIcr::reporteLibres();
    }
    return $output.$this->displayForm();
  }

  public function uninstall()
  {
    if ( !parent::uninstall() )
      Db::getInstance()->Execute( 'DELETE FROM `' . _DB_PREFIX_ . 'mymodule`' );
    parent::uninstall();
  }
}
?>