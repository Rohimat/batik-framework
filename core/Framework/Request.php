<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	/**
	* Manage application request
	*
	* @package Core\Framework\Request
	*/
	class Request {

		/**
		* All of request variable
		*
		* @var mixed $request
		*/
		protected $request;

		/**
		* All of request files
		*
		* @var mixed $files
		*/
		protected $files;

		/**
		* All of request cookie
		*
		* @var mixed $cookie
		*/
		protected $cookie;

		/**
		* Specify the request method
		*
		* @var string $method
		*/
		protected $method;
		
		/**
		* Initializing all request
		*/
		public function __construct() {
			$this->request = $_REQUEST;
			$this->files = $_FILES;
			$this->cookie = $_COOKIE;
			$this->method = $_SERVER['REQUEST_METHOD'];
		}	
			
		/**
		* Get the request method
		*/
		protected function method() {
			return $this->method;
		}
		
		/**
		* Get all request variable into json
		* 
		* @return mixed
		*/
		protected function all() {
			return json_decode(json_encode($this->request));
		}
		
		/**
		* Get cookie variable using their name
		* 
		* @param string $key A string that specified the name of cookie
		* @return string | null
		*/
		protected function cookie($key) {
			$cookieName = 'COOKIE_' . config('app.key') . $key;

			if (isset($this->cookie[$cookieName])) {
				return $this->cookie[$cookieName];
			} else {
				return null;
			}
		}
		
		/**
		* Get request variable by name
		* 
		* @param string $name A string that specified the name of request
		* @param string $default A string that specified the default value if request does't exists
		* @return string
		*/
		protected function get($name, $default = '') {
			if (isset($this->request[$name]) && !empty($this->request[$name])) {
				return $this->request[$name];
			} else {
				return $default;
			}
		}
		
		/**
		* Get files variable by name
		* 
		* @param string $name A string that specified the name of files
		* @return file | null
		*/
		protected function file($name) {
			if (isset($this->files[$name])) {
				return $this->files[$name];
			} else {
				return null;
			}
		}
		
		/**
		* Determine if the request exists
		* 
		* @param string $name A string that specified the name of request
		* @return boolean
		*/
		protected function has($name) {
			if (isset($this->request[$name]) || isset($this->files[$name])) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		* Determine if the request method same as you want
		* 
		* @param string $name A string that specified the name of method
		* @return boolean
		*/
		protected function isMethod($method) {
			if (strtolower($this->method()) == strtolower($method)) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		* Determine if the request is using ajax
		* 
		* @return boolean
		*/
		protected function isAjax() {
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		* Override calling method
		*/
		public static function __callStatic($method, $args) {
			$instance = new Request();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}

?>