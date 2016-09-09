<?php

class AdminGeolocationController extends AdminGeolocationControllerCore
{
	public function __construct()
	{
		parent::__construct();

		$this->fields_options = array(
			'geolocationRedirectionCountriesConfiguration' => array(
				'title' =>	$this->l('Redirección entre paises'),
				'icon' =>	'world',
				'fields' =>	array(
		 			'PS_REDIRECTION_COUNTRIES' => array(
		 				'title' => $this->l('Redirección entre paises'),
		 				'desc' => $this->l('Esta opción activa el pop-up para la redirección entre paises en caso de que el acceso del usuario sea en una pagina diferente al del pais origen'),
		 				'validation' => 'isUnsignedId',
		 				'cast' => 'intval',
		 				'type' => 'bool'
					),
				),
			),
			'geolocationConfiguration' => array(
				'title' =>	$this->l('Geolocation by IP address'),
				'icon' =>	'world',
				'fields' =>	array(
		 			'PS_GEOLOCATION_ENABLED' => array(
		 				'title' => $this->l('Geolocation by IP address'),
		 				'desc' => $this->l('This option allows you, among other things, to restrict access to your shop for certain countries. See below.'),
		 				'validation' => 'isUnsignedId',
		 				'cast' => 'intval',
		 				'type' => 'bool'
					),
				),
			),
			'geolocationCountries' => array(
				'title' =>	$this->l('Options'),
				'icon' =>	'world',
				'description' => $this->l('The following features are only available if you enable the Geolocation by IP address feature.'),
				'fields' =>	array(
		 			'PS_GEOLOCATION_BEHAVIOR' => array(
						'title' => $this->l('Geolocation behavior for restricted countries'),
						'type' => 'select',
						'identifier' => 'key',
						'list' => array(array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->l('Visitors cannot see your catalog.')),
										array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->l('Visitors can see your catalog but cannot place an order.'))),
					),
		 			'PS_GEOLOCATION_NA_BEHAVIOR' => array(
						'title' => $this->l('Geolocation behavior for other countries'),
						'type' => 'select',
						'identifier' => 'key',
						'list' => array(array('key' => '-1', 'name' => $this->l('All features are available')),
										array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->l('Visitors cannot see your catalog.')),
										array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->l('Visitors can see your catalog but cannot place an order.')))
					),
				),
			),
			'geolocationWhitelist' => array(
				'title' =>	$this->l('IP address whitelist'),
				'icon' =>	'world',
				'description' => $this->l('You can add IP addresses that will always be allowed to access your shop (e.g. Google bots\' IP).'),
				'fields' =>	array(
		 			'PS_GEOLOCATION_WHITELIST' => array('title' => $this->l('Whitelisted IP addresses'), 'type' => 'textarea_newlines', 'cols' => 80, 'rows' => 30),
				),
				'submit' => array(),
			),
		);
	}
}

