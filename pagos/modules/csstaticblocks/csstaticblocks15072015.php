<?php
include_once(dirname(__FILE__).'/StaticBlockClass.php');
class csstaticblocks extends Module
{
	protected $error = false;
	private $_html;
	private $myHook = array('displayTop','displayLeftColumn','displayRightColumn','displayFooter','displayHome','homebottom','footertop','footerbottom');
	
	public function __construct()
	{
	 	$this->name = 'csstaticblocks';
	 	$this->tab = 'MyBlocks';
	 	$this->version = '1.0';
		$this->author = 'Codespot';
	 	parent::__construct();

		$this->displayName = $this->l('Cs Static block');
		$this->description = $this->l('Adds static blocks with free content');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete your static blocks?');
	
	}
	public function init_data()
	{
		$content_block1 = '<div class="banner_home_center"><a title="" href="#"><img src="{static_block_url}themes/gomarket/img/cms/ads_fashsion.jpg" alt="" /></a></div>';
		$content_block1_fr='<div class="banner_home_center"><a title="" href="#"><img src="{static_block_url}themes/gomarket/img/cms/ads_fashsion.jpg" alt="" /></a></div>';
		$content_block2 = '<div class="banner_footer_top"><a title="" href="#"><img src="{static_block_url}themes/gomarket/img/cms/ads_feature.jpg" alt="" /></a></div>';
		$content_block2_fr='<div class="banner_footer_top"><a title="" href="#"><img src="{static_block_url}themes/gomarket/img/cms/ads_feature.jpg" alt="" /></a></div>';
		$content_block3 = '<div class="block cs_top_banner_footer">
		<h4>Lorem ipsum dolor</h4>
		<ul class="row_1">
		<li class="item"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_hm.jpg" alt="" /></a></li>
		<li class="item"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_look.jpg" alt="" /></a></li>
		<li class="item"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_online.jpg" alt="" /></a></li>
		</ul>
		<ul class="row_2">
		<li class="item first"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_watch.jpg" alt="" /></a></li>
		<li class="item last"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_digital.jpg" alt="" /></a></li>
		</ul>
		</div>';
		$content_block3_fr='<div class="block cs_top_banner_footer">
		<h4>Lorem ipsum dolor</h4>
		<ul class="row_1">
		<li class="item"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_hm.jpg" alt="" /></a></li>
		<li class="item"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_look.jpg" alt="" /></a></li>
		<li class="item"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_online.jpg" alt="" /></a></li>
		</ul>
		<ul class="row_2">
		<li class="item first"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_watch.jpg" alt="" /></a></li>
		<li class="item last"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/f_digital.jpg" alt="" /></a></li>
		</ul>
		</div>';
		$content_block4 ='<div class="cs_lastest_new">
		<div class="block">
		<h4>Lastest New</h4>
		<a class="show_hide_footer" href="javascript:void(0)">icon</a>
		<ul class="cs_content f_block_content">
		<li><a href="#">Preview Autum Winter 2013</a></li>
		<li><a href="#">Stock Clearence Sales - Save 80%</a></li>
		<li><a href="#">Exdests Stores New Design Online</a></li>
		<li><a href="#">New Customer Offer - $15 Off</a></li>
		<li><a href="#">Grand Store Opening on 05/02</a></li>
		</ul>
		</div>
		</div>';
		$content_block4_fr='<div class="cs_lastest_new">
		<div class="block">
		<h4>Dernières New</h4>
		<a class="show_hide_footer" href="javascript:void(0)">icon</a>
		<ul class="cs_content f_block_content">
		<li><a href="#">Aperçu Automne Hiver 2013</a></li>
		<li><a href="#">Banque Clearence ventes - Gagnez 80%</a></li>
		<li><a href="#">Exdests Stores New Design Online</a></li>
		<li><a href="#">Nouvelle offre à la clientèle - 15 $ Off</a></li>
		<li><a href="#">Grande ouverture du magasin le 05/02</a></li>
		</ul>
		</div>
		</div>';
		$content_block5 = '<div class="text_footer_bottom">
				<div class="static-footer-payment">
				<h4>Payment method</h4>
				<p class="icon"><a title="visa" href="#"><img title="visa" src="{static_block_url}themes/gomarket/img/cms/payment_1.png" alt="visa" width="39" height="18" /></a> <a title="mastercard" href="#"><img title="mastercard" src="{static_block_url}themes/gomarket/img/cms/mastercard.png" alt="mastercard" width="27" height="18" /></a> <a title="express" href="#"><img title="express" src="{static_block_url}themes/gomarket/img/cms/american.png" alt="express" width="16" height="18" /></a> <a title="paypal" href="#"><img title="paypal" src="{static_block_url}themes/gomarket/img/cms/paypal.png" alt="paypal" width="60" height="18" /></a> <a title="skrill" href="#"><img title="skrill" src="{static_block_url}themes/gomarket/img/cms/skrill.png" alt="skrill" width="48" height="18" /></a></p>
				</div>
				<div class="static-footer-follow">
				<h4>Follow Us On</h4>
				<p class="icon">
				<a title="facebook" href="https://www.facebook.com/emthemes">
				<img title="facebook" src="{static_block_url}themes/gomarket/img/cms/face.png" alt="facebook" width="34" height="34" /></a> 
				<a title="twitter" href="https://twitter.com/eggthemes"><img title="mastercard" src="{static_block_url}themes/gomarket/img/cms/i_twitter.png" alt="twitter" width="34" height="34" /></a>
				<a title="flickr" href="http://www.flickr.com/"><img title="flickr" src="{static_block_url}themes/gomarket/img/cms/i_vimeo.png" alt="flickr" width="34" height="34" /></a> <a title="rss" href="?rss"><img title="rss" src="{static_block_url}themes/gomarket/img/cms/i_rss.png" alt="rss" width="34" height="34" /></a></p>
				</div>
				<div class="static-footer-link"><a href="#">Fusce Uttest</a><a href="#">Diam Tempor</a><a href="#">Justo Malesuada</a><a href="#">Volutpat</a><a href="#">Aliquam Auctor</a><a href="#">Elit Quis</a><a href="#">Magna Porta</a><a class="last" href="#">Commodo</a><br /> <a href="#">Fusce Uttest</a><a href="#">Diam Tempor</a><a href="#">Justo Malesuada</a><a href="#">Volutpat</a><a class="last" href="#">Aliquam Auctor</a><br /> <a href="#">Diam Tempor</a><a href="#">Volutpat</a><a class="last" href="#">Aliquam Auctor</a></div>
				<p class="copy"><span>©2013 GoMarket Demo Store. All rights reserved.</span><a href="#">GoMarket Theme</a> by <a href="#">PresThemes</a>.</p>
				</div>';
		$content_block5_fr ='<div class="text_footer_bottom">
		<div class="static-footer-payment">
		<h4>Payment method</h4>
		<p class="icon"><a title="visa" href="#"><img title="visa" src="{static_block_url}themes/gomarket/img/cms/payment_1.png" alt="visa" width="39" height="18" /></a> <a title="mastercard" href="#"><img title="mastercard" src="{static_block_url}themes/gomarket/img/cms/mastercard.png" alt="mastercard" width="27" height="18" /></a> <a title="express" href="#"><img title="express" src="{static_block_url}themes/gomarket/img/cms/american.png" alt="express" width="16" height="18" /></a> <a title="paypal" href="#"><img title="paypal" src="{static_block_url}themes/gomarket/img/cms/paypal.png" alt="paypal" width="60" height="18" /></a> <a title="skrill" href="#"><img title="skrill" src="{static_block_url}themes/gomarket/img/cms/skrill.png" alt="skrill" width="48" height="18" /></a></p>
		</div>
		<div class="static-footer-follow">
		<h4>Follow Us On</h4>
		<p class="icon">
		<a title="facebook" href="https://www.facebook.com/emthemes">
		<img title="facebook" src="{static_block_url}themes/gomarket/img/cms/face.png" alt="facebook" width="34" height="34" /></a> 
		<a title="twitter" href="https://twitter.com/eggthemes"><img title="mastercard" src="{static_block_url}themes/gomarket/img/cms/i_twitter.png" alt="twitter" width="34" height="34" /></a>
		<a title="flickr" href="http://www.flickr.com/"><img title="flickr" src="{static_block_url}themes/gomarket/img/cms/i_vimeo.png" alt="flickr" width="34" height="34" /></a> <a title="rss" href="?rss"><img title="rss" src="{static_block_url}themes/gomarket/img/cms/i_rss.png" alt="rss" width="34" height="34" /></a></p>
		</div>
		<div class="static-footer-link"><a href="#">Fusce Uttest</a><a href="#">Diam Tempor</a><a href="#">Justo Malesuada</a><a href="#">Volutpat</a><a href="#">Aliquam Auctor</a><a href="#">Elit Quis</a><a href="#">Magna Porta</a><a class="last" href="#">Commodo</a><br /> <a href="#">Fusce Uttest</a><a href="#">Diam Tempor</a><a href="#">Justo Malesuada</a><a href="#">Volutpat</a><a class="last" href="#">Aliquam Auctor</a><br /> <a href="#">Diam Tempor</a><a href="#">Volutpat</a><a class="last" href="#">Aliquam Auctor</a></div>
		<p class="copy"><span>©2013 GoMarket Demo Store. All rights reserved.</span><a href="#">GoMarket Theme</a> by <a href="#">PresThemes</a>.</p>
		</div>';
		$content_block6 = '<div class="banner_home_bottom"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/ads_ipod.jpg" alt="" /></a></div>';
		$content_block6_fr ='<div class="banner_home_bottom"><a href="#"><img src="{static_block_url}themes/gomarket/img/cms/ads_ipod.jpg" alt="" /></a></div>';
		$hook_footerbottom = Hook::getIdByName('footerbottom');
		$hook_homebottom = Hook::getIdByName('homebottom');
		$hook_footertop = Hook::getIdByName('footertop');
		$id_en = Language::getIdByIso('en');
		$id_fr = Language::getIdByIso('fr');
		$id_shop = Configuration::get('PS_SHOP_DEFAULT');
		if(!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "1", "banner-home-center","8", "1")') OR 
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "2", "banner_footer_top",'.$hook_footertop.', "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "3", "banner-footer","21", "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "4", "last-new","21", "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "5", "text_footer_bottom",'.$hook_footerbottom.', "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "6", "banner-home-bottom",'.$hook_homebottom.', "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "1", \''.$id_en.'\', \''.$id_shop.'\',"Banner Home Center", \''.$content_block1.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "1",\''.$id_fr.'\',\''.$id_shop.'\',"Banner Home Center", \''.$content_block1_fr.'\')') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "2", \''.$id_en.'\', \''.$id_shop.'\',"Banner footer top", \''.$content_block2.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "2",\''.$id_fr.'\',\''.$id_shop.'\',"Banner footer top", \''.$content_block2_fr.'\')') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "3", \''.$id_en.'\', \''.$id_shop.'\',"Banner Footer", \''.$content_block3.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "3",\''.$id_fr.'\',\''.$id_shop.'\',"Banner Footer", \''.$content_block3_fr.'\')') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "4", \''.$id_en.'\', \''.$id_shop.'\',"Lastest New", \''.$content_block4.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "4",\''.$id_fr.'\',\''.$id_shop.'\',"Lastest New", \''.$content_block4_fr.'\')') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "5", \''.$id_en.'\', \''.$id_shop.'\',"Text footer bottom", \''.$content_block5.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "5",\''.$id_fr.'\',\''.$id_shop.'\',"Text footer bottom", \''.$content_block5_fr.'\')') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "6", \''.$id_en.'\', \''.$id_shop.'\',"Banner Home Bottom", \''.$content_block6.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`,`id_shop`, `title`, `content`) VALUES ( "6",\''.$id_fr.'\',\''.$id_shop.'\',"Banner Home Bottom", \''.$content_block6_fr.'\')') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_shop` (`id_block`, `id_shop`, `is_active`) VALUES ( "1",\''.$id_shop.'\',"1")') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_shop` (`id_block`, `id_shop`, `is_active`) VALUES ( "2",\''.$id_shop.'\',"1")') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_shop` (`id_block`, `id_shop`, `is_active`) VALUES ( "3",\''.$id_shop.'\',"1")') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_shop` (`id_block`, `id_shop`, `is_active`) VALUES ( "4",\''.$id_shop.'\',"1")') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_shop` (`id_block`, `id_shop`, `is_active`) VALUES ( "5",\''.$id_shop.'\',"1")') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_shop` (`id_block`, `id_shop`, `is_active`) VALUES ( "6",\''.$id_shop.'\',"1")'))
			return false;
		return true;
		
	}
	
	
	
