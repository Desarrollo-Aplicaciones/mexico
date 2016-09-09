<?php

require(dirname(__FILE__).'/config/config.inc.php');

$id_address=$_REQUEST['id_address'];
$is_rfc = $_REQUEST['is_rfc'];
$firstname = '';
$lastname = '';
if ( isset(Context::getContext()->customer->firstname) ) {
	$firstname = Context::getContext()->customer->firstname;
}

if ( isset(Context::getContext()->customer->lastname) ) {
	$lastname = Context::getContext()->customer->lastname;
}

$address['id_country']=$_REQUEST['id_country'];
$address['id_state']=$_REQUEST['id_state'];
$address['id_customer']=$_REQUEST['id_customer'];
$address['alias']=$_REQUEST['alias'];
$address['lastname']=$lastname;
$address['firstname']=$firstname;
$address['address1']=$_REQUEST['address1'];
$address['address2']=$_REQUEST['address2'];
$address['city']=$_REQUEST['city'];
$address['phone']=$_REQUEST['phone'];
$address['phone_mobile']=$_REQUEST['phone_mobile'];
$address['postcode']=$_REQUEST['postcode'];
$address['id_colonia']=$_REQUEST['id_colonia'];
$address['date_upd']=date('Y-m-d H:i:s');
if(isset($_REQUEST['is_rfc'])){
	$address['is_rfc'] = $_REQUEST['is_rfc'];
	$address['dni'] = $_REQUEST['rfc'];
}
$city_id=$_REQUEST['city_id'];

if (isset($address['is_rfc']) && $address['is_rfc'] > 0){
	$firstname = Context::getContext()->customer->firstname;
	$lastname = Context::getContext()->customer->lastname;
	$address['id_country'] = Context::getContext()->country->id;
	$address['date_add'] = $address['date_upd'];
	$address['active']=$_REQUEST['active'];
	$address['deleted']=0;
	Db::getInstance()->insert('address', $address);
	$Id_address=Db::getInstance()->Insert_ID(); 
		Db::getInstance()->insert('address_city', array( 'id_address'=>(int)$Id_address, 'id_city'=>(int)$city_id ));

}
elseif($id_address > 0){
    Db::getInstance()->update('address', $address, 'id_address = '.$id_address );
    Db::getInstance()->update('address_city', array( 'id_city'=>(int)$city_id ), 'id_address = '.$id_address );
}
else{
	$address['date_add'] = $address['date_upd'];
	$address['active']=$_REQUEST['active'];
	$address['deleted']=0;
	Db::getInstance()->insert('address', $address);
		$Id_address=Db::getInstance()->Insert_ID(); 

		if($Id_address == 0) {
			$Id_address = $address->id;
			
			Db::getInstance()->update('address_city', array( 'id_city'=>(int)$city_id ), 'id_address = '.(int)$Id_address );

		} else {

			Db::getInstance()->insert('address_city', array( 'id_address'=>(int)$Id_address, 'id_city'=>(int)$city_id ));
		}
	}