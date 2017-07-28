<?php
	
	/*
	|--------------------------------------------------------------------------
	| Request Class
	|--------------------------------------------------------------------------
	| 
	| Request Class adalah class untuk mengatur lalu lintas data yang melewati request,
	| class ini akan mengambil data dari beberapa variable global
	|
	*/

	namespace Core\Framework;

	class Request {
		protected $request;
		protected $files;
		protected $cookie;
		protected $method;
		
		// Method constructor
		public function __construct() {
			$this->request = $_REQUEST;
			$this->files = $_FILES;
			$this->cookie = $_COOKIE;
			$this->method = $_SERVER['REQUEST_METHOD'];
		}	
			
		// Method untuk mengetahui jenis request
		protected function method() {
			return $this->method;
		}
		
		// Method untuk mengambil semua request
		protected function all() {
			return json_decode(json_encode($this->request));
		}
		
		// Method untuk mengambil data yang berasal dari cookie
		protected function cookie($key) {
			$cookieName = 'COOKIE_' . config('app.key') . $key;

			if (isset($this->cookie[$cookieName])) {
				return $this->cookie[$cookieName];
			} else {
				return null;
			}
		}
		
		// Method untuk mengambil request berdasarikan nama
		protected function get($name, $default = '') {
			if (isset($this->request[$name]) && !empty($this->request[$name])) {
				return $this->request[$name];
			} else {
				return $default;
			}
		}
		
		// Method untuk mengambil request yang berupa file
		protected function file($name) {
			if (isset($this->files[$name])) {
				return $this->files[$name];
			} else {
				return null;
			}
		}
		
		// Method untuk memeriksa apakah request dengan nama tertentu tersedia atau tidak
		protected function has($name) {
			if (isset($this->request[$name]) || isset($this->files[$name])) {
				return true;
			} else {
				return false;
			}
		}
		
		// Method untuk memeriksa apakan jenis request sesuai
		protected function isMethod($method) {
			if (strtolower($this->method()) == strtolower($method)) {
				return true;
			} else {
				return false;
			}
		}
		
		// Method untuk memerisa apakah request menggunakan ajax
		protected function isAjax() {
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
				return true;
			} else {
				return false;
			}
		}

		public static function __callStatic($method, $args) {
			$instance = new Request();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}

?>