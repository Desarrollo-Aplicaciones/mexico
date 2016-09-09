<?php
if (!defined('_PS_VERSION_'))
	exit;

class massseo extends Module
{
	function __construct()
	{
		$this->name				= 'massseo';  
		$this->tab				= 'seo';
		$this->version			= '1.2.0';
		$this->author			= 'egica.com';
		$this->displayName		= $this->l('Mass SEO');
		$this->description		= $this->l('Mass SEO Update');
		$this->module_key		= '5cf81f6273e6469b59a430cc85f69a9b';
		
		$this->aLanguages		= Array();
		$this->countLanguages	= 0;
		$this->allLanguages		= (isset($_POST["show_languajes"]))?$_POST["show_languajes"]:0;
		
		$this->totalProducts	= 0;
		$this->realProducts		= 0;
		$this->paginationStart	= 0;
		$this->paginationPage	= (isset($_POST["paginationPage"]))?$_POST["paginationPage"]:0;
		$this->paginationProducts = (isset($_POST["paginationProducts"]))?$_POST["paginationProducts"]:10;
		$this->paginationLimit	= 0;
		
		
		parent::__construct();
	}
 	
  	private function getProducts($s_string='', $s_supplier='', $s_manufacturer='', $categoryList='', $count=0)
  	{
		if($count){
			$sql = 'SELECT COUNT(*) AS total_products ';
		}else{
			$sql = 'SELECT
						p.`id_product`,
						p.`reference`,
						pl.`name` ';
        }

		/**/$sql .='FROM
						`'._DB_PREFIX_.'product` p
                    INNER JOIN
						`'._DB_PREFIX_.'product_lang` AS pl ON p.id_product = pl.id_product
					WHERE
						pl.`id_lang`= '.$this->default_language.'
						AND p.`id_product` != ""
						'.(!empty($s_supplier)?' AND p.`id_supplier` = '.$s_supplier.'' :'').'
						'.(!empty($s_manufacturer)?' AND p.`id_manufacturer` = '.$s_manufacturer.'' :'').'
						'.(is_array($categoryList)?' AND p.`id_category_default` IN ('.implode(",",$categoryList).')':'').'
						'.(!empty($s_string)?' AND pl.`name` LIKE "%'.$s_string.'%" OR p.`reference` LIKE "%'.$s_string.'%" OR p.`supplier_reference` LIKE "%'.$s_string.'%"' :'').'
					ORDER BY
						pl.`name` ASC';

		if($count){
			return Db::getInstance()->getValue($sql);
		}else{
			$sql .= ' LIMIT '.$this->paginationStart.', '.$this->paginationLimit;
            return Db::getInstance()->ExecuteS($sql);

		}
  	}
  	
