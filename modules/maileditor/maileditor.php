<?php

class Maileditor extends Module
{
	/** @var max image size */
	protected $maxImageSize = 307200;

	function __construct()
	{
		$this->name = 'maileditor';
		$this->tab = 'Tools';
		$this->version = '1.0';
		
		parent::__construct();
		
		$this->displayName = $this->l('Mail Template Editor');
		$this->description = $this->l('Mail Template Editor helps you to edit all the emails send from prestashop. You can edit using rich text editor.');
	}

	function install()
	{
		if (!parent::install())
			return false;
		return $this->registerHook('top');
	}
    function putContents()
    {
        $languages = Language::getLanguages();
        foreach ($languages as $language)
        {
            $this->_putMailFileContents( $language ) ;
        }
    }
	function getContent()
	{

		/* display the module name */
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$errors = '';

		/* update the editorial xml */
		if (isset($_POST['submitUpdate']))
		{
			// Forbidden key
            $this->putContents() ;
		}

		/* display the editorial's form */
		$this->_html .= $this->_displayForm();
	
		return $this->_html;
	}
    private function _displayMailTemplates()
    {
/* Languages preliminaries */
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$iso = Language::getIsoById($defaultLanguage);

        $dir = dirname(__FILE__) . "/../../mails/". $iso . "/" ;
        $files = scandir( $dir ) ;
        $cnt = count($files) ;
        $html = '<div style="width:244px;float:left;margin-right:5px;"><form method="post" action="'.$_SERVER['REQUEST_URI'].'" >
			<fieldset style="padding:0;">
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>
				<label>'.$this->l('Existing Mail Templates').'</label><br/><br/><br/>' ;
        $html .= '<table class="table" style="border:none;">
                    <th>Fichero</th><th>Editar</th>';

        $tokenModules = $_REQUEST['token'] ;//Tools::getAdminToken('AdminModules'.intval(Tab::getIdFromClassName('AdminModules')).intval($cookie->id_employee));

        for( $i = 0; $i < $cnt; $i ++ )
        {
            if( preg_match('/.html$/', $files[$i]) )
            {
                $link = '<a href="index.php?tab=AdminModules&token=' . $tokenModules . '&configure=' . urlencode($this->name) . '&file='.$files[$i].'">' ;
                $link .= '<img src="' . __PS_BASE_URI__ . 'img/admin/edit.gif" /></a>' ;

                $html .= '<tr>
                        <td style="float:left;">'. $files[$i] . '</td>
                        <td>'.$link.'</td>
                      </tr>' ;
            }
        }
        $html .= '</table>' ;
        $html .= '</fieldset>' ;
        $html .= '</form></div>' ;
        return $html ;
    }
	private function _displayForm()
	{
        $html = $this->_displayMailTemplates() ;

        if( !isset($_REQUEST['file']) )
        {
            return $html ;
        }
		global $cookie;
		
		/* Languages preliminaries */
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$iso = Language::getIsoById($defaultLanguage);
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$divLangName = 'cpara';

		$this->_html .=  '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
		
		$html .= '
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
		<div style="width:650px;float:left;">
		<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
			<fieldset style="margin-top:5px;margin-top: 0px;">
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />Editor de Contenidos E-mail</legend>
				<div class="margin-form">';
				
		$html .= '
					<div class="clear"></div>
				</div>

				<div class="margin-form" style="padding: 0 0 1em 0;">';

				foreach ($languages as $language)
				{
                    $existing_file = stripslashes($this->_getMailFileContents( $language ) ) ;// ($xml ? stripslashes(htmlspecialchars($xml->body->{'paragraph_'.$language['id_lang']})) : '') ;
					$html .= '
					<div id="cpara_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<textarea class="rte" cols="60" rows="30" id="body_mail_'.$language['id_lang'].'" name="body_mail_'.$language['id_lang'].'">' . $existing_file . '</textarea>
					</div>';
				 }

				$html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'cpara', true);
				
				$html .= '
					<div class="clear"></div>
				</div>
				<div class="clear pspace"></div>
				<div class="margin-form clear"><input type="submit" name="submitUpdate" value="'.$this->l('Update the file').'" class="button" /></div>
			</fieldset>
		</form></div>';
        return $html ;
	}
    function _putMailFileContents( $lang )
    {
        if( !isset($_REQUEST['file']) )
        {
            return "" ;
        }
        $filename = $_REQUEST['file'] ;
        if( !$filename )
        {
            return "" ;
        }
        $contents = $_REQUEST['body_mail_' . $lang['id_lang']] ;
        $file = dirname(__FILE__) . "/../../mails/". $lang['iso_code'] . "/" . $filename ;
        file_put_contents( $file, $contents ) ;
    }
    function _getMailFileContents( $lang )
    {
        if( !isset($_REQUEST['file']) )
        {
            return "" ;
        }
        $filename = $_REQUEST['file'] ;
        if( !$filename )
        {
            return "" ;
        }

        $file = dirname(__FILE__) . "/../../mails/". $lang['iso_code'] . "/" . $filename ;
        $cotents = @file_get_contents( $file ) ;
        return $cotents ;
    }
}

?>