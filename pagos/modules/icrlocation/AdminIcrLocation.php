<?php

class AdminIcrLocation extends ModuleAdminController
{
	/**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
	
	 /**
     * initContent
     */
	public function initContent() 
	{
		parent::initContent();
		global $cookie;
		$token=md5(pSQL(_COOKIE_KEY_.'AdminModules'.(int)Tab::getIdFromClassName('AdminModules').(int)$cookie->id_employee));
		header('Location: index.php?controller=AdminModules&token='.$token.'&configure=icrlocation');
		exit;
	}
}