<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	/**
	* Alias of Database Class
	*
	* @package Core\Framework\DB
	*/
	class DB {
		protected static function __callStatic($method, $args) {
			$instance = new Database();
			return $instance->$method(...$args);
		}
	}

?>