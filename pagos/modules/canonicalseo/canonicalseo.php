<?php
/*
 * Author: Burhan Bavkır
 * Developed and all copyrights owned by www.bvkyazilim.com
 */

class canonicalSeo extends Module
{
	private $errors=array();
	private $message=false;
	private $settings=array();
	
	function __construct()
	{
		$this->name = 'canonicalseo';
		$this->tab = 'SEO';
                $this->author='BVK Software';
                $this->module_key='a3ed9243d7d1cf4919dbf35dc01559cc';
		switch(substr(_PS_VERSION_, 0, 3)){
			case "1.5":
			    $this->tab = 'seo';
			    break;
			case "1.4":
			    $this->tab = 'seo';
			    break;
		}
		$this->version = 1.2;

		parent::__construct();

		$this->displayName = $this->l('Canonical SEO');
		$this->description = $this->l('Improve SEO by avoiding the "duplicate content" status for your Website.');
		
		$this->settings=array(
				'cseodomain'=>array(
					'o'=>false,
					'l'=>$this->l('Domain Name (e.g. http://www.domain.com)'),
					's'=>'CSEO_DOMAIN'
					),
				'cseoignore'=>array(
					'o'=>false,
					'l'=>$this->l('Parameters to ignore(separated by coma, no spaces)'),
					's'=>'CSEO_IGNORE',
					'v'=>'orderway,orderby,n,qty,p'
					),
				'custom'=>array(
					'o'=>array(
                                            0=>$this->l('Disabled'),
                                            1=>$this->l('Enabled')
                                        ),
					'l'=>$this->l('Custom Redirects'),
					's'=>'CSEO_CUSTOM',
					'v'=>0
					)
				     );
		
		foreach($this->settings AS &$setting){
			if(Configuration::get($setting['s'])!==false){
				$setting['v']=Configuration::get($setting['s']);
			}elseif(!isset($setting['v'])){
			    $setting['v']='';
			}
		}
	}

