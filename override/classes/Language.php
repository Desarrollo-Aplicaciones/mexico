<?php


class Language extends LanguageCore
{
	public static function getLanguagePackListContent($iso, $tar) {
		if (!$tar instanceof Archive_Tar)
		return false;
		$key = 'Language::getLanguagePackListContent_'.$iso;
		if (!Cache::isStored($key))
		Cache::store($key, $tar->listContent());
		 
		return Cache::retrieve($key);
	}

	 
	public static function updateModulesTranslations(Array $modules_list) {

		require_once(_PS_TOOL_DIR_.'tar/Archive_Tar.php');
		 
		$languages = Language::getLanguages(false);

		foreach($languages as $lang) {
			$files_listing = array();

			foreach ($modules_list as $module_name) {
				$iso = $lang['iso_code'];
				$filegz = _PS_TRANSLATIONS_DIR_.$iso.'.gzip';
				 
				clearstatcache();
				
				if (@filemtime($filegz) < (time() - (24 * 3600)))
				Language::downloadAndInstallLanguagePack($iso, null, null, false);
				 
				$gz = new Archive_Tar($filegz, true);
				$files_list = Language::getLanguagePackListContent($iso, $gz);

				foreach ($files_list as $i => $file)
				if (!preg_match('/^modules\/'.$module_name.'\/.*/', $file['filename']))
				unset($files_list[$i]);
				 
				foreach($files_list as $file)
				if (isset($file['filename']) && is_string($file['filename'])) 
				$files_listing[] = $file['filename'];
			}

			$gz->extractList($files_listing, _PS_TRANSLATIONS_DIR_.'../', '');
		}
	}
}