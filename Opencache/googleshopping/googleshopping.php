<?php
/*
 *
 * Google Shopping
 * Module gratuit
 * Licence d'utilisation requise (voir readme.txt)
 *
 * @author Matthieu Fradcourt - David Desquiens for igwane.com
 * @copyright Igwane.com 2010-2012 Tous droits réservés
 * @version 2.0
 * @prestashopVersion 1.4.6.2
 *
 */

require_once(_PS_MODULE_DIR_.'googleshopping'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'myTools.php');
require_once(_PS_MODULE_DIR_.'googleshopping'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'html2text.inc.php');
require_once(_PS_MODULE_DIR_.'googleshopping'.DIRECTORY_SEPARATOR.'fonctions.php');				// fonctions

class GoogleShopping extends Module
{
	function __construct()
	{
		$this->name = 'googleshopping';
		$this->tab = 'Igwane.com';
		$this->version = '2.0';

		parent::__construct();

//		$config = Configuration::getMultiple(array('IGW_DOMAIN', 'IGW_LICNUM'));
//		if (isset($config['IGW_DOMAIN']))
//			$this->_domain = $config['IGW_DOMAIN'];
//		if (isset($config['IGW_LICNUM']))
//			$this->_licnum = $config['IGW_LICNUM'];
// GoogleShopping::trim_all(
//
//		if ($this->_domain!=$_SERVER['SERVER_NAME']):
//          	$this->warning = $this->l('L\'utilisation de ce module n\'est pas autorisée sur ce domaine');
//		else:
//    		try {
//        		$handle = fopen("http://www.igwane.com/check_licence.php?domain=".$this->_domain.'&licence='.$this->_licnum, "rb");
//                $valid_licence = '';
//                while (!feof($handle)) {
//                  $valid_licence .= fread($handle, 8192);
//                }
//                fclose($handle);
//                if ($valid_licence!='OK'):
//                   $this->_licnum=null;
//                endif;
//
//        		if (empty($this->_domain) OR empty($this->_licnum))
//        			$this->warning = $this->l('L\'utilisation de ce module n\'est pas autorisée sur ce domaine sans licence (gratuite) - www.igwane.com');
//
//        	    $handle = fopen("http://www.igwane.com/googleshopping_current_version.txt", "rb");
//                $current_version = '';
//                while (!feof($handle)) {
//                  $current_version .= fread($handle, 8192);
//                }
//                fclose($handle);
//                if ($current_version!=$this->version)
//                  $this->warning = $this->l('Une nouvelle version (v'.$current_version.') est disponible.');
//
//    		} catch (Exception $e) {
//
//    		}
//		endif;
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Google Shopping by Igwane.com');
		$this->description = $this->l('Exportez vos produits vers Google Shopping.');
	}

	function install()
	{
		if(!parent::install())
		{
			return false;
		}
		return true;
	}


