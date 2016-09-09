<?php

abstract class Db extends DbCore
{ 

	/**
	 * Get Db object instance
	 *
	 * @param bool $master Decides whether the connection to be returned by the master server or the slave server
	 * @return Db instance
	 */
	public static function getInstance_slv($master = true)
	{
		static $id = 0;

		// This MUST not be declared with the class members because some defines (like _DB_SERVER_) may not exist yet (the constructor can be called directly with params)

		self::$_servers = array(
			array('server' => _DB_SERVER_SLV_, 'user' => _DB_USER_SLV_, 'password' => _DB_PASSWD_SLV_, 'database' => _DB_NAME_SLV_), /* MySQL Master server */
			// Add here your slave(s) server(s)
				// array('server' => '192.168.0.15', 'user' => 'rep', 'password' => '123456', 'database' => 'rep'),
				// array('server' => '192.168.0.3', 'user' => 'myuser', 'password' => 'mypassword', 'database' => 'mydatabase'),
		);

		$total_servers = count(self::$_servers);
		if ($master || $total_servers == 1)
			$id_server = 0;
		else
		{
			$id++;
			$id_server = ($total_servers > 2 && ($id % $total_servers) != 0) ? $id : 1;
		}

		
		$class = Db::getClass();
		self::$instance[$id_server] = new $class(
			self::$_servers[$id_server]['server'],
			self::$_servers[$id_server]['user'],
			self::$_servers[$id_server]['password'],
			self::$_servers[$id_server]['database']
		);
	
		return self::$instance[$id_server];
	}

	
	public function query($sql)
	{

		/*$this->_query("SET max_heap_table_size = 1024 * 1024 * 64");
		$this->_query("SET tmp_table_size = 1024 * 1024 * 64");*/

		if ($sql instanceof DbQuery)
			$sql = $sql->build();

		$this->result = $this->_query("/* ip ".$this->getRealIP()." */ ".$sql);
		if (_PS_DEBUG_SQL_)
			$this->displayError($sql);
		return $this->result;
	}

	/**
	 * [getRealIP obtener ip real del cliente]
	 * @return [type] [ip address]
	 */
	public function getRealIP() {
		$compret='';
		if(isset(Context::getContext()->employee->id) && Context::getContext()->employee->id != '') {
			$compret = " | Emp id : ".Context::getContext()->employee->id." - ".Context::getContext()->employee->firstname." ".Context::getContext()->employee->lastname;
		}

		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'].$compret;
			
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'].$compret;
		
		return $_SERVER['REMOTE_ADDR'].$compret;
	}

}

?>