	private function productDetail($product, $new=FALSE)
  	{
		global $cookie;
        $link           = new Link();
        $productsLang   = Array();

        $sql = 'SELECT
					pl.`id_lang`,
					pl.`meta_keywords`,
					pl.`meta_description`,
					pl.`link_rewrite`,
					pl.`meta_title`
                FROM
                    `'._DB_PREFIX_.'product` p
                INNER JOIN
                    `'._DB_PREFIX_.'product_lang` AS pl ON p.id_product = pl.id_product
                WHERE
                    p.`id_product` = '.$product['id_product'];

        $products = Db::getInstance()->ExecuteS($sql);

        if($products){
            foreach($products as $productData){
                $productsLang[$productData['id_lang']]['name'] = $productData['name'];
                $productsLang[$productData['id_lang']]['meta_description'] = $productData['meta_description'];
                $productsLang[$productData['id_lang']]['meta_title'] = $productData['meta_title'];
                $productsLang[$productData['id_lang']]['meta_keywords'] = $productData['meta_keywords'];
                $productsLang[$productData['id_lang']]['link_rewrite'] = $productData['link_rewrite'];
            }
        }

		$out = '<tr>
					<th>
						'.htmlspecialchars($product['name']).'
					</th>
					<th>
						'.htmlspecialchars($product['reference']).'
					</th>
					<th class="right">
						<a href="'.$link->getProductLink($product['id_product']).'" target="_blank" title="'.$this->l('Go to').'"><img border="0" alt="'.$this->l('Go to').'" src="../img/admin/details.gif"></a>
						<a href="index.php?tab=AdminCatalog&id_product='.$product['id_product'].'&updateproduct&token='.Tools::getAdminToken('AdminCatalog1'.intval($cookie->id_employee)).'" target="_blank" title="'.$this->l('Modify').'"><img border="0" alt="'.$this->l('Modify').'" src="../img/admin/edit.gif"></a>
					</th>
				</tr>';
		
		foreach($this->aLanguages as $language){
			if($language['active'] || $this->allLanguages){
				$out .= '<tr class="td_lang td_lang_'.$language['iso_code'].'">
							<td colspan="3">
								<table>
									<tr>
										<td class="td_flag"><img src="../img/l/'.$language['id_lang'].'.jpg" alt="'.$language['iso_code'].'"/></td>
										<td class="td_c">
											<p>
												<span class="float massseo_input">'.$this->l('Title').'</span><input style="width:500px" class="char_field" type="text" value="'.htmlspecialchars($productsLang[$language['id_lang']]['meta_title']).'" name="product['.$product['id_product'].']['.$language['id_lang'].'][meta_title]" /> <span>'.strlen(htmlspecialchars($productsLang[$language['id_lang']]['meta_title'])).'</span> '.$this->l('Characters').'
											</p>
											<p>
												<span class="float massseo_input">'.$this->l('Description').'</span><input type="text" class="char_field" style="width:500px" value="'.htmlspecialchars($productsLang[$language['id_lang']]['meta_description']).'" name="product['.$product['id_product'].']['.$language['id_lang'].'][meta_description]" /> <span>'.strlen(htmlspecialchars($productsLang[$language['id_lang']]['meta_description'])).'</span> '.$this->l('Characters').'
											</p>
											<p>
												<span class="float massseo_input">'.$this->l('Keywords').'</span><input type="text" class="word_field" style="width:500px" value="'.htmlspecialchars($productsLang[$language['id_lang']]['meta_keywords']).'" name="product['.$product['id_product'].']['.$language['id_lang'].'][meta_keywords]" /> <span>'.(htmlspecialchars($productsLang[$language['id_lang']]['meta_keywords']) == ''? '0':count(preg_split('/,/',htmlspecialchars($productsLang[$language['id_lang']]['meta_keywords'])))).'</span> '.$this->l('Words').'
											</p>
											<p>
												<span class="float massseo_input">'.$this->l('Friendly URL').'</span><input type="text" class="char_field" style="width:500px" value="'.htmlspecialchars($productsLang[$language['id_lang']]['link_rewrite']).'" name="product['.$product['id_product'].']['.$language['id_lang'].'][link_rewrite]" /> <span>'.strlen(htmlspecialchars($productsLang[$language['id_lang']]['link_rewrite'])).'</span> '.$this->l('Characters').'
											</p>
										</td>
									</tr>
								</table>
							</td>
						</tr>';
			}
		}

		return $out;
 	}