	function install()
	{
            if (!parent::install() OR !$this->registerHook('header'))
                    return false;

            Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'bvk_cseo_redirects` (
                    `id_redirect` int(10) unsigned NOT NULL auto_increment,
                    `pattern` TEXT,
                    `destination` TEXT,
                    `type` tinyint,
                    `active` tinyint DEFAULT \'0\',
                    PRIMARY KEY  (`id_redirect`),
                    INDEX (`active`)
                    ) DEFAULT CHARSET=utf8');
            return true;
	}
        
        function uninstall(){
            
            Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'bvk_cseo_redirects`');
            
            foreach($this->settings AS &$setting){
                    Configuration::deleteByName($setting['s']);
            }
            
            return parent::uninstall();
        }

        public function getContent()
	{
		global $smarty;
		
		$baseurl='?tab=AdminModules&configure='.$this->name.'&token='.$_GET['token'];
		$smarty->assign('baseurl', $baseurl);
		$smarty->assign('name', $this->displayName);
		
		if(isset($_POST['updatesettings'])){
                    foreach($this->settings AS $n=>&$setting){
                        $setting['v']=Tools::getValue($n);
                        Configuration::updateValue($setting['s'], $setting['v']);
                    }
		}
                
                if(Tools::getValue('deleteredirect')){
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'bvk_cseo_redirects`WHERE id_redirect='.intval(Tools::getValue('deleteredirect')));
                    
                    Tools::redirectAdmin($baseurl);
                }
                
                if(Tools::isSubmit('saveredirect')){
                    if(Tools::getValue('id_redirect')){
                        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'bvk_cseo_redirects` SET pattern="'.pSQL(Tools::getValue('pattern')).'", destination="'.pSQL(Tools::getValue('destination')).'", type='.intval(Tools::getValue('type')).', active='.intval(Tools::getValue('active')).' WHERE id_redirect='.intval(Tools::getValue('id_redirect')));
                    }else{
                        Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'bvk_cseo_redirects`(pattern, destination, type, active) VALUES("'.pSQL(Tools::getValue('pattern')).'", "'.pSQL(Tools::getValue('destination')).'", '.intval(Tools::getValue('type')).', '.intval(Tools::getValue('active')).')');
                    }
                    
                    Tools::redirectAdmin($baseurl);
                }
                
                $redirect=false;
                if(Tools::getValue('id_redirect')){
                    $redirect=Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'bvk_cseo_redirects` WHERE id_redirect='.intval(Tools::getValue('id_redirect')));
                }
                $smarty->assign('redirect', $redirect);
                
                $types=array(
                    0=>$this->l('Canonical'),
                    1=>$this->l('Canonical (Exact URL)'),
                    2=>$this->l('301'),
                    3=>$this->l('301 (Exact URL)')
                );
                $smarty->assign('types', $types);
                
                $redirects=Db::getInstance()->ExecuteS('SELECT SQL_CALC_FOUND_ROWS *
                    FROM '._DB_PREFIX_.'bvk_cseo_redirects
                    WHERE 1
                    LIMIT '.(Tools::getValue('p')?(Tools::getValue('p')-1)*20:'0').', 20');
                
                $pages=ceil(Db::getInstance()->getValue('SELECT FOUND_ROWS()')/20);

                $smarty->assign('pages', $pages);
                $smarty->assign('redirects', $redirects);
                $smarty->assign('currentpage', Tools::getValue('p'));
		
		$smarty->assign('settings', $this->settings);
		$smarty->assign('errors', $this->errors);
		$smarty->assign('message', $this->message);
		$this->_html .=  $this->display(__FILE__, 'admin_main.tpl');
		
		return $this->_html;
	}

	function hookHeader($params)
	{
            if($this->settings['custom']['v']==1){
                $search=str_replace(__PS_BASE_URI__, '', $_SERVER["REQUEST_URI"]);
                if($redirect=Db::getInstance()->getRow('SELECT type, destination FROM '._DB_PREFIX_.'bvk_cseo_redirects WHERE active=1 AND "'.pSQL($search).'" LIKE pattern')){
                    
                    switch ($redirect['type']){
                        case 3:
                            header('Location: '.$redirect['destination']);
                            break;
                        case 2:
                            header('Location: '._PS_BASE_URL_.__PS_BASE_URI__.$redirect['destination']);
                            break;
                        case 1:
                            header('Link: <'.$redirect['destination'].'>; rel="canonical"');
                            break;
                        case 0:
                        default:
                            header('Link: <'._PS_BASE_URL_.__PS_BASE_URI__.$redirect['destination'].'>; rel="canonical"');
                            break;
                    }
                    return;
                }
            }
            if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS']>=300){
                return;
            }
            global $smarty;
            header('Link: <'.$this->getCanonicalUrl().'>; rel="canonical"');
