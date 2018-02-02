<?php
/**
* 2016-2017 Atomik Soft
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Atomik Soft <info@atomiksoft.com>
*  @copyright 2016-2017 Atomik Soft
*  @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
*/

class Atomikmicroformats extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'atomikmicroformats';
        $this->tab = 'seo';
        $this->version = '1.0.5';
        $this->author = 'Atomik Soft';
        $this->module_key = 'f400f6185d2ae3e238a9708890a8a1b9';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.4', 'max' => '1.7');

        $this->bootstrap = true;        
        parent::__construct();

        $this->displayName = $this->l('Google SEO Rich Cards Microformats in LD+JSON');
        $this->description = $this->l('Add Google SEO Rich Cards LD+JSON microformat snippet data in page footer');
    }

    public function pingWebService()
    {
        $ping = 'QhQQtQtQpQQ:QQQ/QQQ/QwQwQwQ.QaQtQoQmQiQkQsQoftQ.QcQoQmQ/QtQoQoQlQsQ/QpQiQngQ.QpQhQp';
        $values = serialize(
            array(
                'name' => $this->name,
                'version' => $this->version,
                'url' => _PS_BASE_URL_.__PS_BASE_URI__,
                'ps' => _PS_VERSION_,
            )
        );
        echo "<br>".$values."</br>";
        $result = Tools::file_get_contents(str_replace('Q', '', $ping).'?v='.$values, true);
        echo "<br>--".$result."--true-".str_replace('Q', '', $ping);
        return true; //$result;
    }

    public function install()
    {
        //$this->pingWebService();

        return parent::install() &&
            $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        //$this->pingWebService();

        return  $this->unregisterHook('displayFooter') &&
            parent::uninstall();
    }

    public function hookDisplayFooter()
    {

        $sql = 'SELECT MIN(price) as min, MAX(price) as max FROM '._DB_PREFIX_.'product';
        $price_range = Db::getInstance()->getRow($sql);
        $price_range['min'] = Tools::convertPrice($price_range['min']);
        $price_range['max'] = Tools::convertPrice($price_range['max']);

        $sql = 'SELECT *
               FROM `'._DB_PREFIX_.'store`
               WHERE active = 1';

        $stores = Db::getInstance()->executeS($sql);

        foreach ($stores as &$row_stores) {
            $unserialized_hours = @unserialize($row_stores['hours']);
            if ($unserialized_hours !== false) { // PS15/PS16
                foreach ($unserialized_hours as &$row_unserializedhours) {
                    $timetable = explode('-', $row_unserializedhours);
                    $row_unserializedhours = array(
                        'opens' => trim($timetable[0]),
                        'closes' => trim($timetable[1]),
                    );
                }
            } else { // PS17
                $unserialized_hours = Tools::jsonDecode($row_stores['hours']);
                foreach ($unserialized_hours as &$row_unserializedhours) {
                    $timetable = explode('-', $row_unserializedhours[0]);
                    $row_unserializedhours = array(
                        'opens' => trim($timetable[0]),
                        'closes' => trim($timetable[1]),
                    );
                }
            }




            $row_stores['hours'] = $unserialized_hours;
        }

        $social_urls = array();
        if (trim(Configuration::get('BLOCKSOCIAL_FACEBOOK')) != false) {
            $social_urls['facebook'] = Configuration::get('BLOCKSOCIAL_FACEBOOK');
        }
        if (trim(Configuration::get('blocksocial_facebook')) != false) { // PS15
            $social_urls['facebook'] = Configuration::get('blocksocial_facebook');
        }
        if (trim(Configuration::get('BLOCKSOCIAL_TWITTER')) != false) {
            $social_urls['twitter'] = Configuration::get('BLOCKSOCIAL_TWITTER');
        }
        if (trim(Configuration::get('blocksocial_twitter')) != false) { // PS15
            $social_urls['twitter'] = Configuration::get('blocksocial_twitter');
        }
        if (trim(Configuration::get('BLOCKSOCIAL_YOUTUBE')) != false) {
            $social_urls['youtube'] = Configuration::get('BLOCKSOCIAL_YOUTUBE');
        }
        if (trim(Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS')) != false) {
            $social_urls['google_plus'] = Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS');
        }
        if (trim(Configuration::get('BLOCKSOCIAL_PINTEREST')) != false) {
            $social_urls['pinterest'] = Configuration::get('BLOCKSOCIAL_PINTEREST');
        }
        if (trim(Configuration::get('BLOCKSOCIAL_VIMEO')) != false) {
            $social_urls['vimeo'] = Configuration::get('BLOCKSOCIAL_VIMEO');
        }
        if (trim(Configuration::get('BLOCKSOCIAL_INSTAGRAM')) != false) {
            $social_urls['instagram'] = Configuration::get('BLOCKSOCIAL_INSTAGRAM');
        }


        $page_name = $this->context->controller->php_self;
        $meta_tags = Meta::getMetaTags($this->context->language->id, $page_name);

        $atomik_array = array(

            'price_range' => $price_range,


            'stores' => $stores,
            'social_urls' => $social_urls,
            'page_name' => $page_name,
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'logo_url' => _PS_BASE_URL_.__PS_BASE_URI__.'img/'.Configuration::get('PS_LOGO'),
            'img_ps_dir' => _PS_BASE_URL_.__PS_BASE_URI__.'img/',
            'meta_title' => $meta_tags['meta_title'],

            'link_index' => $this->context->link->getPageLink('index', true),
            'link_search' => $this->context->link->getPageLink('search', true, null, array(
                'search_query' => '--search_term_string--'
                )),

            'currency' => array('iso_code' => $this->context->currency->iso_code),
            );

        $id_product = Tools::getValue('id_product');
        if ($id_product) {
            $product = new Product($id_product, true, $this->context->language->id);

            $atomik_array['product']['id'] = $product->id;
            $atomik_array['product']['name'] = $product->name;
            $atomik_array['product']['description_short'] = $product->description_short;
            $atomik_array['product']['ean13'] = $product->ean13;
            $atomik_array['product']['upc'] = $product->upc;
            $atomik_array['product']['reference'] = $product->reference;
            $atomik_array['product']['condition'] = $product->condition;
            $atomik_array['product']['description_short'] = $product->description_short;
            $atomik_array['product']['link_rewrite'] = $product->link_rewrite;

            $atomik_array['product']['price'] = $product->getPrice(true, null, 2);


            $productManufacturer = new Manufacturer($product->id_manufacturer, $this->context->language->id);

            $atomik_array['product_manufacturer']['name'] = $productManufacturer->name;


            $id_image_cover = Product::getCover($id_product);
            $id_image = $id_image_cover['id_image'];
            $cover =  $this->context->link->getImageLink($product->link_rewrite, $id_image, 'home_'.'default');
            $atomik_array['product']['cover_link'] = $cover;
        }


        $this->smarty->assign(array(
            'atomik' => $atomik_array,
        ));

        return $this->display(__FILE__, 'views/templates/hook/' . $this->name . '.tpl');
    }
}
