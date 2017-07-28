<?php
	
	/*
	|--------------------------------------------------------------------------
	| Facades DB Class
	|--------------------------------------------------------------------------
	| 
	| Facades DB class adalah bentuk statik dari class Database
	| fungsinya untuk menyingkat pemanggilan class Database
	|
	*/

	namespace Core\Framework;

	class DB {
		protected static function __callStatic($method, $args) {
			$instance = new Database();
			return $instance->$method(...$args);
		}
	}

?>