//            $smarty->assign('canonical_url', $this->getCanonicalUrl());
//            return $this->display(__FILE__, 'canonicalseo.tpl');
	}

        function getCanonicalUrl(){
		global $smarty, $protocol, $rewrited_url, $cookie, $link, $page_name;

		$url='';
		if(strlen($rewrited_url)){
			$url=$rewrited_url;
			if(stristr($url, 'http://')===false && strlen($this->settings['cseodomain']['v'])){
				$url=$protocol.$this->settings['cseodomain']['v'].$url;
			}elseif(_PS_BASE_URL_!=$protocol.$this->settings['cseodomain']['v']){
				$url=str_replace(_PS_BASE_URL_, $protocol.$this->settings['cseodomain']['v'], $url);
			}
		}else{

			$urlpars=true;
			if(Configuration::get('PS_REWRITING_SETTINGS')){
				$urlpars=false;

                                switch(substr(_PS_VERSION_, 0, 3)){
                                        case "1.5":
                                        case "1.4":
                                            if(Configuration::get('PS_FORCE_SMARTY_2')){
                                                $page_name=$smarty->get_template_vars('page_name');
                                            }else{
                                                $page_name=$smarty->getTemplateVars('page_name');
                                            }

                                            break;
                                }
				switch($page_name){
					case 'product':
						if($linkrw=Db::getInstance()->getRow('SELECT pl.link_rewrite AS plink, cl.link_rewrite AS clink FROM '._DB_PREFIX_.'product AS p, '._DB_PREFIX_.'product_lang AS pl, '._DB_PREFIX_.'category_lang AS cl WHERE cl.id_lang=pl.id_lang AND cl.id_category=p.id_category_default AND p.id_product=pl.id_product AND pl.id_lang='.intval($cookie->id_lang).' AND pl.id_product='.intval(Tools::getValue('id_product')))){
							$url=$link->getProductLink(intval(Tools::getValue('id_product')), $linkrw['plink'], $linkrw['clink']);
						}
						break;
					case 'category':
						if($linkrw=Db::getInstance()->getRow('SELECT cl.link_rewrite AS clink FROM  '._DB_PREFIX_.'category_lang AS cl WHERE cl.id_lang='.intval($cookie->id_lang).' AND cl.id_category='.intval(Tools::getValue('id_category')))){
							$url=$link->getCategoryLink(intval(Tools::getValue('id_category')), $linkrw['clink']);
						}
						break;
					case 'cms':
						if($linkrw=Db::getInstance()->getRow('SELECT cl.link_rewrite AS cmslink FROM  '._DB_PREFIX_.'cms_lang AS cl WHERE cl.id_lang='.intval($cookie->id_lang).' AND cl.id_cms='.intval(Tools::getValue('id_cms')))){
							$url=$link->getCMSLink(intval(Tools::getValue('id_cms')), $linkrw['cmslink']);
						}
						break;
					case 'supplier':
						if($linkrw=Db::getInstance()->getRow('SELECT s.name FROM  '._DB_PREFIX_.'supplier AS s WHERE s.id_supplier='.intval(Tools::getValue('id_supplier')))){
							$url=$link->getSupplierLink(intval(Tools::getValue('id_supplier')), Tools::link_rewrite($linkrw['name']));
						}
						break;
					case 'manufacturer':
						if($linkrw=Db::getInstance()->getRow('SELECT m.name FROM  '._DB_PREFIX_.'manufacturer AS m WHERE m.id_manufacturer='.intval(Tools::getValue('id_manufacturer')))){
							$url=$link->getManufacturerLink(intval(Tools::getValue('id_manufacturer')), Tools::link_rewrite($linkrw['name']));
						}
						break;
					default:
						$urlpars=true;
                                                break;
				}

			}
			if($urlpars){
				if(strlen($this->settings['cseodomain']['v'])){
					$url=$protocol.$this->settings['cseodomain']['v'];
				}

				$uri=$_SERVER["REQUEST_URI"];
				$ignorelist=explode(',', $this->settings['cseoignore']['v']);
				if(sizeof($ignorelist)>0 && strpos($uri, '?')){
					if($cookie->id_lang==Configuration::get('PS_LANG_DEFAULT')){
						$ignorelist[]='id_lang';
					}
					foreach($ignorelist as &$ignore){
						$ignore='/'.$ignore.'=?[^\&]*[\&]?/';
					}
					$ignorelist[]='/\&$/';
//					$ignorelist[]='/\?$/';
//					$uri=preg_replace($ignorelist, '', $uri);
                                        $uri=explode('?', $uri);
					$uri[1]=preg_replace($ignorelist, '', $uri[1]);
                                        $uri=$uri[0].(strlen($uri[1])?'?'.$uri[1]:'');
				}
				$url.=$uri;
			}elseif(_PS_BASE_URL_!=$protocol.$this->settings['cseodomain']['v']){
				$url=str_replace(_PS_BASE_URL_, $protocol.$this->settings['cseodomain']['v'], $url);
			}
		}
                return $url;

        }
}

?>
