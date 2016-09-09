<?php
include_once(dirname(__FILE__).'/SliderClass.php');
include_once(dirname(__FILE__).'/Caption.php');

class CsSlider extends Module
{
	private $_html;
	private $transitions = array("boxslide","boxfade","slotzoom-horizontal","slotslide-horizontal","slotfade-horizontal","slotzoom-vertical","slotslide-vertical","slotfade-vertical","curtain-1","curtain-2","curtain-3","slideleft","slideright","slideup","slidedown","fade","random","slidehorizontal","slidevertical","papercut","flyin","turnoff","cube","3dcurtain-vertical","3dcurtain-horizontal");
	private $incomAnimation = array("sft"=>"Short from Top","sfb"=>"Short from Bottom","sfr"=>"Short from Right","sfl"=>"Short from Left","lft"=>"Long from Top","lfb"=>"Long from Bottom","lfr"=>"Long from Right","lfl"=>"Long from Left","fade"=>"fading","randomrotate"=>"randomrotate");
	private $outgoAnimation = array("stt"=>"Short to Top","stb"=>"Short to Bottom","str"=>"Short to Right","stl"=>"Short to Left","ltt"=>"Long to Top","ltb"=>"Long to Bottom","ltr"=>"Long to Right","ltl"=>"Long to Left","fadeout"=>"fading","randomrotateout"=>"Fade in");
	private $easing = array("easeOutBack","easeInQuad","easeOutQuad","easeInOutQuad","easeInCubic","easeOutCubic","easeInQuart","easeOutQuart","easeInOutQuart","easeInQuint","easeOutQuint","easeInOutQuint","easeInSine","easeOutSine","easeInOutSine","easeInExpo","easeOutExpo","easeInOutExpo","easeInCirc","easeOutCirc","easeInOutCirc","easeInElastic","easeOutElastic","easeInOutElastic","easeInBack","easeOutBack","easeInOutBack","easeInBounce","easeOutBounce","easeInOutBounce");
	private $optionList = array(
				"delay"=>"9000",
				"startheight"=>"500",
				"startwidth"=>"1180",
				"touchenabled"=>"on",
				"onhoverstop"=>"on",
				"timerline"=>"true",
				"timerlineposition"=>"top",
				"navigationtype"=>"bullet",
				"navigationarrow"=>"solo",
				"navigationstyle"=>"round",
				"navigationhalign"=>"center",
				"navigationvalign"=>"bottom",
				"navigationhoffset"=>"0",
				"navigationvoffset"=>"0",
				"soloarrowlefthalign"=>"left",
				"soloarrowleftvalign"=>"center",
				"soloarrowlefthoffset"=>"20",
				"soloarrowleftvoffset"=>"0",
				"soloarrowrighthalign"=>"right",
				"soloarrowrightvalign"=>"center",
				"soloarrowrighthoffset"=>"20",
				"soloarrowrightvoffset"=>"0",
				"timehidethumbnail"=>"10",
				"thumbnailwidth"=>"100",
				"thumbnailheight"=>"100",
				"thumbamount"=>"2",
				"hidecapptionatlimit"=>"0",
				"hideallcapptionatlimit"=>"0",
				"hideslideratlimit"=>"0",
				"shadow"=>"0",
				"stopatslide"=>"-1",
				"stopafterloops"=>"-1"
				);
	private $classList = array("big_white","big_orange","big_black","medium_grey","small_text","medium_text","large_text","large_black_text","very_large_text","very_large_black_text","bold_red_text","bold_brown_text","bold_green_text","very_big_white","very_big_black");
	public function __construct()
	{
	 	$this->name = 'csslider';
	 	$this->tab = 'MyBlocks';
	 	$this->version = '1.0';
		$this->author = 'CodeSpot';

	 	parent::__construct();

		$this->displayName = $this->l('CS Slider');
		$this->description = $this->l('Add a JQuery Revolution Slider on the homepage');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete your CSSlider?');

	}
	public function init_data()
	{
		$id_en = Language::getIdByIso('en');
		$id_fr = Language::getIdByIso('fr');
		$id_shop = Configuration::get('PS_SHOP_DEFAULT');
		if(!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'csslider` (`id_slider`, `url`, `animation`, `image`, `position`, `display`) VALUES 
		(1, "http://presthemes.com/",\'{"transitions":"slidehorizontal","slotamount":"7","masterspeed":"300","target":"_self","delay":"9000","enablelink":"1","enablefullvideo":"1"}\',\'{"type":"image","value":"1_1.jpg"}\', 1, 1),
		(2, "http://presthemes.com/", \'{"transitions":"curtain-1","slotamount":"7","masterspeed":"300","target":"_self","delay":"9400","enablelink":"1","enablefullvideo":"1"}\', \'{"type":"image","value":"1_7.jpg"}\', 2, 1),
		(3, "http://presthemes.com/", \'{"transitions":"slotslide-vertical","slotamount":"7","masterspeed":"300","target":"_self","delay":"9000","enablelink":"1","enablefullvideo":"1"}\', \'{"type":"image","value":"1_8.jpg"}\', 3, 1)') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'csslider_shop` (`id_slider`, `id_shop`, `url`, `animation`, `image`, `position`, `display`) VALUES 
		(1, "'.$id_shop.'","http://presthemes.com/",\'{"transitions":"slidehorizontal","slotamount":"7","masterspeed":"300","target":"_self","delay":"9000","enablelink":"1","enablefullvideo":"1"}\',\'{"type":"image","value":"1_1.jpg"}\', 0, 1),
		(2,"'.$id_shop.'", "http://presthemes.com/", \'{"transitions":"curtain-1","slotamount":"7","masterspeed":"300","target":"_self","delay":"9400","enablelink":"1","enablefullvideo":"1"}\', \'{"type":"image","value":"1_7.jpg"}\', 1, 1),
		(3,"'.$id_shop.'", "http://presthemes.com/", \'{"transitions":"slotslide-vertical","slotamount":"7","masterspeed":"300","target":"_self","delay":"9000","enablelink":"1","enablefullvideo":"1"}\', \'{"type":"image","value":"1_8.jpg"}\', 2, 1)') OR
		
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'csslider_caption` (`id_caption`, `id_slider`, `layer`, `content`) VALUES 
		(1, 1, 0, \'{"type_caption":"imagecaption","datax":"415","datay":"2","class":"","dataspeed":"900","datastart":"500","incomanimation":"lft","outgoanimation":"ltb","easing":"easeOutBack","endeasing":"easeInQuad","image_1":"1_1.png","image_2":"1_1.png"}\'),
		(2, 1, 1, \''.pSQL('{"type_caption":"textcaption","datax":"415","datay":"75","class":"big_white","dataspeed":"300","datastart":"700","incomanimation":"sfr","outgoanimation":"randomrotateout","easing":"easeOutExpo","endeasing":"easeInExpo","text":{"1":"Responsive<br\/> Design","2":"Responsive<br\/> Design"}}').'\'),
		(3, 1, 2, \''.pSQL('{"type_caption":"textcaption","datax":"415","datay":"145","class":"","dataspeed":"300","datastart":"900","incomanimation":"lfl","outgoanimation":"randomrotateout","easing":"easeOutQuart","endeasing":"easeOutBack","text":{"1":"Design is optimized for all<br\/> the most popular screen resolutions.<br\/> When screen is resized,<br\/> design elements are transformed <br\/>smoothly on each breakpoin.","2":"Design is optimized for all<br\/> the most popular screen resolutions.<br\/> When screen is resized,<br\/> design elements are transformed <br\/>smoothly on each breakpoin."}}').'\'),
		(4, 1, 3, \'{"type_caption":"imagecaption","datax":"415","datay":"230","class":"","dataspeed":"300","datastart":"1100","incomanimation":"sfl","outgoanimation":"ltb","easing":"easeInCirc","endeasing":"easeInCirc","image_1":"13_1.png","image_2":"13_1.png"}\'),
		(5, 2, 4, \'{"type_caption":"imagecaption","datax":"415","datay":"2","class":"","dataspeed":"300","datastart":"500","incomanimation":"randomrotate","outgoanimation":"str","easing":"easeOutBack","endeasing":"easeInBack","image_1":"14_1.png","image_2":"14_1.png"}\'),
		(6, 2, 5, \'{"type_caption":"textcaption","datax":"415","datay":"80","class":"big_white","dataspeed":"300","datastart":"700","incomanimation":"randomrotate","outgoanimation":"ltr","easing":"easeInSine","endeasing":"easeOutSine","text":{"1":"Mega Menu","2":"Mega Menu"}}\'),
		(7, 2, 6, \''.pSQL('{"type_caption":"textcaption","datax":"415","datay":"130","class":"","dataspeed":"300","datastart":"900","incomanimation":"randomrotate","outgoanimation":"ltr","easing":"easeInQuart","endeasing":"easeInElastic","text":{"1":"Configure many types of flexible<br\/> menu by combining options together<br\/>\r\nConfigure width and columns <br\/>of dropdown menu\r\nConfigure <br\/>columns for each option\r\n","2":"Configure many types of flexible<br\/> menu by combining options together<br\/>\r\nConfigure width and columns <br\/>of dropdown menu\r\nConfigure <br\/>columns for each option\r\n"}}').'\'),
		(8, 2, 7, \'{"type_caption":"imagecaption","datax":"415","datay":"220","class":"","dataspeed":"300","datastart":"1100","incomanimation":"sfr","outgoanimation":"ltt","easing":"easeInQuart","endeasing":"easeInOutElastic","image_1":"17_1.png","image_2":"17_1.png"}\'),
		(9, 3, 8, \'{"type_caption":"imagecaption","datax":"415","datay":"2","class":"","dataspeed":"300","datastart":"500","incomanimation":"sft","outgoanimation":"stt","easing":"easeOutBack","endeasing":"easeOutBack","image_1":"18_1.png","image_2":"18_1.png"}\'),
		(10, 3, 9, \''.pSQL('{"type_caption":"textcaption","datax":"415","datay":"80","class":"big_white","dataspeed":"300","datastart":"700","incomanimation":"sfl","outgoanimation":"stt","easing":"easeInCubic","endeasing":"easeOutBack","text":{"1":"Slideshow<br\/>Revolution","2":"Slideshow<br\/>Revolution"}}').'\'),
		(11, 3, 10, \''.pSQL('{"type_caption":"textcaption","datax":"415","datay":"158","class":"","dataspeed":"300","datastart":"900","incomanimation":"lft","outgoanimation":"fadeout","easing":"easeOutSine","endeasing":"easeInSine","text":{"1":"On top of that, <br\/>Slider Revolution is fully responsive <br\/>and mobile optimized <br\/>and can take on any dimensions.","2":"On top of that, <br\/>Slider Revolution is fully responsive <br\/>and mobile optimized <br\/>and can take on any dimensions."}}').'\'),
		(12, 3, 11, \'{"type_caption":"imagecaption","datax":"415","datay":"230","class":"","dataspeed":"300","datastart":"1100","incomanimation":"sft","outgoanimation":"stt","easing":"easeOutBack","endeasing":"easeOutBack","image_1":"21_1.png","image_2":"21_1.png"}\')')
		)
			return false;
		return true;
	}
	
	
	public function install()
	{
	 	if (parent::install() == false OR !$this->registerHook('header') OR !$this->registerHook('csslideshow'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csslider (`id_slider` int(10) unsigned NOT NULL AUTO_INCREMENT, `url` varchar(255),`animation` text ,`image` varchar(255) NOT NULL DEFAULT \'\', `position` int(10) unsigned DEFAULT \'0\', `display` tinyint(1) NOT NULL DEFAULT \'1\', PRIMARY KEY (`id_slider`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csslider_shop (`id_slider` int(10) unsigned NOT NULL ,`id_shop` int(10) unsigned NOT NULL, `url` varchar(255),`animation` text, `image` varchar(255) NOT NULL DEFAULT \'\', `position` int(10) unsigned DEFAULT \'0\', `display` tinyint(1) NOT NULL DEFAULT \'1\', PRIMARY KEY (`id_slider`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csslider_caption (`id_caption` int(10) unsigned NOT NULL AUTO_INCREMENT, `id_slider` int(10) unsigned NOT NULL,`layer` int(10) unsigned NOT NULL, `content` text, PRIMARY KEY (`id_caption`)) ENGINE=InnoDB default CHARSET=utf8'))
			return false;
		$this->init_data();
	 	return true;
	}
	
	public function uninstall()
	{
	 	if (parent::uninstall() == false OR !$this->unregisterHook('csslideshow'))
	 		return false;
		if (!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csslider') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csslider_shop') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csslider_caption'))
	 		return false;
		$this->_clearCache('csslider.tpl');
	 	return true;
	}
	
	private function _displayHelp()
	{
		$this->_html .= '
		<br/>
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Revolution Slider Helper').'</legend>
			<a href="http://www.themepunch.com/codecanyon/revolution_wp/index.html" alt="Revolution Slider" title="Revolution Slider">http://www.themepunch.com/codecanyon/revolution_wp/index.html</a>
		</fieldset>';
	}
	
	private function _displayOptions()
	{
		$this->context->controller->addCss($this->_path.'css/admin/tab.css', 'all');
		$this->context->controller->addJS($this->_path.'js/admin/tab.js');
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		if (!file_exists(dirname(__FILE__).'/'.'option_'.$id_shop.'.xml'))
		{
			copy(dirname(__FILE__).'/'.'option_'.Configuration::get('PS_SHOP_DEFAULT').'.xml',dirname(__FILE__).'/'.'option_'.$id_shop.'.xml');
		}
		$option = simplexml_load_file(dirname(__FILE__).'/'.'option_'.$id_shop.'.xml');
		$this->_html .= '
		<br/>
		
		<h2><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Setting Options').'</h2>
		<div class="productTabs" style="margin-right: -1px;width: 170px;">
			<ul class="tab">
				<li class="tab-row">
				<a class="nav-manager selected" id="general-option" href="javascript:void(0);">'.$this->l('General').'</a></li>
				<li class="tab-row">
				<a class="nav-manager" id="navigation-option" href="javascript:void(0);">'.$this->l('Categorías').'</a></li>
				<li class="tab-row">
				<a class="nav-manager" id="thumbnail-option" href="javascript:void(0);">'.$this->l('Thumbnail').'</a></li>
				<li class="tab-row">
				<a class="nav-manager" id="mobilevisibility-option" href="javascript:void(0);">'.$this->l('Mobile visibility').'</a></li>
			</ul>
		</div>
		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
			<fieldset class="general-option tab-manager plblogtabs">
				<label>'.$this->l('Delay:').'</label>
			<div class="margin-form">
				<input type="text" name="delay" value="'.($option->delay ? $option->delay : '9000').'"/>
				<p class="clear">'.$this->l('The time one slide stays on the screen in Milliseconds').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Startheight:').'</label>
			<div class="margin-form">
				<input type="text" name="startheight" value="'.($option->startheight ? $option->startheight : '500').'"/>
				<p class="clear">'.$this->l('Basic Height of the Slider in the desktop resolution in pixel, other screen sizes will be calculated from this').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Startwidth:').'</label>
			<div class="margin-form">
				<input type="text" name="startwidth" value="'.($option->startwidth ? $option->startwidth : '1180').'"/>
				<p class="clear">'.$this->l('Basic Width of the Slider in the desktop resolution in pixel, other screen sizes will be calculated from this').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Touchenabled:').'</label>
			<div class="margin-form">
				<input type="radio" name="touchenabled" value="on" '.($option->touchenabled == "on" ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input type="radio" name="touchenabled" value="off" '.($option->touchenabled == "off" ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
				<p class="clear">'.$this->l('Enable Swipe Function on touch devices').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('OnHoverStop:').'</label>
			<div class="margin-form">
				<input type="radio" name="onhoverstop" value="on" '.($option->onhoverstop == "on" ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input type="radio" name="onhoverstop" value="off" '.($option->onhoverstop == "off" ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
				<p class="clear">'.$this->l('Stop the Timer when hovering the slider').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Show timer line:').'</label>
			<div class="margin-form">
				<input type="radio" name="timerline" value="true" '.($option->timerline != "false" ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input type="radio" name="timerline" value="false" '.($option->timerline == "false" ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
				<p class="clear">'.$this->l('Display or not dislay timer liner.').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Timer line position:').'</label>
			<div class="margin-form">
				<input type="radio" name="timerlineposition" value="top" '.($option->timerlineposition == "top" ? 'checked="checked"' : '').' />
				<label class="t">'.$this->l('Top').'</label>
				<input type="radio" name="timerlineposition" value="bottom" '.($option->timerlineposition == "bottom" ? 'checked="checked"' : '').' />
				<label class="t">'.$this->l('Bottom').'</label>
				<p class="clear">'.$this->l('Position timer liner.').'</p>
				<div class="clear"></div>
			</div>
			<label>'.$this->l('Shadow:').'</label>
			<div class="margin-form">
				<select name="shadow">';
				for($i=0;$i<=3;$i++)
					$this->_html.='<option value="'.$i.'" '.($option->shadow == $i ? "selected=selected" : "").'>'.$i.'</option>';
				$this->_html.='</select>
				<p class="clear">'.$this->l('Basic Width of the Slider in the desktop resolution in pixel, other screen sizes will be calculated from this').'</p>
				<div class="clear"></div>
			</div>
			<label>'.$this->l('Stop at slide :').'</label>
			<div class="margin-form">
				<input type="text" name="stopatslide" value="'.($option->stopatslide ? $option->stopatslide : '-1').'"/>
				<p class="clear">'.$this->l('-1 or 1 to 999. Stop at selected Slide Number. If set to -1 it will loop without stopping. Only available if stopAfterLoops is not equal -1 !').'</p>
				<div class="clear"></div>
			</div>
			<label>'.$this->l('Stop after loops :').'</label>
			<div class="margin-form">
				<input type="text" name="stopafterloops" value="'.($option->stopafterloops ? $option->stopafterloops : '-1').'"/>
				<p class="clear">'.$this->l('-1 or 0 to 999. Stop at selected Slide Number (stopAtSlide) after slide looped "x" time, where x this Number. If set to -1 it will loop without stopping. Only available if stopAtSlide not equal -1 !').'</p>
				<div class="clear"></div>
			</div>
			</fieldset>
			<fieldset class="navigation-option tab-manager plblogtabs">
				<label>'.$this->l('Navigation Type:').'</label>
				<div class="margin-form">
					<input type="radio" name="navigationtype" value="bullet" '.($option->navigationtype == "bullet" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('Bullet').'</label>
					<input type="radio" name="navigationtype" value="thumb" '.($option->navigationtype == "thumb" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('Thumb').'</label>
					<input type="radio" name="navigationtype" value="none" '.($option->navigationtype == "none" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('None').'</label>
					<p class="clear">'.$this->l(' Display type of the navigation bar').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Navigation Arrows:').'</label>
				<div class="margin-form">
					<input type="radio" name="navigationarrow" value="nexttobullets" '.($option->navigationarrow == "nexttobullets" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('Next to Bullets').'</label>
					<input type="radio" name="navigationarrow" value="solo" '.($option->navigationarrow == "solo" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('Solo').'</label>
					<input type="radio" name="navigationarrow" value="none" '.($option->navigationarrow == "none" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('None').'</label>
					<p class="clear">'.$this->l('Display position of the Navigation Arrows').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Navigation style:').'</label>
				<div class="margin-form">
					<input type="radio" name="navigationstyle" value="round" '.($option->navigationstyle == "round" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('round').'</label>
					<input type="radio" name="navigationstyle" value="navbar" '.($option->navigationstyle == "navbar" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('navbar').'</label>
					<input type="radio" name="navigationstyle" value="round-old" '.($option->navigationstyle == "round-old" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('round-old').'</label>
					<input type="radio" name="navigationstyle" value="square-old" '.($option->navigationstyle == "square-old" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('square-old').'</label>
					<input type="radio" name="navigationstyle" value="navbar-old" '.($option->navigationstyle == "navbar-old" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('navbar-old').'</label>
					<p class="clear">'.$this->l('Look of the navigation bullets').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Navigation Horizontal Align :').'</label>
				<div class="margin-form">
					<input type="radio" name="navigationhalign" value="left" '.($option->navigationhalign == "left" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('left').'</label>
					<input type="radio" name="navigationhalign" value="center" '.($option->navigationhalign == "center" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('center').'</label>
					<input type="radio" name="navigationhalign" value="right" '.($option->navigationhalign == "right" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('right').'</label>
					<p class="clear">'.$this->l('Horizontal Align of Bullets / Thumbnails').'</p>
					<div class="clear"></div>
				</div>
				
				
				<label>'.$this->l('Navigation Vertical Align :').'</label>
				<div class="margin-form">
					<input type="radio" name="navigationvalign" value="top" '.($option->navigationvalign == "top" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('top').'</label>
					<input type="radio" name="navigationvalign" value="center" '.($option->navigationvalign == "center" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('center').'</label>
					<input type="radio" name="navigationvalign" value="bottom" '.($option->navigationvalign == "bottom" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('bottom').'</label>
					<p class="clear">'.$this->l('Vertical Align of Bullets / Thumbnails').'</p>
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Navigation Horizontal Offset :').'</label>
				<div class="margin-form">
					<input type="text" name="navigationhoffset" value="'.($option->navigationhoffset ? $option->navigationhoffset : '0').'"/>
					<p class="clear">'.$this->l('A value between -600 to 600 - Offset from current Horizontal position of Bullets / Thumbnails negative and positive direction').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Navigation Vertical Offset :').'</label>
				<div class="margin-form">
					<input type="text" name="navigationvoffset" value="'.($option->navigationvoffset ? $option->navigationvoffset : '0').'"/>
					<p class="clear">'.$this->l('A value between -600 to 600 - Offset from current Vertical position of Bullets / Thumbnails negative and positive direction').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Solo Arrow Left Horizontal Align:').'</label>
				<div class="margin-form">
					<input type="radio" name="soloarrowlefthalign" value="left" '.($option->soloarrowlefthalign == "left" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('left').'</label>
					<input type="radio" name="soloarrowlefthalign" value="center" '.($option->soloarrowlefthalign == "center" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('center').'</label>
					<input type="radio" name="soloarrowlefthalign" value="right" '.($option->soloarrowlefthalign == "right" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('right').'</label>
					<p class="clear">'.$this->l('Horizontal Align of left Arrow (only if arrow is not next to bullets)').'</p>
					<div class="clear"></div>
				</div>
				
				
				<label>'.$this->l('Solo Arrow Left Vertical Align :').'</label>
				<div class="margin-form">
					<input type="radio" name="soloarrowleftvalign" value="top" '.($option->soloarrowleftvalign == "top" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('top').'</label>
					<input type="radio" name="soloarrowleftvalign" value="center" '.($option->soloarrowleftvalign == "center" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('center').'</label>
					<input type="radio" name="soloarrowleftvalign" value="bottom" '.($option->soloarrowleftvalign == "bottom" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('bottom').'</label>
					<p class="clear">'.$this->l('Vertical Align of left Arrow (only if arrow is not next to bullets)').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Solo Arrow Left Horizontal Offset :').'</label>
				<div class="margin-form">
					<input type="text" name="soloarrowlefthoffset" value="'.($option->soloarrowlefthoffset ? $option->soloarrowlefthoffset : '20').'"/>
					<p class="clear">'.$this->l('a value between -600 to 600 -	Offset from current Horizontal position of of left Arrow negative and positive direction').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Solo Arrow Left Vertical Offset :').'</label>
				<div class="margin-form">
					<input type="text" name="soloarrowleftvoffset" value="'.($option->soloarrowleftvoffset ? $option->soloarrowleftvoffset : '0').'"/>
					<p class="clear">'.$this->l('a value between -600 to 600 -	Offset from current Vertical position of of left Arrow negative and positive direction').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Solo Arrow Right Horizontal Align:').'</label>
				<div class="margin-form">
					<input type="radio" name="soloarrowrighthalign" value="left" '.($option->soloarrowrighthalign == "left" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('left').'</label>
					<input type="radio" name="soloarrowrighthalign" value="center" '.($option->soloarrowrighthalign == "center" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('center').'</label>
					<input type="radio" name="soloarrowrighthalign" value="right" '.($option->soloarrowrighthalign == "right" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('right').'</label>
					<p class="clear">'.$this->l('Horizontal Align of right Arrow (only if arrow is not next to bullets)').'</p>
					<div class="clear"></div>
				</div>
				
				
				<label>'.$this->l('Solo Arrow Right Vertical Align :').'</label>
				<div class="margin-form">
					<input type="radio" name="soloarrowrightvalign" value="top" '.($option->soloarrowrightvalign == "top" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('top').'</label>
					<input type="radio" name="soloarrowrightvalign" value="center" '.($option->soloarrowrightvalign == "center" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('center').'</label>
					<input type="radio" name="soloarrowrightvalign" value="bottom" '.($option->soloarrowrightvalign == "bottom" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('bottom').'</label>
					<p class="clear">'.$this->l('Vertical Align of left Arrow (only if arrow is not next to bullets)').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Solo Arrow Right Horizontal Offset :').'</label>
				<div class="margin-form">
					<input type="text" name="soloarrowrighthoffset" value="'.($option->soloarrowrighthoffset ? $option->soloarrowrighthoffset : '20').'"/>
					<p class="clear">'.$this->l('a value between -600 to 600 -	Offset from current Horizontal position of of right Arrow negative and positive direction').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Solo Arrow Right Vertical Offset :').'</label>
				<div class="margin-form">
					<input type="text" name="soloarrowrightvoffset" value="'.($option->soloarrowrightvoffset ? $option->soloarrowrightvoffset : '0').'"/>
					<p class="clear">'.$this->l('a value between -600 to 600 -	Offset from current Vertical position of of right Arrow negative and positive direction').'</p>
					<div class="clear"></div>
				</div>
			</fieldset>
		<fieldset class="thumbnail-option tab-manager plblogtabs">
			<label>'.$this->l('Time hide thumbnails:').'</label>
				<div class="margin-form">
					<input type="text" name="timehidethumbnail" value="'.($option->timehidethumbnail ? $option->timehidethumbnail : '10').'"/>
					<p class="clear">'.$this->l('Time after that the Thumbs will be hidden').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Thumbnails width:').'</label>
				<div class="margin-form">
					<input type="text" name="thumbnailwidth" value="'.($option->thumbnailwidth ? $option->thumbnailwidth : '100').'"/>
					<p class="clear">'.$this->l('The basic Width of one Thumbnail').'</p>
				<div class="clear"></div>
			</div>
			<label>'.$this->l('Thumbnails height:').'</label>
				<div class="margin-form">
					<input type="text" name="thumbnailheight" value="'.($option->thumbnailheight ? $option->thumbnailheight : '100').'"/>
					<p class="clear">'.$this->l('The basic Height of one Thumbnail').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Thumbnails amount:').'</label>
				<div class="margin-form">
					<input type="text" name="thumbamount" value="'.($option->thumbamount ? $option->thumbamount : '2').'"/>
					<p class="clear">'.$this->l('The amount of the Thumbs visible same time (only if thumb is selected)').'</p>
				<div class="clear"></div>
			</div>
			
		</fieldset>
		<fieldset class="mobilevisibility-option tab-manager plblogtabs">
			<label>'.$this->l('Hide Caption At Limit:').'</label>
				<div class="margin-form">
					<input type="text" name="hidecapptionatlimit" value="'.($option->hidecapptionatlimit ? $option->hidecapptionatlimit : '0').'"/>
					<p class="clear">'.$this->l('It Defines if a caption should be shown under a Width Limit ( Basod on The Width of Banner ! )').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Hide All Caption At Limit:').'</label>
				<div class="margin-form">
					<input type="text" name="hideallcapptionatlimit" value="'.($option->hideallcapptionatlimit ? $option->hideallcapptionatlimit : '0').'"/>
					<p class="clear">'.$this->l('Hide all The Captions if Width of Browser is less then this value').'</p>
				<div class="clear"></div>
			</div>
			
			<label>'.$this->l('Hide Slider At Limit:').'</label>
				<div class="margin-form">
					<input type="text" name="hideslideratlimit" value="'.($option->hideslideratlimit ? $option->hideslideratlimit : '0').'"/>
					<p class="clear">'.$this->l('Under this Limit the Slider is hidden and the timer is stopped').'</p>
				<div class="clear"></div>
			</div>
			</fieldset> <br/>
			<center>';
				$this->_html .= '
				<input type="submit" class="button" name="applyOptions" value="'.$this->l('Apply').'" id="applyOptions" />
				<input type="submit" class="button" name="resetOptions" value="'.$this->l('Reset').'" id="applyOptions" />';
				$this->_html .= '					
			</center>
		</form>
		';
	}
	
	public function getContent()
   	{
		global $currentIndex;
		$this->_html .= '<div class="toolbar-placeholder">
			<div class="toolbarBox toolbarHead" style="width: 1319px;">
				<ul style="float:right;">
					<li id="btnAddMenu">
						<a class="toolbar_btn" href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addSlider"><span class="process-icon-new "></span>
						<div>'.$this->l('Add new slide').'</div></a>
					</li>
				</ul>
				<div class="pageTitle">
					<h3><span id="current_obj" style="font-weight: normal;"><span class="breadcrumb item-0">'.$this->displayName.'</span></span></h3>
				</div>
			</div>
			
		</div>';
		$this->_postProcess();
		
		if (Tools::isSubmit('addSlider'))
			$this->_displayAddForm();
		elseif (Tools::isSubmit('editSlider'))
			$this->_displayUpdateForm();
		else
		{
			$this->_displayForm();
			$this->_displayOptions();
		}
		
		$this->_displayHelp();
		
		return $this->_html;
	}
	
	private function saveXmlOption($reset = false)
	{
		$error = false;
		$newXml = '<?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n".'<options>'."\n";
		foreach($this->optionList as $key=>$default)
		{
			$newXml .= '<'.$key.'>';
			$newXml .= ($reset ? ''.$default.'' : Tools::getValue(''.$key.''));
			$newXml .= '</'.$key.'>'."\n";
		}
		$newXml .= '</options>'."\n";
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		if ($fd = @fopen(dirname(__FILE__).'/'.'option_'.$id_shop.'.xml', 'w'))
		{
			if (!@fwrite($fd, $newXml))
				$error = $this->displayError($this->l('Unable to write to the editor file.'));
			if (!@fclose($fd))
				$error = $this->displayError($this->l('Can\'t close the editor file.'));
		}
		else
			$error = $this->displayError($this->l('Unable to update the editor file. Please check the editor file\'s writing permissions.'));
		return $error;
	}
	
	private function _postProcess()
	{
		global $currentIndex;
		$errors = array();
		if(Tools::isSubmit('submitAddCaption'))
		{
			$caption = new Caption(Tools::getValue('id_caption'));
			$caption->copyFromPost();
			$errors = $caption->validateController();
			if (sizeof($errors))
			{
				$this->_html .= $this->displayError(implode('<br />', $errors));
			}
			else
			{
				Tools::getValue('id_caption') ? $caption->update() : $caption->add();
				$this->_clearCache('csslider.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editSlider&id_slider='.$caption->id_slider.'#tab=caption');
			}
		}
		if (Tools::isSubmit('submitAddSliderAndContinue') OR Tools::isSubmit('submitAddSliderAndBack') OR Tools::isSubmit('submitAddSlider'))
		{
			$slider = new SliderClass(Tools::getValue('id_slider'));
			$slider->copyFromPost();
			$errors = $slider->validateController();	
			if (sizeof($errors))
			{
				$this->_html .= $this->displayError(implode('<br />', $errors));
			}
			else
			{
				Tools::getValue('id_slider') ? $slider->update() : $slider->add();
				$this->_clearCache('csslider.tpl');
				if(Tools::getValue('id_slider') OR Tools::isSubmit('submitAddSliderAndBack'))
					Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveSliderConfirmation');
				else
					Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editSlider&id_slider='.$slider->id.'#tab=caption');
			}
		}
		elseif (Tools::isSubmit('deleteSlider') AND Tools::getValue('id_slider'))
		{
			$slider = new SliderClass(Tools::getValue('id_slider'));
			$captions = $slider->getCaption();
			foreach ($captions as $caption)
			{
				$c = new Caption($caption['id_caption']);
				$c->delete();
				$c->cleanLayers();
			}
			$slider->delete();
			$slider->cleanPositions();
			$this->_clearCache('csslider.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteSliderConfirmation');
		}
		elseif (Tools::isSubmit('orderSlider') AND Validate::isInt(Tools::getValue('id_slider')) AND Validate::isInt(Tools::getValue('position')))
		{
			$slider = new SliderClass(Tools::getValue('id_slider'));
			$slider->updatePosition(Tools::getValue('way'),Tools::getValue('position'));
			$this->_clearCache('csslider.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('deleteCaption') AND Tools::getValue('id_caption'))
		{
			$caption = new Caption(Tools::getValue('id_caption'));
			$caption->delete();
			$caption->cleanLayers();
			$this->_clearCache('csslider.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editSlider&id_slider='.$caption->id_slider.'#tab=caption');
		}
		elseif (Tools::isSubmit('orderLayerCaption') AND Validate::isInt(Tools::getValue('id_caption')) AND Validate::isInt(Tools::getValue('layer')))
		{
			$caption = new Caption(Tools::getValue('id_caption'));
			$caption->updateLayer(Tools::getValue('way'),Tools::getValue('layer'));
			$this->_clearCache('csslider.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editSlider&id_slider='.$caption->id_slider.'#tab=caption');
		}
		elseif (Tools::isSubmit('applyOptions'))
		{
			if ($error = $this->saveXmlOption())
				$this->_html .= $error;
			else
			{
				$this->_clearCache('csslider.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&statusConfirmation');
			}
		}
		elseif (Tools::isSubmit('changeStatusSlider') AND Tools::getValue('id_slider'))
		{
			$slidernew = new SliderClass(Tools::getValue('id_slider'));
			$slidernew->updateStatus(Tools::getValue('status'));
			$this->_clearCache('csslider.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('resetOptions'))
		{
			if ($error = $this->saveXmlOption(true))
				$this->_html .= $error;
			else
			{
				$this->_clearCache('csslider.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&statusConfirmation');
			}
		}
		elseif (Tools::isSubmit('saveSliderConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Slider has been added successfully'));
		elseif (Tools::isSubmit('deleteSliderConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Slider deleted successfully'));
		elseif (Tools::isSubmit('statusConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Options updated successfully'));
		elseif (Tools::isSubmit('saveCaptionConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Caption has been added successfully'));
		elseif (Tools::isSubmit('cancelAddSlider'))
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
	}
	
	
	private function getSliders($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
	 	if (!$result = Db::getInstance()->ExecuteS(
			'SELECT ss.* FROM `'._DB_PREFIX_.'csslider_shop` ss
			LEFT JOIN `'._DB_PREFIX_.'csslider` s ON (ss.id_slider = s.id_slider)
			WHERE (ss.id_shop = '.(int)$id_shop.')'.
			($active ? ' AND ss.`display` = 1' : ' ').'
			ORDER BY ss.`position` ASC'))
	 		return false;
	 	return $result;
	}
	
	private function _displayForm()
	{
		global $currentIndex, $cookie;
		$this->context->controller->addJQueryPlugin('fancybox');
		$languages = Language::getLanguages(false);
		$divLangName = 'captiondiv¤imagecaptiondiv¤video_typediv¤video_iddiv';
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	 	$this->_html .= '
		<script type="text/javascript">
		function appE(id,layer)
		{
			$("input[name=\'hiddenId\']").remove();
			$("body").append("<input type=\'hidden\' value=\"" + id + "\" name=\'hiddenId\' />");
			$("body").append("<input type=\'hidden\' value=\"" + layer + "\" name=\'hiddenLayer\' />");
		}
		$(document).ready(function() {
				$("a.addCaption").fancybox({
					\'onStart\': function() {
					$("input[name=\'id_slider\']").remove();
					var idSlider = $("input[name=\'hiddenId\']").attr(\'value\');
					var hiddenLayer = $("input[name=\'hiddenLayer\']").attr(\'value\');
						$("form.formAddCaption").append("<input type=\'hidden\' name=\'id_slider\' value=\"" + idSlider + "\" />");
						$("form.formAddCaption").append("<input type=\'hidden\' name=\'layer\' value=\"" + hiddenLayer + "\" />");
					}
				});
			});
		</script>
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Sliders').'</legend>
			<table width="100%" id="csmegamenu" class="table tableDnD feature table_grid" cellspacing="0" cellpadding="0">
			<thead>
			<tr class="nodrag nodrop">
				<th>&nbsp;</th>
				<th class="center">'.$this->l('Image').'</th>
				<th class="center">'.$this->l('Url').'</th>
				<th class="center">'.$this->l('Displayed').'</th>
				<th class="center">'.$this->l('Position').'</th>
				<th class="right">'.$this->l('Add caption').'</th>
				<th class="right">'.$this->l('Delete').'</th>
			</tr>
			</thead>
			<tbody>';
		$sliders = $this->getSliders(false);
		$stringConfirm='onclick="if (!confirm(\' Are you sure that you want to delete item ?\')) return false "';
		if (is_array($sliders))
		{
			static $irow;
			$stt = 1;
			foreach ($sliders as $slider)
			{
				
				$bg_slider = json_decode($slider['image']);
				if ($dot_pos = strrpos($slider['image'],'.'))
					$thumb_url = _PS_BASE_URL_._MODULE_DIR_.$this->name.'/images/thumbs/'.substr($slider['image'], 0, $dot_pos).substr($slider['image'], $dot_pos);
				else
					$thumb_url =_PS_BASE_URL_._MODULE_DIR_.$this->name.'/image/default_thumb.jpg';
				$this->_html .= '
				<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
					<td class="pointer" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editSlider&id_slider='.$slider['id_slider'].'\'">'.$stt.'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editSlider&id_slider='.$slider['id_slider'].'\'">';
					if(isset($bg_slider->type) && $bg_slider->type == "color")
						$this->_html .= '<div style="display:inline-block;width:45px;height:45px;background-color:'.$bg_slider->value.'"></div>';
					if(isset($bg_slider->type) && $bg_slider->type == "image")
					{	
						$thumb_url = _PS_BASE_URL_._MODULE_DIR_.$this->name.'/images/thumbs/'.$bg_slider->value;
						$this->_html .= '<img src="'.$thumb_url.'" alt="" title="" style="height:45px;width:45px;margin: 3px 0px 3px 0px;"/>';
					}
					if(isset($bg_slider->type) && $bg_slider->type == "transparent")
					{
						$thumb_url = _PS_BASE_URL_._MODULE_DIR_.$this->name.'/images/thumbs/transparent.jpg';
						$this->_html .= '<img src="'.$thumb_url.'" alt="" title="" style="height:45px;width:45px;margin: 3px 0px 3px 0px;"/>';
					}
					$this->_html .= '</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editSlider&id_slider='.$slider['id_slider'].'\'">'.$slider['url'].'</td>
					
					<td class="pointer center"> <a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&changeStatusSlider&id_slider='.$slider['id_slider'].'&status='.$slider['display'].'">'.($slider['display'] ? '<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" />' : '<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" />').'</a> </td>
					
					<td class="pointer center">'.($slider !== end($sliders) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderSlider&id_slider='.$slider['id_slider'].'&way=1&position='.($slider['position']+1).'"><img src="'._PS_ADMIN_IMG_.'down.gif" alt="'.$this->l('Down').'" /></a>' : '').($slider !== reset($sliders) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderSlider&id_slider='.$slider['id_slider'].'&way=0&position='.($slider['position']-1).'"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="'.$this->l('Up').'" /></a>' : '').'</td>
					<td class="pointer center"><a class="addCaption" id="'.$slider['id_slider'].'" href="#contentAddCaption" onclick="return appE('.$slider['id_slider'].','.Caption::getNextLayer().')" > <img class="add_caption"  src="../img/admin/add.gif" alt="'.$this->l('Add caption').'" title="'.$this->l('Add caption').'" /></a></td>
					<td class="pointer center">
					<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteSlider&id_slider='.$slider['id_slider'].'\'" '.$stringConfirm.'><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="Delete" title="Delete" /></a>
					</td>
				</tr>';
				$stt++;
			}
		}
		$this->_html .= '
			</tbody>
			</table>
		</fieldset>
		<div style="display:none"><div id="contentAddCaption"><form class="formAddCaption" method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'"  enctype="multipart/form-data">
		';
		$this->_html .= '<div style="margin-left:150px">
		<input type="radio" name="addCaption" onclick="$(\'.csCaption\').hide();$(\'.textcaption\').show();"  value="textcaption" checked="checked"/><label style="float:none">'.$this->l('Text Caption').'</label>
		<input type="radio" name="addCaption" onclick="$(\'.csCaption\').hide();$(\'.imagecaption\').show();" value="imagecaption"/><label style="float:none">'.$this->l('Image Caption').'</label>
		<input type="radio" name="addCaption" onclick="$(\'.csCaption\').hide();$(\'.videocaption\').show();" value="videocaption"/><label style="float:none">'.$this->l('Video Caption').'</label>
		</div><br/>
		<div class="textcaption csCaption">
		<label style="width:150px">'.$this->l('Text:').'</label>
		';
			foreach ($languages as $language)
			{
				$this->_html .= '
			<div id="captiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
				<textarea class="rte" name="textcaption['.$language['id_lang'].']" id="caption_'.$language['id_lang'].'" cols="50" rows="5">'.Tools::getValue('caption_'.$language['id_lang']).'</textarea>
			</div>';
			}
			$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'captiondiv', true);	
			$this->_html .= '</div>
		<div class="imagecaption csCaption" style="display:none">
		<label style="width:150px">'.$this->l('Image:').'</label>
		';
			foreach ($languages as $language)
			{
				$this->_html .= '
			<div id="imagecaptiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
				<input type="file" name="imagecaption_'.$language['id_lang'].'"/>
			</div>';
			}
			$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'imagecaptiondiv', true);
		$this->_html .= '</div>
		<div class="videocaption csCaption" style="display:none">
		<label style="width:150px">'.$this->l('Video Type:').'</label>';
		foreach ($languages as $language)
		{
			$this->_html .= '
		<div id="video_typediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
			<select name="videocaption_type['.$language['id_lang'].']">';
			$this->_html .= '<option value="1" selected="selected">Youtube</option>';
			$this->_html .= '<option value="2">Vimeo</option>';
		$this->_html .= '
			</select>
		</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'video_typediv', true);	
		$this->_html .= '
		<div class="clear"></div>
	<label style="width:150px">'.$this->l('Video ID:').'</label>';
		foreach ($languages as $language)
		{
			$this->_html .= '
		<div id="video_iddiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">';
		
			$this->_html .= '<input type="text" name="videocaption_text['.$language['id_lang'].']" value="'.Tools::getValue('video_id_'.$language['id_lang']).'" size="30" />';
			$this->_html .= '
		</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'video_iddiv', true);	
		$this->_html .= '
		</div>
		<div class="clear"></div>'.$this->stringCaption().'
		</form></div></div>';
			
		
	}
	
	private function stringCaption($content=null)
	{
		$string_caption = '<div><p><label style="width:150px">'.$this->l('Data x:').'</label>
				<input type="text" name="content[datax]" value="'.($content ? $content->datax : "100").'"/></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('The horizontal position in the standard (via startwidth option defined) screen size (other screen sizes will be calculated)').'</p></div>
				
				<div><p><label style="width:150px">'.$this->l('Data y:').'</label>
				<input type="text" name="content[datay]"  value="'.($content ? $content->datay : "100").'"/></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('The vertical position in the standard (via startheight option defined) screen size (other screen sizes will be calculated)').'</p></div>
				
				<p><label style="width:150px">'.$this->l('Class css:').'</label>
				<select name="content[class]">
				<option value="">'.$this->l('No choose').'</option>
				';
				foreach($this->classList as $class)
				{
					$string_caption .= '<option value="'.$class.'" '.($content && $content->class == ''.$class.'' ? "selected=selected" : "").'>'.$class.'</option>';
				}
				$string_caption .= '</select></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('Example big_white, big_orange, medium_grey').'</p>
				
				<div><p><label style="width:150px">'.$this->l('Data speed:').'</label>
				<input type="text" name="content[dataspeed]"  value="'.($content ? $content->dataspeed : "100").'"/></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('Duration of the animation in milliseconds').'</p></div>
				
				<div><p><label style="width:150px">'.$this->l('Data start:').'</label>
				<input type="text" name="content[datastart]"  value="'.($content ? $content->datastart : "100").'"/></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('How many milliseconds should this caption start to show').'</p></div>
				
				<p><label style="width:150px">'.$this->l('Incom animation:').'</label>
				<select name="content[incomanimation]">';
				foreach($this->incomAnimation as $key=>$animation)
				{
					$string_caption .= '<option value="'.$key.'" '.($content && $content->incomanimation == ''.$key.'' ? "selected=selected" : "").'>'.$animation.'</option>';
				}
				$string_caption .= '</select></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('Animation incoming caption').'</p>
				
				<p><label style="width:150px">'.$this->l('Outgo animation:').'</label>
				<select name="content[outgoanimation]">';
				foreach($this->outgoAnimation as $key=>$animation)
				{
					$string_caption .= '<option value="'.$key.'" '.($content && $content->outgoanimation == ''.$key.'' ? "selected=selected" : "").'>'.$animation.'</option>';
				}
				$string_caption .= '</select></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('Animation outgoing caption').'</p>
				
				<p><label style="width:150px">'.$this->l('Easing:').'</label>
				<select name="content[easing]">';
				foreach($this->easing as $e)
				{
					$string_caption .= '<option value="'.$e.'" '.($content && $content->easing == ''.$e.'' ? "selected=selected" : "").'>'.$e.'</option>';
				}
				$string_caption .= '</select></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('Special Easing effect of the animation').'</p>
				
				<p><label style="width:150px">'.$this->l('Endeasing:').'</label>
				<select name="content[endeasing]">';
				foreach($this->easing as $e)
				{
					$string_caption .= '<option value="'.$e.'" '.($content && $content->endeasing == ''.$e.'' ? "selected=selected" : "").'>'.$e.'</option>';
				}
				$string_caption .= '</select></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('Special easing effect of the animation').'</p>
				
				<p><label style="width:150px">'.$this->l('Hide under width:').'</label><input type="checkbox" name="content[hideunderwidth]" '.($content && isset($content->hideunderwidth) ? "checked=checked" : "").'/></p>
				<p class="preference_description clear" style="margin-left:150px;width:auto">'.$this->l('Hide Caption at mobile size').'</p>
				<div style="margin-left:150px">
				<input class="button" type="submit" '.($content ? '' : 'onclick="$.fancybox.close();"').' name="submitAddCaption" value="'.($content ? $this->l('Save Caption') : $this->l('Add Caption')).'"/>
				</div>';
		return $string_caption;		
	}
	private function _displayAddForm()
	{
		$this->context->controller->addCss($this->_path.'css/admin/tab.css', 'all');
		$this->context->controller->addCss($this->_path.'css/admin/colorpicker.css', 'all');
		$this->context->controller->addJS($this->_path.'js/admin/tab.js');
		$this->context->controller->addJs($this->_path.'js/admin/colorpicker.js');
		$this->context->controller->addJs($this->_path.'js/admin/eye.js');
		$this->context->controller->addJs($this->_path.'js/admin/utils.js');
		$this->context->controller->addJs($this->_path.'js/admin/custom.js');
		
		$this->context->controller->addJQueryPlugin('fancybox');
		global $currentIndex, $cookie;
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'captiondiv¤video_typediv¤video_iddiv¤imagecaptiondiv';
		
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
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>';
		// Form
		$this->_html .= '
		<h2><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Add New Slider').'</h2>
		<div class="productTabs">
		<ul class="tab">
			<li class="tab-row">
			<a class="nav-manager selected" id="general" href="javascript:void(0);">'.$this->l('General').'</a></li>
			</li>
			<li class="tab-row">
			<a class="nav-manager" id="animations" href="javascript:void(0);">'.$this->l('Animation').'</a></li>
			</li>
			<li class="tab-row">
			<a class="nav-manager" id="caption" href="javascript:void(0);">'.$this->l('Caption').'</a></li>
			</li>
		</ul>
		</div>
			<form id="csslider_add" method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
			<fieldset class="general tab-manager plblogtabs">
				<label>'.$this->l('Background:').'</label>
				<div class="margin-form">
					<input type="radio" checked="checked" onclick="return showBackground(\'image\')" name="image[type]" value="image"/><label class="radioCheck">'.$this->l('Image').'</label>
					<input type="radio" onclick="return showBackground(\'color\')" name="image[type]" value="color"/><label class="radioCheck">'.$this->l('Color').'</label>
					<input type="radio" onclick="return showBackground(\'transparent\')" name="image[type]" value="transparent"/><label class="radioCheck">'.$this->l('Transparent').'</label>
				</div>
				<div class="animation" id="animation_image">
				<div class="margin-form">
					<input type="file" name="image[value]" /><sup> *</sup>
					<div class="clear"></div>
				</div>
				</div>
				<div class="animation" id="animation_color" style="display:none">
					<div class="margin-form">
						<input id="result_animation_colobg" name="image[value]" type="text"/><sup> *</sup> 
						<span id="animation_colobg" style="cursor:pointer">
						<img src="'._PS_ADMIN_IMG_.'color.png"/>
						</span>
					</div>
				</div>
				<label>'.$this->l('URL:').'</label>
				<div class="margin-form">
					<div id="urldiv" style="float: left;">
						<input type="text" name="url" value="'.(Tools::getValue('url') ? Tools::getValue('url') : '#').'" size="55" />
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Displayed:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="display" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="display" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
			</fieldset>
			<fieldset class="animations tab-manager plblogtabs"><label>
				'.$this->l('Transition').'</label><div class="margin-form"><select name="animation[transitions]">';
				foreach ($this->transitions as $transition)
				{
					$this->_html .= '<option value='.$transition.'>'.$transition.'</option>';
				}
				$this->_html .= '</select><p class="preference_description clear">'.$this->l('The appearance transition of this slide.').'</p></div>
			
				<label>'.$this->l('Slot Amount').'</label><div class="margin-form"><input type="text" name="animation[slotamount]" value="7"/><p class="preference_description clear">'.$this->l('The number of slots or boxes the slide is divided into. If you use boxfade, over 7 slots can be juggy.').'</p></div>
				
				<label>'.$this->l('Master Speed').'</label><div class="margin-form"><input type="text" name="animation[masterspeed]" value="300"/><p class="preference_description clear">'.$this->l('Set the Speed of the Slide Transition. Default 300, min:100 max:2000.').'</p></div>
				
				<label>'.$this->l('Target').'</label><div class="margin-form">
				<select name="animation[target]">
					<option value="_self">'.$this->l('_self').'</option>
					<option value="_blank">'.$this->l('_blank').'</option>
				</select>
				<p class="preference_description clear">'.$this->l('A link target (like _self or _blank)').'</p></div>
				
				<label>'.$this->l('Delay').'</label><div class="margin-form"><input type="text" name="animation[delay]" value="9400"/><p class="preference_description clear">'.$this->l('A new delay value for the Slide. If no delay defined per slide, the delay defined via Options ( 9000 ms) will be used.').'</p></div>
				
				<label>'.$this->l('Enable Link').'</label><div class="margin-form"><div>
						<input type="radio" name="animation[enablelink]" value="1" checked="checked"/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="animation[enablelink]" value="0"/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
				</div></div>
				
				<label>'.$this->l('Enable Full Width Video').'</label><div class="margin-form"><div>
						<input type="radio" name="animation[enablefullvideo]" value="1" checked="checked" />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="animation[enablefullvideo]" value="0"/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
				</div></div>
			</fieldset>
			<fieldset class="caption tab-manager plblogtabs">
			<div>'.$this->l('You have to save slider to add caption.').'</div>
			</fieldset>
			<br/>
			<center><input class="button" type="submit" name="submitAddSliderAndBack" value="'.$this->l('Save and back list').'"/>
			<input class="button" type="submit" name="submitAddSliderAndContinue" value="'.$this->l('Save and add caption').'"/>
			<input class="button" type="submit" name="cancelAddSlider" value="'.$this->l('Cancel').'" /></center>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
			</form>
			';
		$this->_html.='<script type="text/javascript">
			function showBackground(classActive)
			{
				$(".animation").hide();
				$("#animation_" + classActive + "").show("slow");
			}
			$(document).ready(function() {
				$("a.captionfancybox").fancybox({
				autoDimensions : false,
				width : 650,
				height : 600
				});
			});
			
			(function($){var initLayout = function() {
				colorEvent("animation_colobg");};
				EYE.register(initLayout, \'init\');
			})(jQuery)
		</script>';
	}
	
	public function ajaxCallLoadCaption($id_caption)
	{
		
		$caption = new Caption($id_caption);
		$slider = new SliderClass($caption->id_caption);
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$content = json_decode($caption->content);
		$divLangName = 'captiondiv¤video_typediv¤video_iddiv¤imagecaptiondiv';
		$html = '<div style="margin-left:150px">
				<input type="hidden" name="id_caption" value="'.$caption->id_caption.'"/>
				<input type="radio" name="addCaption" '.($content->type_caption == "textcaption" ? "checked=checked" : "" ).' onclick="$(\'.csCaption\').hide();$(\'.textcaption\').show();"  value="textcaption" checked="checked"/><label style="float:none">'.$this->l('Text Caption').'</label>
				<input type="radio" name="addCaption" '.($content->type_caption == "imagecaption" ? "checked=checked" : "" ).' onclick="$(\'.csCaption\').hide();$(\'.imagecaption\').show();" value="imagecaption"/><label style="float:none">'.$this->l('Image Caption').'</label>
				<input type="radio" name="addCaption" '.($content->type_caption == "videocaption" ? "checked=checked" : "" ).' onclick="$(\'.csCaption\').hide();$(\'.videocaption\').show();" value="videocaption"/><label style="float:none">'.$this->l('Video Caption').'</label>
				</div><br/>
				<div class="textcaption csCaption" '.($content->type_caption == "textcaption" ? "" : "style='display:none'" ).'>
				<input type="hidden" name="layer" value="'.$caption->layer.'"/>
				<label style="width:150px">'.$this->l('Text:').'</label>
				';
					foreach ($languages as $language)
					{
						$html .= '
					<div id="captiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="textcaption['.$language['id_lang'].']" id="caption_'.$language['id_lang'].'" cols="50" rows="5">'.(isset($content->text->$language['id_lang']) ? $content->text->$language['id_lang'] : '').'</textarea>
					</div>';
					}
					$html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'captiondiv', true);	
					$html .= '</div>
				<div class="imagecaption csCaption" '.($content->type_caption == "imagecaption" ? "" : "style='display:none'" ).'>
				<label style="width:150px">'.$this->l('Image:').'</label>
				';
					foreach ($languages as $language)
					{	
						
						$image = 'image_'.$language['id_lang'];
						if(isset($content->$image))
							$thumb_url = _PS_BASE_URL_._MODULE_DIR_.$this->name.'/images/captions/'.$content->$image;
						$html .= '
						<input type="hidden" name="imagehidden_'.$language['id_lang'].'" value="'.(isset($content->$image) ? $content->$image : '').'"/>
					<div id="imagecaptiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="file" name="imagecaption_'.$language['id_lang'].'"/>
						'.(isset($thumb_url) && $thumb_url ? '<p><img src="'.$thumb_url.'" alt="" title="" style="height:45px;width:45px;"/></p>' : '').'
					</div>';
					}
					$html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'imagecaptiondiv', true);
				$html .= '</div>
				<div class="videocaption csCaption" '.($content->type_caption == "videocaption" ? "" : "style='display:none'" ).'>
				<label style="width:150px">'.$this->l('Video Type:').'</label>';
				foreach ($languages as $language)
				{
					$html .= '
				<div id="video_typediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
					<select name="videocaption_type['.$language['id_lang'].']">';
					
					if(isset($content->video_type->$language['id_lang']) && $slider->video_type->$language['id_lang']==1) $checkedy='selected="selected"';else $checkedy='';
						if(isset($slider->video_type->$language['id_lang']) && $slider->video_type->$language['id_lang']==2) $checkedv='selected="selected"';else $checkedv='';
					
					$html .= '<option value="1" '.$checkedy.'>Youtube</option>';
					$html .= '<option value="2" '.$checkedv.'>Vimeo</option>';
				$html .= '
					</select>
				</div>';
				}
				$html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'video_typediv', true);	
				$html .= '
				<div class="clear"></div>
			<label style="width:150px">'.$this->l('Video ID:').'</label>';
				foreach ($languages as $language)
				{
					$html .= '
				<div id="video_iddiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">';
				
					$html .= '<input type="text" name="videocaption_text['.$language['id_lang'].']" value="'.(isset($content->type_id->$language['id_lang']) ? $content->type_id->$language['id_lang'] : '').'" size="30" />';
					$html .= '
				</div>';
				}
				$html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'video_iddiv', true);	
				$captions = $slider->getCaption();
			
				$html .= '
				</div>
				<div class="clear"></div>'.$this->stringCaption($content).'';
		return $html;
	}
	
	private function _displayUpdateForm()
	{
		$this->context->controller->addCss($this->_path.'css/admin/tab.css', 'all');
		$this->context->controller->addCss($this->_path.'css/admin/colorpicker.css', 'all');
		$this->context->controller->addJS($this->_path.'js/admin/tab.js');
		$this->context->controller->addJs($this->_path.'js/admin/colorpicker.js');
		$this->context->controller->addJs($this->_path.'js/admin/eye.js');
		$this->context->controller->addJs($this->_path.'js/admin/utils.js');
		$this->context->controller->addJs($this->_path.'js/admin/custom.js');
		$this->context->controller->addJQueryPlugin('fancybox');
		global $currentIndex, $cookie;
		//get Slider
		if (!Tools::getValue('id_slider'))
		{
			$this->_html .= '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>';
			return;
		}
		$this->context = Context::getContext();
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$id_lang = $this->context->language->id;
		$slider = new SliderClass((int)Tools::getValue('id_slider'));
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'captiondiv¤video_typediv¤video_iddiv¤imagecaptiondiv';
		
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
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>';
		// Form
		$this->_html .= '
		<h2><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Edit Slider').'</h2>
		<div class="productTabs">
		<ul class="tab">
			<li class="tab-row">
			<a class="nav-manager selected" id="general" href="javascript:void(0);">'.$this->l('General').'</a></li>
			</li>
			<li class="tab-row">
			<a class="nav-manager" id="animations" href="javascript:void(0);">'.$this->l('Animation').'</a></li>
			</li>
			<li class="tab-row">
			<a class="nav-manager" id="caption" href="javascript:void(0);">'.$this->l('Caption').'</a></li>
			</li>
		</ul>
		</div>
			<form id="csslider_add" method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
			<input type="hidden" name="id_slider" value="'.$slider->id_slider.'"/>
			<fieldset class="general tab-manager plblogtabs">
				<label>'.$this->l('Background:').'</label>
				<div class="margin-form">
					<input type="radio" '.($slider->image->type == "image" ? "checked=checked" : '').'  onclick="return showBackground(\'image\')" name="image[type]" value="image"/><label class="radioCheck">'.$this->l('Image').'</label>
					<input type="radio" '.($slider->image->type == "color" ? "checked=checked" : '').'  onclick="return showBackground(\'color\')" name="image[type]" value="color"/><label class="radioCheck">'.$this->l('Color').'</label>
					<input type="radio" '.($slider->image->type == "transparent" ? "checked=checked" : '').'  onclick="return showBackground(\'transparent\')" name="image[type]" value="transparent"/><label class="radioCheck">'.$this->l('Transparent').'</label>
				</div>
				<div class="animation" id="animation_image" '.($slider->image->type == "image" ? '' : 'style="display:none"').'>
				<div class="margin-form">';
				if($slider->image->type == "image")
					$this->_html .='<input type="hidden" name="imagehidden" value="'.$slider->image->value.'"/>';
				$this->_html .='<input type="file" name="image[value]" /><sup> *</sup>';
				$thumb_url = _PS_BASE_URL_._MODULE_DIR_.$this->name.'/images/thumbs/'.$slider->image->value;
				$this->_html.= ($slider->image->type == "image" ? '<p><img src="'.$thumb_url.'" alt="" title="" style="height:45px;width:45px;"/></p>' : '') ;
					$this->_html.= '<div class="clear"></div>
				</div>
				</div>
				<div class="animation" id="animation_color" '.($slider->image->type == "color" ? '' : 'style="display:none"').'>
					<div class="margin-form">
						<input '.($slider->image->type == 'color' ? 'style="background-color:'.$slider->image->value.'"' : '').' value="'.($slider->image->type == 'color' ? $slider->image->value : '').'" id="result_animation_colobg" name="image[value]" type="text"/> <sup> *</sup>
						<span id="animation_colobg" style="cursor:pointer">
						<img src="'._PS_ADMIN_IMG_.'color.png"/>
						</span>
					</div>
				</div>
				<label>'.$this->l('URL:').'</label>
				<div class="margin-form">
					<div id="urldiv" style="float: left;">
						<input type="text"  name="url" value="'.($slider->url ? $slider->url : '#').'" size="55" />
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Displayed:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="display" value="1"'.($slider->display ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="display" value="0"'.($slider->display ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
			</fieldset>
			<fieldset class="animations tab-manager plblogtabs"><label>
				'.$this->l('Transition').'</label><div class="margin-form"><select name="animation[transitions]">';
				foreach ($this->transitions as $transition)
				{
					$this->_html .= '<option value='.$transition.' '.($slider->animation->transitions == ''.$transition.'' ? "selected=selected" : '').'>'.$transition.'</option>';
				}
				$this->_html .= '</select><p class="preference_description clear">'.$this->l('The appearance transition of this slide.').'</p></div>
				
				<label>'.$this->l('Slot Amount').'</label><div class="margin-form"><input type="text" name="animation[slotamount]" value="'.($slider->animation->slotamount ? $slider->animation->slotamount : '').'"/><p class="preference_description clear">'.$this->l('The number of slots or boxes the slide is divided into. If you use boxfade, over 7 slots can be juggy.').'</p></div>
			
				
				<label>'.$this->l('Master Speed').'</label><div class="margin-form"><input type="text" name="animation[masterspeed]" value="'.($slider->animation->masterspeed ? $slider->animation->masterspeed : '').'"/><p class="preference_description clear">'.$this->l('Set the Speed of the Slide Transition. Default 300, min:100 max:2000.').'</p></div>
				
				<label>'.$this->l('Target').'</label><div class="margin-form">
				<select name="animation[target]">
					<option value="_self" '.($slider->animation->target == "_self" ? "selected=selected" : '').'>'.$this->l('_self').'</option>
					<option value="_blank" '.($slider->animation->target == "_blank" ? "selected=selected" : '').'>'.$this->l('_blank').'</option>
				</select>
				<p class="preference_description clear">'.$this->l('A link target (like _self or _blank)').'</p></div>
				
				
				<label>'.$this->l('Delay').'</label><div class="margin-form"><input type="text" name="animation[delay]" value="'.($slider->animation->delay ? $slider->animation->delay : '').'"/><p class="preference_description clear">'.$this->l('A new delay value for the Slide. If no delay defined per slide, the delay defined via Options ( 9000 ms) will be used.').'</p></div>
				
				<label>'.$this->l('Enable Link').'</label><div class="margin-form"><div>
						<input type="radio" name="animation[enablelink]" value="1"'.($slider->animation->enablelink ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="animation[enablelink]" value="0"'.($slider->animation->enablelink ? '' : 'checked="checked"').'/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
				</div></div>
				
				<label>'.$this->l('Enable Full Width Video').'</label><div class="margin-form"><div>
						<input type="radio" name="animation[enablefullvideo]" value="1" '.($slider->animation->enablefullvideo ? 'checked="checked"' : '').'/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="animation[enablefullvideo]" value="0" '.($slider->animation->enablefullvideo ? '' : 'checked="checked"').'/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
				</div></div>
			</fieldset>
		<fieldset class="caption tab-manager plblogtabs">
				<div style="float:left;width:665px" class="resultcaption">
				<h4>'.$this->l('Add Caption').'</h4>
				<div style="margin-left:150px">
				<input type="radio" name="addCaption" onclick="$(\'.csCaption\').hide();$(\'.textcaption\').show();"  value="textcaption" checked="checked"/><label style="float:none">'.$this->l('Text Caption').'</label>
				<input type="radio" name="addCaption" onclick="$(\'.csCaption\').hide();$(\'.imagecaption\').show();" value="imagecaption"/><label style="float:none">'.$this->l('Image Caption').'</label>
				<input type="radio" name="addCaption" onclick="$(\'.csCaption\').hide();$(\'.videocaption\').show();" value="videocaption"/><label style="float:none">'.$this->l('Video Caption').'</label>
				</div><br/>
				<div class="textcaption csCaption">
				<input type="hidden" name="layer" value="'.Caption::getNextLayer().'"/>
				<label style="width:150px">'.$this->l('Text:').'</label>
				';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="captiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="textcaption['.$language['id_lang'].']" id="caption_'.$language['id_lang'].'" cols="50" rows="5">'.Tools::getValue('caption_'.$language['id_lang']).'</textarea>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'captiondiv', true);	
					$this->_html .= '</div>
				<div class="imagecaption csCaption" style="display:none">
				<label style="width:150px">'.$this->l('Image:').'</label>
				';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="imagecaptiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="file" name="imagecaption_'.$language['id_lang'].'"/>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'imagecaptiondiv', true);
				$this->_html .= '</div>
				<div class="videocaption csCaption" style="display:none;">
				<div style="display:block;overflow:hidden;margin:5px 0;"><label style="width:150px">'.$this->l('Video Type:').'</label>';
				foreach ($languages as $language)
				{
					$this->_html .= '
				<div id="video_typediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
					<select name="videocaption_type['.$language['id_lang'].']">';
					$this->_html .= '<option value="1" selected="selected">Youtube</option>';
					$this->_html .= '<option value="2">Vimeo</option>';
				$this->_html .= '
					</select>
				</div>';
				}
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'video_typediv', true);	
				$this->_html .= '
				</div><div style="display:block;overflow:hidden;margin:5px 0;">
			<label style="width:150px">'.$this->l('Video ID:').'</label>';
				foreach ($languages as $language)
				{
					$this->_html .= '
				<div id="video_iddiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">';
				
					$this->_html .= '<input type="text" name="videocaption_text['.$language['id_lang'].']" value="'.Tools::getValue('video_id_'.$language['id_lang']).'" size="30" />';
					$this->_html .= '
				</div>';
				}
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'video_iddiv', true);	
				$captions = $slider->getCaption();
				$this->_html .= '
				</div></div>
				<div class="clear"></div>'.$this->stringCaption().'</div>
				<div style="float:right;width:390px"><h4>'.$this->l('List caption').'</h4>
				<script type="text/javascript">
					function ajaxloadCaption(idcaption)
					{
						$.ajax({
						type: \'post\',
						url: \''.__PS_BASE_URI__.'\' + \'modules/csslider/csslider-ajax.php\',
						data: \'&id_caption=\' + idcaption,
						success: function(result) {
							$(".resultcaption").html(result);
						}
						});
					return false;
					};
				</script>
				<table width="100%" class="table" cellspacing="0" cellpadding="0">
				<thead>
				<tr class="nodrag nodrop">
					<th>'.$this->l('Caption').'</th>
					<th class="center">'.$this->l('Content').'</th>
					<th class="center">'.$this->l('Layer').'</th>
					<th class="center">'.$this->l('Edit').'</th>
					<th class="right">'.$this->l('Delete').'</th>
				</tr>
				</thead>
				<tbody>';
				$stringConfirm='onclick="if (!confirm(\' Are you sure that you want to delete item ?\')) return false "';
				if(isset($captions) and !empty($captions))
				{
					static $irow;
					$irow = 0;
					foreach($captions as $caption)
					{
						$content = json_decode($caption['content']);
						$this->_html .= '<tr class="'.($irow++ % 2 ? 'alt_row' : '').'"> 
						<td class="pointer center">'.$irow.'</td>
						<td class="pointer center">';  
						if($content->type_caption == "textcaption")
							$this->_html .= $content->text->$defaultLanguage;
						if($content->type_caption == "imagecaption")
						{
							$image = 'image_'.$defaultLanguage;
							$thumb_url = _PS_BASE_URL_._MODULE_DIR_.$this->name.'/images/captions/'.$content->$image;
							$this->_html .= '<img src="'.$thumb_url.'" alt="" title="" style="height:35px;width:35px;"/>';
						}
						if($content->type_caption == "videocaption")
						{
							if($content->type_video->$defaultLanguage == 1)
								$this->_html .= '<iframe src="http://www.youtube.com/embed/'.$content->type_id->$defaultLanguage.'?hd=1&amp;wmode=opaque&amp;controls=1&amp;showinfo=0" style="height:40px;width:40px;"></iframe>';
							else
								$this->_html .= '<iframe src="http://player.vimeo.com/video/'.$content->type_id->$defaultLanguage.'?title=0&amp;byline=0&amp;portrait=0" style="height:40px;width:40px;"></iframe>';
						}
						
						$this->_html .= '
						</td>
						<td class="pointer center">'.($caption !== end($captions) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderLayerCaption&id_caption='.$caption['id_caption'].'&way=1&layer='.($caption['layer']+1).'"><img src="'._PS_ADMIN_IMG_.'down.gif" alt="'.$this->l('Down').'" /></a>' : '').($caption !== reset($captions) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderLayerCaption&id_caption='.$caption['id_caption'].'&way=0&layer='.($caption['layer']-1).'"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="'.$this->l('Up').'" /></a>' : '').'</td>
						
						<td class="pointer center"><a class="editcaption" href="#" onclick="return ajaxloadCaption('.$caption['id_caption'].')"><img src="'._PS_ADMIN_IMG_.'edit.gif" alt="Edit" title="Edit" /></a></td>
						
						<td class="pointer center"><a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteCaption&id_caption='.$caption['id_caption'].'\'" '.$stringConfirm.'><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="Delete" title="Delete" /></a></td>
						
						
						</tr>';
					}
					
					
				}
				$this->_html .= '</tbody>
				</table>
				</div>
			</fieldset>
			<br/>
			<center><input class="button" type="submit" name="submitAddSlider" value="'.$this->l('Save').'"/>
			<input class="button" type="submit" name="cancelAddSlider" value="'.$this->l('Cancel').'" /></center>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
			</form>
			';
		$this->_html.='<script type="text/javascript">
			function showBackground(classActive)
			{
				$(".animation").hide();
				$("#animation_" + classActive + "").show("slow");
			}
			$(document).ready(function() {
				$("a.captionfancybox").fancybox({
				autoDimensions : false,
				width : 650,
				height : 600
				});
			});
			
			(function($){var initLayout = function() {
				colorEvent("animation_colobg");};
				EYE.register(initLayout, \'init\');
			})(jQuery)
		</script>';
	}
	

	public function hookHeader($params)
	{
		global $smarty;
		$smarty->assign(array(
			
			'HOOK_CS_SLIDESHOW' => Hook::Exec('csslideshow')
		));
		if ($smarty->tpl_vars['page_name']->value == 'index')
		{
			$this->context->controller->addJS($this->_path.'revolution/js/jquery.themepunch.revolution.min.js');
			$this->context->controller->addJS($this->_path.'revolution/js/jquery.themepunch.plugins.min.js');
			$this->context->controller->addCSS($this->_path.'revolution/css/settings.css');
		}
	}
	
	public function hookCsSlideshow()
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
			$smarty_cache_id = $this->getCacheId();
		}
		
		if (!$this->isCached('csslider.tpl', $smarty_cache_id))
		{
			$sliders = $this->getSliders(true);
			$sliderList = array();
			$captionList = array();
			if(isset($sliders) && !empty($sliders))
			{
				foreach($sliders as $keySl=>$slider)
				{
					$sl = new SliderClass($slider['id_slider']);
					$captions = $sl->getCaption();
					$slider['animation'] = json_decode($slider['animation']);
					$slider['image'] = json_decode($slider['image']);
					if(isset($captions) and !empty($captions))
					{
						foreach($captions as $key=>$caption)
						{
							$slider['caption'][$key] = json_decode($caption['content']);
						}
					}
					$sliderList[$keySl] = $slider;
				}
			}
			if($sliderList)
			{
				$context = Context::getContext();
				$id_shop = $context->shop->id;
				if (file_exists(dirname(__FILE__).'/'.'option_'.$id_shop.'.xml'))
					$option = simplexml_load_file(dirname(__FILE__).'/'.'option_'.$id_shop.'.xml');
				else	
					$option = simplexml_load_file(dirname(__FILE__).'/'.'option_'.Configuration::get('PS_SHOP_DEFAULT').'.xml');
				$smarty->assign(array(
					'path' => $this->_path,
					'sliders' => $sliderList,
					'option' => $option
				));
			}
		}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();
		return $this->display(__FILE__, 'csslider.tpl',$smarty_cache_id);
	}
	
}
?>