	public function getContent()
	{
		if(isset($_POST['generate']))
		{
            if(isset($_POST['shipping']))
    		{
    			Configuration::updateValue('GS_SHIPPING', $_POST['shipping']);

    		}

		if(isset($_POST['image']))
  		{
  			Configuration::updateValue('GS_IMAGE', $_POST['image']);
  		}

		// Récupération des langues actives pour la boutique
		$languages = Language::getLanguages();
		foreach ($languages as $i => $lang)
		{
			if(isset($_POST['product_type_'.$lang['iso_code']]))
	  		{
	  			Configuration::updateValue('GS_PRODUCT_TYPE_'.$lang['iso_code'], $_POST['product_type_'.$lang['iso_code']]);
	  		}
		}

		if(isset($_POST['DOMAIN']))
  		{
  			Configuration::updateValue('IGW_DOMAIN', rtrim($_POST['DOMAIN']));
  		}

		if(isset($_POST['LICNUM']))
  		{
  			Configuration::updateValue('IGW_LICNUM', $_POST['LICNUM']);
  		}


    		// Endroit où générer les fichiers
    		if(isset($_POST['generate_root']) && $_POST['generate_root'] === "on")
    		{
    			Configuration::updateValue('GENERATE_FILE_IN_ROOT', intval(1));

    		} else {
    			Configuration::updateValue('GENERATE_FILE_IN_ROOT', intval(0));
    			@mkdir($path_parts["dirname"].'/file_exports', 0755, true);
    			@chmod($path_parts["dirname"].'/file_exports', 0755);
    		}
    		// Gtin - Code EAN13
    		if(isset($_POST['gtin']) && $_POST['gtin'] === "on")
    		{
    			Configuration::updateValue('GTIN', intval(1));
    		} else {
    			Configuration::updateValue('GTIN', intval(0));
    		}
    		// Référence fabricant
    		if(isset($_POST['mpn']) && $_POST['mpn'] === "on")
    		{
    			Configuration::updateValue('MPN', intval(1));
    		} else {
    			Configuration::updateValue('MPN', intval(0));
    		}
    		// Quantité
    		if(isset($_POST['quantity']) && $_POST['quantity'] === "on")
    		{
    			Configuration::updateValue('QUANTITY', intval(1));
    		} else {
    			Configuration::updateValue('QUANTITY', intval(0));
    		}
    		// Marque
    		if(isset($_POST['brand']) && $_POST['brand'] === "on")
    		{
    			Configuration::updateValue('BRAND', intval(1));
    		} else {
    			Configuration::updateValue('BRAND', intval(0));
    		}
    		// Description
    		if(isset($_POST['description']) && $_POST['description'] != 0)
    		{
    			Configuration::updateValue('DESCRIPTION', intval($_POST['description']));
    		}

    		//Offre spéciale
    		if(isset($_POST['featured_product']) && $_POST['featured_product'] === "on")
    		{
    			Configuration::updateValue('FEATURED_PRODUCT', intval(1));
    		} else {
    			Configuration::updateValue('FEATURED_PRODUCT', intval(0));
    		}
			self::generateFileList();
		}

		$output = '<h2>'.$this->displayName.'</h2>';
		$output .= $this->_displayForm();

		// Bloc liens vers les fichiers générés
		$output .= '<fieldset class="space width3">
						<legend>'.$this->l('Fichiers').'</legend>
						<p><b>'.$this->l('Liens des fichiers générés').'</b></p>';

		// Récupération des langues actives pour la boutique
		$languages = Language::getLanguages();
		foreach ($languages as $i => $lang)
		{
			if(Configuration::get('GENERATE_FILE_IN_ROOT') == 1)
			{
				$get_file_url = 'http://'.myTools::getHttpHost(false, true).__PS_BASE_URI__.'googleshopping-'.$lang['iso_code'].'.xml';
			} else {
				$get_file_url = 'http://'.myTools::getHttpHost(false, true).__PS_BASE_URI__.'modules/'.$this->getName().'/file_exports/googleshopping-'.$lang['iso_code'].'.xml';
			}

			$output .='<a href="'.$get_file_url.'">'.$get_file_url.'</a><br />';
		}

		$output .='<hr><p><b>Génération automatique des fichiers</b></p>
		'.$this->l('Vous devez installer une règle CRON qui appellera le fichier suivant chaque jour ').'<br/>http://'.myTools::getHttpHost(false, true).__PS_BASE_URI__.'modules/'.$this->getName().'/cron.php'.'</p>
		</fieldset>';


		return $output;
	}