	public function install()
	{		
	 	if (parent::install() == false OR !$this->registerHook('header'))
	 		return false;
		foreach ($this->myHook AS $hook){
			if ( !$this->registerHook($hook))
				return false;
		}
	 	if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'staticblock (`id_block` int(10) unsigned NOT NULL AUTO_INCREMENT, `identifier_block` varchar(255) NOT NULL DEFAULT \'\', `hook` int(10) unsigned, `is_active` tinyint(1) NOT NULL DEFAULT \'1\', PRIMARY KEY (`id_block`),UNIQUE KEY `identifier_block` (`identifier_block`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'staticblock_shop (`id_block` int(10) unsigned NOT NULL,`id_shop` int(10) unsigned NOT NULL,`is_active` tinyint(1) NOT NULL DEFAULT \'1\',PRIMARY KEY (`id_block`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'staticblock_lang (`id_block` int(10) unsigned NOT NULL, `id_lang` int(10) unsigned NOT NULL,`id_shop` int(10) unsigned NOT NULL, `title` varchar(255) NOT NULL DEFAULT \'\', `content` mediumtext, PRIMARY KEY (`id_block`,`id_lang`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		$this->init_data();
	 	return true;
	}
	
	public function uninstall()
	{
		
	 	if (parent::uninstall() == false)
	 		return false;
	 	if (!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'staticblock') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'staticblock_shop') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'staticblock_lang'))
	 		return false;
		foreach($this->myHook as $hook)
		{
			$this->clearCacheBlockForHook($hook);
		}
	 	return true;
	}
	
	private function _displayHelp()
	{
		$this->_html .= '
		<br/>
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Static block Helper').'</legend>
			<div>This module customize static contents on the site. Static contents are displayed at the position of the hook : top, left, home,right, footer.</div>
		</fieldset>';
	}
	
	public function getContent()
   	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_postProcess();
		if (Tools::isSubmit('addBlock'))
			$this->_displayAddForm();
		elseif (Tools::isSubmit('editBlock'))
			$this->_displayUpdateForm();
		else
			$this->_displayForm();
		$this->_displayHelp();
		return $this->_html;
	}
	
	private function _postProcess()
	{
		global $currentIndex;
		$errors = array();
		if (Tools::isSubmit('saveBlock'))
		{
			$block = new StaticBlockClass(Tools::getValue('id_block'));
			$block->copyFromPost();
			$errors = $block->validateController();		
			if (sizeof($errors))
			{
				$this->_html .= $this->displayError(implode('<br />', $errors));
			}
			else
			{
				Tools::getValue('id_block') ? $block->update() : $block->add();
				$this->clearCacheBlockForHook(Tools::getValue('hook'));
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveBlockConfirmation');
			}
		}
		elseif (Tools::isSubmit('changeStatusStaticblock') AND Tools::getValue('id_block'))
		{
			$stblock = new StaticBlockClass(Tools::getValue('id_block'));
			$stblock->updateStatus(Tools::getValue('status'));
			$this->clearCacheBlockForHook(Tools::getValue('hook'));
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('deleteBlock') AND Tools::getValue('id_block'))
		{
			$block = new StaticBlockClass(Tools::getValue('id_block'));
			$block->delete();
			$this->clearCacheBlockForHook(Tools::getValue('hook'));
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteBlockConfirmation');
		}
		elseif (Tools::isSubmit('saveBlockConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Static block has been saved successfully'));
		elseif (Tools::isSubmit('deleteBlockConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Static block deleted successfully'));
		
	}
	
	private  function clearCacheBlockForHook($hook)
	{
		
		$this->_clearCache('csstaticblocks_'.strtolower($this->getHookName($hook)).'.tpl');
	}
	 private function getHookName($id_hook,$name=false)
	{
		if (!$result = Db::getInstance()->getRow('
		SELECT `name`,`title`
		FROM `'._DB_PREFIX_.'hook` 
		WHERE `id_hook` = '.(int)($id_hook)))
			return false;
		return $result['name'];
	}
	private function getBlocks()
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
	 	if (!$result = Db::getInstance()->ExecuteS(
			'SELECT b.id_block, b.identifier_block, b.hook, bs.is_active, bl.`title`, bl.`content` 
			FROM `'._DB_PREFIX_.'staticblock` b 
			LEFT JOIN `'._DB_PREFIX_.'staticblock_shop` bs ON (b.`id_block` = bs.`id_block` )
			LEFT JOIN `'._DB_PREFIX_.'staticblock_lang` bl ON (b.`id_block` = bl.`id_block`'.( $id_shop ? 'AND bl.`id_shop` = '.$id_shop : ' ' ).') 
			WHERE bl.id_lang = '.(int)$id_lang.
			( $id_shop ? ' AND bs.`id_shop` = '.$id_shop : ' ' )))
	 		return false;
	 	return $result;
	}
	
	private function getHookTitle($id_hook,$name=false)
	{
		if (!$result = Db::getInstance()->getRow('
			SELECT `name`,`title`
			FROM `'._DB_PREFIX_.'hook` 
			WHERE `id_hook` = '.(int)($id_hook)))
			return false;
		return (($result['title'] != "" && $name) ? $result['title'] : $result['name']);
	}
	
	private function _displayForm()
	{
		global $currentIndex, $cookie;
	 	$this->_html .= '
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('List of static blocks').'</legend>
			<p><a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addBlock"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="" /> '.$this->l('Add a new block').'</a></p><br/>
			<table width="100%" class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr class="nodrag nodrop">
				<th>'.$this->l('ID').'</th>
				<th class="center">'.$this->l('Title').'</th>
				<th class="center">'.$this->l('Identifier').'</th>
				<th class="center">'.$this->l('Hook into').'</th>
				<th class="right">'.$this->l('Active').'</th>
			</tr>
			</thead>
			<tbody>';
		$s_blocks = $this->getBlocks();
		if (is_array($s_blocks))
		{
			static $irow;
			foreach ($s_blocks as $block)
			{
				$this->_html .= '
				<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
					<td class="pointer" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.$block['id_block'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.$block['title'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.$block['identifier_block'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.(Validate::isInt($block['hook']) ? $this->getHookTitle($block['hook']) : '').'</td>
					<td class="pointer center"> <a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&changeStatusStaticblock&id_block='.$block['id_block'].'&status='.$block['is_active'].'&hook='.$block['hook'].'">'.($block['is_active'] ? '<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" />' : '<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" />').'</a> </td>
				</tr>';
			}
		}
		$this->_html .= '
			</tbody>
			</table>
		</fieldset>';
			
		
	}
	
	private function _displayAddForm()
	{
		global $currentIndex, $cookie;
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv¤contentdiv';
		// TinyMCE
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
		<script type="text/javascript">	
		var iso = \''.$isoTinyMCE.'\' ;
		var pathCSS = \''._THEME_CSS_DIR_.'\' ;
		var ad = \''.$ad.'\' ;
		</script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>	
		<script type="text/javascript">
		$(document).ready(function(){		
			tinySetup({});});
		</script>
		';
		// Form
		$this->_html .= '
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('New block').'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
				<label>'.$this->l('Title:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="titlediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.Tools::getValue('title_'.$language['id_lang']).'" size="55" /><sup> *</sup>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlediv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Identifier:').'</label>
				<div class="margin-form">
					<div id="identifierdiv" style="float: left;">
						<input type="text" name="identifier_block" value="'.Tools::getValue('identifier_block').'" size="55" /><sup> *</sup>
					</div>
					<p class="clear">'.$this->l('Identifier must be unique').'. '.$this->l('Match a-zA-Z-_0-9').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Hook into:').'</label>
				<div class="margin-form">
					<div id="hookdiv" style="float: left;">
						<select name="hook">
							<option value="0">'.$this->l('None').'</option>';

		foreach ($this->myHook AS $hook){
			$id_hook = Hook::getIdByName($hook);
			$this->_html .= '<option value="'.$id_hook.'"'.($id_hook == Tools::getValue('hook') ? 'selected="selected"' : '').'>'.$this->getHookTitle($id_hook).'</option>';
		}
		
		$this->_html .= '
						</select>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Active:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="is_active" value="1"'.(Tools::getValue('is_active',1) ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="is_active" value="0"'.(Tools::getValue('is_active',1) ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Content:').'</label>
				<div class="margin-form">';									
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="contentdiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="content_'.$language['id_lang'].'" id="contentInput_'.$language['id_lang'].'" cols="100" rows="20">'.Tools::getValue('content_'.$language['id_lang']).'</textarea>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'contentdiv', true);
					$this->_html .= '
					<div class="clear"></div>
				</div>			
				<div class="margin-form">';
					$this->_html .= '<input type="submit" class="button" name="saveBlock" value="'.$this->l('Save Block').'" id="saveBlock" />
									';
					$this->_html .= '					
				</div>
				
			</form>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
		</fieldset>';
	}
	
	private function _displayUpdateForm()
	{
		global $currentIndex, $cookie;
		if (!Tools::getValue('id_block'))
		{
			$this->_html .= '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>';
			return;
		}

		$block = new StaticBlockClass((int)Tools::getValue('id_block'));
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv¤contentdiv';
		// TinyMCE
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
		<script type="text/javascript">	
		var iso = \''.$isoTinyMCE.'\' ;
		var pathCSS = \''._THEME_CSS_DIR_.'\' ;
		var ad = \''.$ad.'\' ;
		</script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>	
		<script type="text/javascript">
		$(document).ready(function(){		
			tinySetup({});});
		</script>
		';
		// Form
		$this->_html .= '
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Edit block').' '.$block->identifier_block.'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
				<input type="hidden" name="id_block" value="'.(int)$block->id_block.'" id="id_block" />
				<div class="margin-form">
					<input type="submit" class="button " name="deleteBlock" value="'.$this->l('Delete Block').'" id="deleteBlock" onclick="if (!confirm(\'Are you sure that you want to delete this static blocks?\')) return false "/>
				</div>
				<label>'.$this->l('Title:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="titlediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.(isset($block->title[$language['id_lang']]) ? $block->title[$language['id_lang']] : '').'" size="55" /><sup> *</sup>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlediv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Identifier:').'</label>
				<div class="margin-form">
					<div id="identifierdiv" style="float: left;">
						<input type="text" name="identifier_block" value="'.$block->identifier_block.'" size="55" /><sup> *</sup>
					</div>
					<p class="clear">'.$this->l('Identifier must be unique').'. '.$this->l('Match a-zA-Z-_0-9').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Hook into:').'</label>
				<div class="margin-form">
					<div id="hookdiv" style="float: left;">
						<select name="hook">
							<option value="0">'.$this->l('None').'</option>';
		foreach ($this->myHook AS $hook){
			$id_hook = Hook::getIdByName($hook);
			$this->_html .= '<option value="'.$id_hook.'"'.($id_hook == $block->hook ? 'selected="selected"' : '').'>'.$this->getHookTitle($id_hook).'</option>';
		}
		$this->_html .= '
						</select>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Status:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="is_active" '.($block->is_active ? 'checked="checked"' : '').' value="1" />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="is_active" '.($block->is_active ? '' : 'checked="checked"').' value="0" />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Content:').'</label>
				<div class="margin-form">';									
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="contentdiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="content_'.$language['id_lang'].'" id="contentInput_'.$language['id_lang'].'" cols="100" rows="20">'.(isset($block->content[$language['id_lang']]) ? $block->content[$language['id_lang']] : '').'</textarea>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'contentdiv', true);
					$this->_html .= '
					<div class="clear"></div>
				</div>			
				<div class="margin-form">';
					$this->_html .= '<input type="submit" class="button" name="saveBlock" value="'.$this->l('Save Block').'" id="saveBlock" />';
					$this->_html .= '					
				</div>
				
			</form>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
		</fieldset>';
	}

	public function contentById($id_block)
	{
		global $cookie;

		$staticblock = new StaticBlockClass($id_block);
		return ($staticblock->is_active ? $staticblock->content[(int)$cookie->id_lang] : '');
	}
	
	public function contentByIdentifier($identifier)
	{
		global $cookie;

		if (!$result = Db::getInstance()->getRow('
			SELECT `id_block`,`identifier_block`
			FROM `'._DB_PREFIX_.'staticblock` 
			WHERE `identifier_block` = \''.$identifier.'\''))
			return false;
		$staticblock = new StaticBlockClass($result['id_block']);
		return ($staticblock->is_active ? $staticblock->content[(int)$cookie->id_lang] : '');
	}
	
	private function getBlockInHook($hook_name)
	{
		$block_list = array();
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_hook = Hook::getIdByName($hook_name);
		if ($id_hook)
		{
			$results = Db::getInstance()->ExecuteS('SELECT b.`id_block` FROM `'._DB_PREFIX_.'staticblock` b
			LEFT JOIN `'._DB_PREFIX_.'staticblock_shop` bs ON (b.id_block = bs.id_block)
			WHERE bs.is_active = 1 AND (bs.id_shop = '.(int)$id_shop.') AND b.`hook` = '.(int)($id_hook));
			foreach ($results as $row)
			{
				$temp = new StaticBlockClass($row['id_block']);
				$block_list[] = $temp;
			}
		}	
		
		return $block_list;
	}
	
	public function hookDisplayTop()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_top');
		}
		if (!$this->isCached('csstaticblocks_top.tpl', $smarty_cache_id))
		{
			$block_list = $this->getBlockInHook('displayTop');
			
			$smarty->assign(array(
				'block_list' => $block_list
			));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_top.tpl', $smarty_cache_id);
	}
	
	public function hookDisplayLeftColumn()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_leftcolumn');
		}
		if (!$this->isCached('csstaticblocks_leftcolumn.tpl', $smarty_cache_id))
		{
			$block_list = $this->getBlockInHook('displayLeftColumn');
			
			$smarty->assign(array(
				'block_list' => $block_list
			));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_leftcolumn.tpl', $smarty_cache_id);
	}
	
	public function hookDisplayRightColumn()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_rightcolumn');
		}
		if (!$this->isCached('csstaticblocks_rightcolumn.tpl', $smarty_cache_id))
		{
		$block_list = $this->getBlockInHook('displayRightColumn');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_rightcolumn.tpl',$smarty_cache_id);
	}
	
	public function hookDisplayFooter()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_footer');
		}
		if (!$this->isCached('csstaticblocks_footer.tpl',$smarty_cache_id))
		{
			$block_list = $this->getBlockInHook('displayFooter');
			$smarty->assign(array(
				'block_list' => $block_list
			));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_footer.tpl', $smarty_cache_id);
	}
	
	public function hookDisplayHome()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_home');
		}
		if (!$this->isCached('csstaticblocks_home.tpl', $smarty_cache_id))
		{
			$block_list = $this->getBlockInHook('displayHome');
			$smarty->assign(array(
				'block_list' => $block_list
			));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_home.tpl', $smarty_cache_id);
	}
	public function hookHomeBottom()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_homebottom');
		}
		if (!$this->isCached('csstaticblocks_homebottom.tpl', $smarty_cache_id))
		{
			$block_list = $this->getBlockInHook('homebottom');
			$smarty->assign(array(
				'block_list' => $block_list
			));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_homebottom.tpl', $smarty_cache_id);
	}
	public function hookFooterTop()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_footertop');
		}
		if (!$this->isCached('csstaticblocks_footertop.tpl', $smarty_cache_id))
		{
			$block_list = $this->getBlockInHook('footertop');
			$smarty->assign(array(
				'block_list' => $block_list
			));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_footertop.tpl', $smarty_cache_id);
	}
	public function hookFooterBottom()
	{
		global $smarty, $cookie;
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
		{
			$smarty_cache_id = $this->name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id;
			$this->context->smarty->cache_lifetime = 31536000;
			Tools::enableCache();
		}
		else 
		{
			$smarty_cache_id = $this->getCacheId('csstaticblocks_footerbottom');
		}
		if (!$this->isCached('csstaticblocks_footerbottom.tpl', $smarty_cache_id))
		{
			$block_list = $this->getBlockInHook('footerbottom');
			$smarty->assign(array(
				'block_list' => $block_list
			));
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csstaticblocks_footerbottom.tpl', $smarty_cache_id);
	}
	public function hookHeader()
	{
		global $smarty;
		$smarty->assign(array(
			'HOOK_CS_FOOTER_TOP' => Hook::Exec('footertop'),
			'HOOK_CS_FOOTER_BOTTOM' => Hook::Exec('footerbottom'),
			'HOOK_CS_HOME_BOTTOM' => Hook::Exec('homebottom')
		));
	}
	
}
?>