	private function paginateProducts()
	{
		$adjacents	= 3;
		$page		= $this->paginationPage;
		$this->totalProducts = $this->getProducts((isset($_POST['s_string']))?$_POST['s_string']:'', (isset($_POST['s_supplier']))?$_POST['s_supplier']:'', (isset($_POST['s_manufacturer']))?$_POST['s_manufacturer']:'', (isset($_POST['s_categories']))?$_POST['s_categories']:'', 1);
		$this->realProducts = (int)($this->totalProducts);
		$this->paginationLimit 	= (int)($this->paginationProducts);
		
		
		if($page){
			$this->paginationStart = ($page - 1) * $this->paginationLimit;
		}else{
			$this->paginationStart = 0;
		}
		
		if($page == 0){
			$page = 1;
		}
		
		$prev		= $page - 1;
		$next		= $page + 1;
		$lastpage	= ceil($this->totalProducts / $this->paginationLimit);
		$lpm1		= $lastpage - 1;
		$pagination = "";
		
		if($lastpage > 1){
			$pagination .= '<div class="pagination">';

			if($page > 1){
				$pagination .= '<a href="#" class="link" page="'.$prev.'"><img src="../img/admin/list-prev.gif" /></a>';
			}else{
				$pagination .= '<span class="disabled"><img src="../img/admin/blank.gif" width="5" /></span>';
			}
			
			if($lastpage < 7 + ($adjacents * 2)){
				for($counter = 1; $counter <= $lastpage; $counter++){
					if($counter == $page){
						$pagination .= '<span class="current">'.$counter.'</span>';
					}else{
						$pagination .= '<a href="#" class="link" page="'.$counter.'">'.$counter.'</a>';
					}
				}
			}elseif($lastpage > 5 + ($adjacents * 2)){
				if($page < 1 + ($adjacents * 2)){
					for($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
						if($counter == $page){
							$pagination.= '<span class="current">'.$counter.'</span>';
						}else{
							$pagination.= '<a href="#" class="link" page="'.$counter.'">'.$counter.'</a>';
						}
					}
					$pagination .= '...';
					$pagination .= '<a href="#" class="link" page="'.$lpm1.'">'.$lpm1.'</a>';
					$pagination .= '<a href="#" class="link" page="'.$lastpage.'">'.$lastpage.'</a>';
				}elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
					$pagination .= '<a href="#" class="link" page="1">1</a>';
					$pagination .= '<a href="#" class="link" page="2">2</a>';
					$pagination .= '...';
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if($counter == $page){
							$pagination .= '<span class="current">'.$counter.'</span>';
						}else{
							$pagination .= '<a href="#" class="link" page="'.$counter.'">'.$counter.'</a>';
						}
					}
					$pagination .= '...';
					$pagination .= '<a href="#" class="link" page="'.$lpm1.'">'.$lpm1.'</a>';
					$pagination .= '<a href="#" class="link" page="'.$lastpage.'">'.$lastpage.'</a>';
				}else{
					$pagination .= '<a href="#" class="link" page="1">1</a>';
					$pagination .= '<a href="#" class="link" page="2">2</a>';
					$pagination .= '...';
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if($counter == $page){
							$pagination .= '<span class="current">'.$counter.'</span>';
						}else{
							$pagination .= '<a href="#" class="link" page="'.$counter.'">'.$counter.'</a>';
						}
					}
				}
			}

			if($page < $counter - 1){
				$pagination .= '<a href="#" class="link" page="'.$next.'"><img src="../img/admin/list-next.gif" /></a>';
			}else{
				$pagination .= '<span class="disabled"><img src="../img/admin/blank.gif" width="5" /></span>';
			}
			$pagination .= '</div>';
		}
		
		return $pagination;
	}

	/* -- IDENET-COMMENT: DESACTIVADO PARA VERSION DEMO */

    public function updateProducts()
	{
		if(!isset($_POST['product'])){
			return;
		}

		foreach($_POST['product'] as $id_product => $languages){
			foreach($languages as $id_lang => $product){
				$sql = 'UPDATE 
							`'._DB_PREFIX_.'product_lang` 
						SET 
							meta_title = "'.pSQL($product['meta_title']).'",
							meta_description = "'.pSQL($product['meta_description']).'",
							meta_keywords = "'.pSQL($product['meta_keywords']).'",
							link_rewrite = "'.pSQL($product['link_rewrite']).'" 
						WHERE 
							id_lang = '.(int)$id_lang.' and id_product = '.(int)$id_product.'
						';

				Db::getInstance()->Execute($sql);
			}
		} 	 		
	}

	/**/

	private function getSuppliers()
	{
		$sql = 'SELECT DISTINCT
					`id_supplier`,
					`name`
				FROM
					`'._DB_PREFIX_.'supplier`
				ORDER BY
					`name` ASC
				';

		return Db::getInstance()->ExecuteS($sql);
	}

	private function getManufacturers()
	{
		$sql = 'SELECT
					`id_manufacturer`,
					`name`
				FROM
					`'._DB_PREFIX_.'manufacturer`
				ORDER BY
					`name` ASC
				';

		return Db::getInstance()->ExecuteS($sql);
	}

	private function createSelect($element, $name, $class_name='', $selected=null, $keyField, $descField)
	{
		$out =	'<select name="'.$name.'" class="'.$class_name.'">
				<option value="">'.$this->l('--- All ---').'</option>';
				
				foreach($element as $item){
					$out .= '<option value="'.$item[$keyField].'"'.(($item[$keyField] == $selected)?'selected':'').'>'.$item[$descField].'</option>';
				}
		$out .=	'</select>';
		
		return $out;
	}
	
	private function createSelectWithOptions($element, $name, $class_name='', $selected=null)
	{
		$out =	'<select name="'.$name.'" class="'.$class_name.'">';
				foreach($element as $item){
					$out .= '<option value="'.$item.'"'.(($item == $selected)?'selected':'').'>'.$item.'</option>';
				}
		$out .=	'</select>';
		
		return $out;
	}

	private function getCategories()
	{
		global $cookie;
		$aSelectedCategories = (isset($_POST['s_categories']))? $_POST["s_categories"] : array();
		
		$out = '';
		$categories = Category::getCategories(intval($cookie->id_lang));
		$out .= $this->recursiveCategory($categories, $categories[0][1], 1, $aSelectedCategories);
		
		return $out;
	}

	private function recursiveCategory($categories, $current, $id_category = 1, $aSelectedCategories)
	{
		$out = '';
		$out .= '<option value="'.$id_category.'"'.(in_array($id_category,$aSelectedCategories)? ' selected="selected"' : '').'>'.str_repeat('&nbsp;', $current['infos']['level_depth'] * 5) . preg_replace('/^[0-9]+\./', '', stripslashes($current['infos']['name'])).'</option>';
		if(isset($categories[$id_category])){
			foreach($categories[$id_category] AS $key => $row){
				$out .= $this->recursiveCategory($categories, $categories[$id_category][$key], $key, $aSelectedCategories);
			}
		}

		return $out;
	}

	public function getContent()
	{
		$out = '';
		$this->aLanguages		= Language::getLanguages(false);
		$this->countLanguages	= count($this->aLanguages);
		$this->default_language = (int)(Configuration::get('PS_LANG_DEFAULT'));

		/* -- IDENET-COMMENT: DESACTIVADO PARA VERSION DEMO*/

		if(isset($_POST['submitAction']) && $_POST['submitAction'] == "update"){
			$this->updateProducts($_POST);
		}

		/**/

		$pagination = $this->paginateProducts($_POST);
		$products	= $this->getProducts((isset($_POST['s_string']))?$_POST['s_string']:'', (isset($_POST['s_supplier']))?$_POST['s_supplier']:'', (isset($_POST['s_manufacturer']))?$_POST['s_manufacturer']:'', (isset($_POST['s_categories']))?$_POST['s_categories']:'', 0);

		$out .=	'<style>
					.massseo_select{min-width: 200px}
					.massseo_input{width: 75px; margin-top: 3px}
					.massseo_button{padding: 0 40px}
					.massseo_comment{font-size: 10px; color: #AAAAAA}
					.search{width: 190px}
					.td_c{border: none!important}
					.td_flag{border: none!important; width: 100px}
					.pagination{float: right}
					.languaje_flags{float: left}
					.link, .current{padding: 0 5px}
					
				</style>
				<script>
					$(document).ready(function(){
						$(".lang_hidden").click(function(event){
							event.preventDefault();
							language = $(this).attr("language");
							$(".td_lang_"+language).toggle("slow","swing");
							if($(this).attr("opacity") == 1){
								$(this).attr("opacity",0);
								$(this).css({opacity: 0.4});
							}else{
								$(this).attr("opacity",1);
								$(this).css({opacity: 1});
							}
						});
						
						$(".char_field").keyup(function(event){
							$(this).next().text($(this).val().length);							
						});
						
						$(".word_field").keyup(function(event){
							$(this).next().text($(this).val().split(",").length);							
						});
						
						$(".link").click(function(){
							$("#paginationPage").val($(this).attr("page"));
							$("#f_products").submit();
							return false;
						});
						
						$(".save_changes").click(function(){
							$("#paginationPage").val($(".current").html());
							$("#submitAction").val("update");
							$("#f_products").submit();
							return false;
						});
						
						$(".paginationSelect").change(function(){
							$("#paginationProducts").val($(this).val());
							$("#f_products").submit();
							return false;
						});
						
					})
				</script>
				<form id="f_products" method="post" action="'.$_SERVER['REQUEST_URI'].'">
					<fieldset style="margin-top: 10px;">
						<legend>'.$this->l('Products search').'</legend>
						<input type="hidden" id="paginationPage" name="paginationPage" value="" />
						<input type="hidden" id="paginationProducts" name="paginationProducts" value="'.$this->paginationProducts.'" />
						<input type="hidden" id="submitAction" name="submitAction" value="" />
						<div style="float: left">
							<p><label>'.$this->l('Search:').' </label><input type="text" name="s_string" value="'.(isset($_POST['s_string'])?$_POST['s_string']:'').'" class="search" /></p>
							<p><label>'.$this->l('Manufacturer:').' </label>'.$this->createSelect($this->getManufacturers(), 's_manufacturer', 'massseo_select', (isset($_POST['s_manufacturer'])?$_POST['s_manufacturer']:''), 'id_manufacturer', 'name').'</p>
							<p><label>'.$this->l('Supplier:').' </label>'.$this->createSelect($this->getSuppliers(), 's_supplier', 'massseo_select', (isset($_POST['s_supplier'])?$_POST['s_supplier']:''), 'id_supplier', 'name').'</p>
							<p><label>'.$this->l('Languages:').' </label><select class="massseo_select" name="show_languajes"><option value="0"'.($this->allLanguages == 0 ? ' selected':'').'>'.$this->l('Show only active languages').'</option><option value="1"'.($this->allLanguages == 1 ? ' selected':'').'>'.$this->l('Show all languages').'</option></select>
						</div>
						<div style="float: left; text-align: right">
							<p>
								<label>'.$this->l('Categories:').' </label><select name="s_categories[]" multiple="multiple" size="9" style="width: 270px">'.$this->getCategories().'</select>
								<br /><span class="massseo_comment">'.$this->l('Press Ctrl key to select multiple items').'</span>
							</p>
						</div>
						<div style="clear:both">
							<p style="text-align: center"><input class="massseo_button button" type="submit" value="'.$this->l('Search').'" name="s_button" /></p>
						</div>
					</fieldset>
					<fieldset style="margin-top: 10px;">
						<legend>'.$this->l('Search result').' - '.$this->l('Total').': '.$this->realProducts.' '.$this->l('products').'</legend>
						<div class="languaje_flags">
						'.$this->l('Show or hide languajes').': ';
						
						foreach($this->aLanguages as $language){
							if($language['active'] || $this->allLanguages){
								$out .= '<a href="#" class="lang_hidden" language="'.$language['iso_code'].'" opacity="1"><img src="../img/l/'.$language['id_lang'].'.jpg" alt="'.$language['iso_code'].'"/></a>';
							}
						}
					
		$out .= '		</div>
						<div class="floatr">'.$this->createSelectWithOptions(Array(5,10,20,30,40,50), 'paginationSelect', 'paginationSelect',$this->paginationProducts).' '.$this->l('products per page').'</div>
						'.$pagination.'
						<div class="clear"></div>
						<p class="center clear" style="padding-top: 10px"><input class="massseo_button button save_changes" type="submit" value="'.$this->l('Save changes').'" name="saveChanges" /></p>
						
						<table cellspacing="0" cellpadding="0" class="table space" width="100%" align="center" style="margin-bottom: 10px">
							<tbody>';
	
							foreach($products as $product)
								$out .= $this->productDetail($product, true);
					
		$out .= '			</tbody>
						</table>
						<div class="floatr">'.$this->createSelectWithOptions(Array(5,10,20,30,40,50), 'paginationSelect', 'paginationSelect',$this->paginationProducts).' '.$this->l('products per page').'</div>
						'.$pagination.'
						<p class="center clear" style="padding-top: 10px"><input class="massseo_button button save_changes" type="submit" value="'.$this->l('Save changes').'" name="saveChanges" /></p>
					</fieldset>
				</form>';

		return $out;
	}
}
?>