	private function _displayForm()
	{

		$options = '';
		$mpn = '';
		$generate_file_in_root = '';
		$quantity = '';
		$brand = '';
		$gtin = '';
		$selected_short = '';
		$selected_long = '';
		$featured_product = '';

		// Checked sur la generate_root box si on veut générer les fichiers à la racine du site
		if(Configuration::get('GENERATE_FILE_IN_ROOT') == 1)
		{
			$generate_file_in_root = "checked";
		}

		// Balises googleshopping optionnelles
		if(Configuration::get('GTIN') == 1)
		{
			$gtin = "checked";
		}
		if(Configuration::get('MPN') == 1)
		{
			$mpn = "checked";
		}
		if(Configuration::get('QUANTITY') == 1)
		{
			$quantity = "checked";
		}
		if(Configuration::get('BRAND') == 1)
		{
			$brand = "checked";
		}
		if(Configuration::get('FEATURED_PRODUCT') == 1)
		{
			$featured_product = "checked";
		}

		(intval(Configuration::get('DESCRIPTION')) === intval(1)) ? $selected_short = "selected" : $selected_long = "selected";

		$form = '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">


		<fieldset style="float: right; width: 255px">
					<legend>'.$this->l('A propos').'</legend>
					<p style="font-size: 1.5em; font-weight: bold; padding-bottom: 0">'.$this->displayName.' '.$this->version.'</p>
					<p style="clear: both">
					'.$this->description.'
					</p>
					<p><center><a href="http://www.igwane.com" target="_blank"><img src="http://www.igwane.com/images/logo.jpg"><img src="http://www.igwane.com/images/devis-gratuit.gif"></a></center></p>
					<p><center><a href="/modules/googleshopping/readme.txt">'.$this->l('Lire les dernières mises à jour (readme.txt)').'</a></p>
					<p>'.($this->_licnum?'':'<a style="color: #7ba45b; text-decoration: underline;" href="http://www.igwane.com/fr/contact" target="_blank">Demandez votre licence d\'utilisation gratuite (précisez votre nom de domaine)</a>').'</p>
		</fieldset>

		<fieldset class="space width3">
		<legend>'.$this->l('Paramètres').'</legend>';

		if (!$this->_licnum):
        $form.='
			<label>'.$this->l('Licence accordée à').' </label>
			<div class="margin-form">
				<input type="text" name="DOMAIN" value="'.Configuration::get('IGW_DOMAIN').'" size="40">
			</div>
			<label>'.$this->l('Numéro de licence').' </label>
			<div class="margin-form">
				<input type="text" name="LICNUM" value="'.Configuration::get('IGW_LICNUM').'" size="40">
			</div>
		';
        else:
           $form.='
			<label>'.$this->l('Licence accordée à').' </label>
			<div class="margin-form">
				<input type="text" name="DOMAIN" readonly value="'.Configuration::get('IGW_DOMAIN').' "size="40">
			</div>

		';
        endif;

        $form.='
			<label>'.$this->l('Type de description').' </label>
			<div class="margin-form">
				<select name="description">
					<option value="1" '.$selected_short.'>'.$this->l('Description courte').'</option>
					<option value="2" '.$selected_long.'>'.$this->l('Description longue').'</option>
				</select>
			</div>';

		// Récupération des langues actives pour la boutique
		$languages = Language::getLanguages();
		foreach ($languages as $i => $lang)
		{
			$form.='<label title="product_type_'.$lang['iso_code'].'">'.$this->l('Catégorie Google').' '.strtoupper($lang['iso_code']).'</label>
			<div class="margin-form">
				<input type="text" name="product_type_'.$lang['iso_code'].'" value="'.Configuration::get('GS_PRODUCT_TYPE_'.$lang['iso_code']).'" size="40">
				<br />(<a href="http://www.google.com/support/merchants/bin/answer.py?answer=160081&query=product_type" target="_blank">'.$this->l('Voir Catégorie Google').'</a>)
			</div>';
		}


		$form.='<label title="[shipping]">'.$this->l('Frais de port').' </label>
			<div class="margin-form">
				<input type="text" name="shipping" value="'.Configuration::get('GS_SHIPPING').'">
			</div>

