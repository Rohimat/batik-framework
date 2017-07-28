<?php
	
	/*
	|--------------------------------------------------------------------------
	| Controller Class
	|--------------------------------------------------------------------------
	| 
	| Controller Class adalah class utama untuk membangun sebuah controller,
	| Class ini akan di extend di semua Controller
	|
	*/

	namespace Core\Framework;

	class Model extends Database {
		protected $table = '';
		protected $fillable = array();
		
		// Method untuk menginisialisasi db
		public function __construct() {
			parent::__construct();
		}	

		public static function __callStatic($method, $args) {
			$instance = new Model();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}

?>