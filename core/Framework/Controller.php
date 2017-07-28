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

	class Controller {
		public $db = null;
		public $response = null;
		public $request = null;
		
		// Method untuk menginisialisasi db, request dan response
		public function __construct() {
			$this->db = new Database();
			$this->request = new Request();
			$this->response = new Response();
		}	
	}

?>