			<label title="[image]">'.$this->l('Type de l\'image').' </label>
			<div class="margin-form">
				<input type="text" name="image" value="'.((Configuration::get('GS_IMAGE')!='')?(Configuration::get('GS_IMAGE')):'large').'">
			</div>

			<hr>

			<table>
				<tr>
					<td><label>'.$this->l('Générer les fichiers à la racine du site').'</label></td>
					<td><input type="checkbox" name="generate_root" '.$generate_file_in_root.'></td>
				</tr>
				<tr>
					<td><label>'.$this->l('Références fabricants').'</label></td>
					<td><input type="checkbox" name="mpn" '.$mpn.' title="'.$this->l('Recommandé par Google').'"></td>
				</tr>
				<tr>
					<td><label>'.$this->l('Quantité de produits').'</label></td>
					<td><input type="checkbox" name="quantity" '.$quantity.' title="'.$this->l('Recommandé par Google').'"></td>
				</tr>
				<tr>
					<td><label title="[brand]">'.$this->l('Marque').'</label></td>
					<td><input type="checkbox" name="brand" '.$brand.' title="'.$this->l('Recommandé par Google').'"></td>
				</tr>
				<tr>
					<td><label>'.$this->l('Code EAN13').'</label></td>
					<td><input type="checkbox" name="gtin" '.$gtin.' title="'.$this->l('Recommandé par Google').'"></td>
				</tr>
				<tr>
					<td><label>'.$this->l('En solde').'</label></td>
					<td><input type="checkbox" name="featured_product" '.$featured_product.'></td>
				</tr>
			</table>
			<br>
			<center><input name="generate" type="submit" value="'.$this->l('Générer les fichiers').'"></center>
		</fieldset>
		</form>
		';
		return $form;
	}

	public function getName()
	{
		$output = $this->name;
		return $output;
	}

	public function uninstall()
	{
		Configuration::deleteByName('GS_PRODUCT_TYPE');
		Configuration::deleteByName('GS_SHIPPING');

		Configuration::deleteByName('IGW_DOMAIN');
		Configuration::deleteByName('IGW_LICNUM');

		return parent::uninstall();
	}

	public static function generateFileList()
	{
		// Récupération des langues actives pour la boutique
		$languages = Language::getLanguages();
		foreach ($languages as $i => $lang)
		{
			self::generateFile($lang);
		}
	}
        
        public static function getCategoryTree($id_product,$id_lang = 1){
                $root = Category::getRootCategory();
               return $selected_cat = Product::getProductCategoriesFull($id_product, $id_lang);
//                return $tab_root = array('id_category' => $root->id, 'name' => $root->name);
//                $helper = new Helper();
//                $category_tree = $helper->renderCategoryTree($tab_root, $selected_cat, 'categoryBox', false, true, array(), false, true);
//                return $category_tree;
            }

	private static function generateFile($lang)
	{
		global $link;
		$path_parts = pathinfo(__FILE__);

//		if (Configuration::get('GENERATE_FILE_IN_ROOT')):
//			$generate_file_path = '../googleshopping-'.$lang['iso_code'].'.xml';
//	        else:
			$generate_file_path = $path_parts["dirname"].'/../../file_exports/googleshopping-'.$lang['iso_code'].'.xml';
                        
                        
//        	endif;

               $protocolo=''; 
               if(GoogleShopping::is_ssl())
                 {                 
                  $protocolo='https://';
                }
                else {
                  $protocolo='http://';    
                  }
                 $link_site=$protocolo.myTools::getHttpHost(false, true).__PS_BASE_URI__; 
//                    if(strtolower(substr($product_link, 4,1)) === 's'){
//                        $protocolo=substr($product_link, 0,8);
//                       }else{
//                        $protocolo=substr($product_link, 0,7); 
//                       }
             
              
           
		//Google Shopping XML
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
		$xml .= '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0" encoding="UTF-8" >'."\n";
		$xml .= '<title>'.GoogleShopping::trim_all(Configuration::get('PS_SHOP_NAME')).' México</title>'."\n";
		$xml .= '<link href="'.$link_site.'" rel="alternate" type="text/html"/>'."\n";
		$xml .= '<updated>'.date('Y-m-d').'T01:01:01Z</updated><author><name>'.GoogleShopping::trim_all(Configuration::get('PS_SHOP_NAME')).'</name></author>'."\n";
		
		$xml .='<id>tag:farmalisto.com.mx,'.date('Y-m-d').'T01:01:01Z:/content/1-entregas-y-pedidos-domicilios</id>';

		$googleshoppingfile = fopen($generate_file_path,'w');

		fwrite($googleshoppingfile, $xml);

		$sql='SELECT p.id_category_default,p.id_product,pl.id_lang,pl.`name`,pl.description
					,pl.description_short,p.date_upd,p.supplier_reference,pl.link_rewrite
					,p.id_manufacturer, p.weight,p.on_sale,p.ean13,p.reference
			FROM '._DB_PREFIX_.'product p 
			INNER JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product)
			INNER JOIN '._DB_PREFIX_.'category_product catp ON(p.id_product = catp.id_product)
			INNER JOIN '._DB_PREFIX_.'category cat ON ( catp.id_category = cat.id_category)
			INNER JOIN '._DB_PREFIX_.'feature_product feap ON(p.id_product = feap.id_product)
			INNER JOIN '._DB_PREFIX_.'feature fea ON(feap.id_feature = fea.id_feature)   
			WHERE cat.id_category IN(1916 ,1961 ,1918 )
			-- AND p.id_category_default IN(555,447,636,433,576)
			AND fea.id_feature NOT IN(4121,4128) 
			AND p.active = 1 AND pl.id_lang='.$lang['id_lang'].' 
			GROUP BY p.id_product;';

		// $sql='SELECT * FROM '._DB_PREFIX_.'product p'.
		// ' LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.id_product = pl.id_product'.
		// ' WHERE p.active = 1 AND pl.id_lang='.$lang['id_lang'];

		$products = Db::getInstance()->ExecuteS($sql);

		$site_base = __PS_BASE_URI__;		// préfix du site
		$url_site = myTools::getHttpHost(false, true);	// url du site base Serveur
		$url_site_base_prestashop = $url_site.$site_base;

		$title_limit = 70;
		$description_limit = 5000;

		$languages = Language::getLanguages();
		$tailleTabLang = sizeof($languages);

		foreach($products as $product)
		{
			$xml_googleshopping ='';
			$cat_link_rew = Category::getLinkRewrite($product['id_category_default'], intval($lang));
			$product['details'] = new Product((int)($product['id_product']), false, $lang['id_lang']);
                        $link = new Link();
			$product_link = $link->getProductLink((int)($product['details']->id), $product['details']->link_rewrite, $cat_link_rew, $product['details']->ean13, $lang['id_lang']);
// https://support.google.com/merchants/answer/160593?hl=es&ref_topic=2473799
// http://www.celebird.com/ckbase.htm			
			$title_crop = $product['name'];
			if(strlen($product['name']) > $title_limit)
			{
				$title_crop = substr($title_crop, 0, ($title_limit-1));
				$title_crop = substr($title_crop, 0, strrpos($title_crop," "));
			}

			if(intval(Configuration::get('DESCRIPTION')) === intval(2))
			{
				$description_crop = $product['description'];
			} else {
				$description_crop = $product['description_short'];
			}
			$description_crop = myTools::f_convert_text("", $description_crop, false);

			if(strlen($description_crop) > $description_limit)
			{
				$description_crop = substr($description_crop, 0, ($description_limit-1));
				$description_crop = substr($description_crop, 0, strrpos($description_crop," "));
			}
                        
                        $description_crop = trim($description_crop);
                        if(empty($description_crop) || $description_crop ==='' || is_null($description_crop))
                        {                       
                          $description_crop = 'Productos de farmacia, medicamentos, salud, nutrici&#243;n y cuidado personal para la familia';  
                        }
			$xml_googleshopping .= '<entry>'."\n";
			$xml_googleshopping .= '<g:id>'.$product['id_product'].'-'.$lang['iso_code'].'</g:id>'."\n";
			$xml_googleshopping .= '<title>'.htmlspecialchars(ucfirst(mb_strtolower(GoogleShopping::trim_all($title_crop),'UTF-8'))).'</title>'."\n";
			$xml_googleshopping .= '<link>'.$product_link.'</link>'."\n";
			$xml_googleshopping .= '<g:price>'.Product::getPriceStatic($product['id_product'],true,NULL,2).'</g:price>'."\n";
			$xml_googleshopping .= '<summary>'.htmlspecialchars(GoogleShopping::trim_all($description_crop), null, 'UTF-8', false).' </summary>'."\n";
			$xml_googleshopping .= '<updated>'.$product['date_upd'].'T01:01:01Z</updated>';
			$xml_googleshopping .= '<g:condition>new</g:condition>'."\n"; //condition = neuf, occasion, reconditionné OR new, used, refurbished

                       
                        $xml_googleshopping .= '<g:product_type>>';
                       foreach (GoogleShopping::getCategoryTree((int)$product['id_product'],1) as $value) {
                         $xml_googleshopping .=  str_replace(' Y ', ' &amp; ', $value['name'].' &gt; ');                           
                       }
                       $xml_googleshopping .= '</g:product_type>';
                       
                       $xml_googleshopping .= '<g:google_product_category>'.GoogleShopping::trim_all($product['details']->category).'</g:google_product_category>';
                       
                   
                     // exit('<pre>'.print_r($product['details'],TRUE).'</pre>');
                    // $xml_googleshopping .='<g:product_type>Home &amp; Garden &gt; Kitchen &amp; Dining &gt; Appliances &gt; Refrigerators</g:product_type>';

			if(Configuration::get('MPN') && $product['supplier_reference'] != '')
			{
				$xml_googleshopping .= '<g:mpn>'.GoogleShopping::trim_all($product['supplier_reference']).'</g:mpn>'; //ref fabricant
			}
			// Pour chaque image
			$images = Image::getImages($lang['id_lang'], $product['id_product']);
			$indexTabLang = 0;
                        
			if($tailleTabLang >1 ){			
				while(sizeof($images) < 1 && $indexTabLang<$tailleTabLang){
					if($languages[$indexTabLang]['id_lang']!=$lang['id_lang']){
						$images = Image::getImages($languages[$indexTabLang]['id_lang'], $product['id_product']);
					}
					 $indexTabLang++;	
				}
			}
                        
			$nbimages=0;
			$image_type=Configuration::get('GS_IMAGE');
		        if ($image_type=='') $image_type='large';

                        if(count($images) > 0){
			foreach($images as $im)
			{
                          // Old URL
			  //$image='http://'.$url_site_base_prestashop.'img/p/'.$product['id_product'].'-'.$im['id_image'].'-large.jpg';
//			  $image= $link->getImageLink($product['link_rewrite'], $product['id_product'] .'-'. $im['id_image'],$image_type);
                          $imagePath = $link->getImageLink($product['link_rewrite'], $im['id_image'], 'large_default');
                   
               
  			  $xml_googleshopping .= '<g:image_link>'.$protocolo.$imagePath.'</g:image_link>'."\n";
  			  if (++$nbimages == 10) break;
			}
                        }else {
                             $xml_googleshopping .= '<g:image_link>'.$link_site.'img/p/es-default-large_default.jpg</g:image_link>'."\n";    
                          }
            /*          
			// Quantité et disponibilité
			if(Configuration::get('QUANTITY') == 1)
			{
				if ($product['quantity'] != '' && $product['quantity'] != '0')
					{
						$xml_googleshopping .= '<g:quantity>'.$product['quantity'].'</g:quantity>'."\n";
						$xml_googleshopping .= '<g:availability>in stock</g:availability>'."\n";
					}
					else{
						$xml_googleshopping .= '<g:quantity>0</g:quantity>'."\n";
						$xml_googleshopping .= '<g:availability>out of stock</g:availability>'."\n";
				
						$xml_googleshopping .= '<g:quantity>32</g:quantity>'."\n";
						$xml_googleshopping .= '<g:availability>in stock</g:availability>'."\n";	
			}
			*/
						$xml_googleshopping .= '<g:quantity>32</g:quantity>'."\n";
						$xml_googleshopping .= '<g:availability>in stock</g:availability>'."\n";			

			// Marque
			if(Configuration::get('BRAND') && $product['id_manufacturer'] != '0')
			{
				$xml_googleshopping .= '<g:brand>'.GoogleShopping::trim_all(htmlspecialchars(Manufacturer::getNameById(intval($product['id_manufacturer'])), null, 'UTF-8', false)).'</g:brand>'."\n";
			}

			// Catégorie
			if(Configuration::get('GS_PRODUCT_TYPE_'.$lang['iso_code']))
			{
				$product_type = str_replace('>', '&gt;', Configuration::get('GS_PRODUCT_TYPE_'.$lang['iso_code']));
				$product_type = str_replace('&', '&amp;', $product_type);
				$xml_googleshopping .= '<g:google_product_category>'.GoogleShopping::trim_all($product_type).'</g:google_product_category>'."\n";
				$xml_googleshopping .= '<g:product_type>'.GoogleShopping::trim_all($product_type).'</g:product_type>'."\n";				
			}

			// Frais de port
			if(Configuration::get('GS_SHIPPING'))
			{
				$xml_googleshopping .= '<g:shipping>'."\n";
				$xml_googleshopping .= '<g:country>MX</g:country>'."\n";
				$xml_googleshopping .= '<g:service>Standard</g:service>'."\n";
				$xml_googleshopping .= '<g:price>'.Configuration::get('GS_SHIPPING').'</g:price>'."\n";
				$xml_googleshopping .= '</g:shipping>'."\n";
			}

			//Poids
			if($product['weight'] != '0')
			{
				$xml_googleshopping .= '<g:shipping_weight>'.$product['weight'].' kilograms</g:shipping_weight>'."\n";
			}

			// Offre spéciale
			if(Configuration::get('FEATURED_PRODUCT') == 1 && $product['on_sale'] != '0')
			{
				$xml_googleshopping .= '<g:featured_product>o</g:featured_product>'."\n";
			}


			if(Configuration::get('GTIN') && $product['ean13'] != '')
			{
				$xml_googleshopping .= '<g:gtin>'.GoogleShopping::trim_all($product['ean13']).'</g:gtin>'."\n";

			}
				$xml_googleshopping .= '</entry>'."\n";

			// Ecriture du produit dans l'XML googleshopping
			fwrite($googleshoppingfile, $xml_googleshopping);
		}

		$xml = '</feed>';
		fwrite($googleshoppingfile, $xml);
		fclose($googleshoppingfile);

		@chmod($generate_file_path, 0777);
		return true;
	}
        
public static function is_ssl() {
    if ( isset($_SERVER['HTTPS']) ) {
        if ( 'on' == strtolower($_SERVER['HTTPS']) )
            return true;
        if ( '1' == $_SERVER['HTTPS'] )
            return true;
    } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
        return true;
    }
    return false;
}

public static function trim_all( $str , $what = NULL , $with = ' ' )
{
    if( $what === NULL )
    {
        //  Character      Decimal      Use
        //  "\0"            0           Null Character
        //  "\t"            9           Tab
        //  "\n"           10           New line
        //  "\x0B"         11           Vertical Tab
        //  "\r"           13           New Line in Mac
        //  " "            32           Space
       
        $what   = "\\x00-\\x20";    //all white-spaces and control chars
    }
   
    return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
}

}
?>
