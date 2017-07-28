<?php

	/*
	|--------------------------------------------------------------------------
	| Router Class
	|--------------------------------------------------------------------------
	| 
	| Router Class adalah class untuk penerjemah antara URL dengan function atau Controller,
	| class ini akan mengatur semua lalu lintas yang diminta dari URL
	|
	*/

	namespace Core\Framework;

	class Route {
		protected $routes = array();
		protected $where = array();
		protected $middleware = array();
		protected $prefix = '';
		protected $namespace = '';
		protected $index = null;
		protected $content = '';

		protected static $params = array();
		protected static $routed = false;
		
		// Method constructor
		public function __construct() {
			if (sizeof(static::$params)) {
				$params = static::$params;
				$this->where = $params['where'];
				$this->prefix = $params['prefix'];
				$this->middleware = $params['middleware'];
				$this->namespace = $params['namespace'];
			}
		}
		
		// Method untuk membatasi argument sebuah URL
		protected function where($where) {
			$this->where = $where;
			return $this;
		}
		
		// Method untuk menentukan namespace dari Controller
		protected function namespaces($namespace) {
			$this->namespace = $namespace;
			return $this;
		}
		
		// Method untuk menentukan prefix sebuah router
		protected function prefix($prefix) {
			$this->prefix = $prefix;
			return $this;
		}
		
		// Method untuk menentukan middleware sebuah router
		protected function middleware($middleware) {
			$this->middleware = $middleware;
			return $this;
		}
		
		// Method untuk mengambil nama path dari URL
		protected function path() {
			$uri = $_SERVER['REQUEST_URI'];
			$path = substr($uri, strlen(base_url()), strlen($uri));

			return $path;
		}
		
		// Method untuk memeriksa apakah router ditemukan atau tidak
		protected function found() {
			return static::$routed;
		}
		
		// Method untuk menjalankan router
		protected function run() {
			if ($this->found()) {
				echo $this->content;
			} else {
				View::show('errors.404');
			}
		}
		
		// Method untuk membuat group router 
		protected function group($callback) {
			$params = array(
				'where' => $this->where,
				'prefix' => $this->prefix,
				'middleware' => $this->middleware,
				'namespace' => $this->namespace
			);
			
			static::$params = $params;
			call_user_func($callback);
			static::$params = array();
		}
		
		// Method untuk membuat router dengan method GET
		protected function get($map, $callback) {
			if (Request::isMethod('get')) {
				$this->map($map, $callback);
			}
		}
		
		// Method untuk membuat router dengan method POST
		protected function post($map, $callback) {
			if (Request::isMethod('post')) {
				$this->map($map, $callback);
			}
		}
		
		// Method untuk membuat router dengan method PUT
		protected function put($map, $callback) {
			if (Request::isMethod('put')) {
				$this->map($map, $callback);
			}
		}
		
		// Method untuk membuat router dengan method DELETE
		protected function delete($map, $callback) {
			if (Request::isMethod('delete')) {
				$this->map($map, $callback);
			}
		}
		
		// Method untuk membuat router dengan semua method
		protected function any($map, $callback) {
			$this->map($map, $callback);
		}
		
		// Method untuk mapping router
		protected function map($map, $callback) {
			// Cek prefix
			if (!empty($this->prefix)) {
				$map = $this->prefix . '/' . $map;
			}			
			
			$match = true;
			$path = $this->path();
			
			$arMap = $map == '/' ? array('') : explode('/', $map);				
			$arPath = explode('/', $path);
			$args = array();
			
			// Looping bagian dari map
			for ($i = 0; $i < sizeof($arMap); $i++) {
				if (substr($arMap[$i], 0, 1) == '{') {
					$var = substr($arMap[$i], 1, -1);
					$preg = preg_match('/^' . $this->where[$var] . '*$/', $arPath[$i]);

					if (sizeof($this->where) && !$preg) {
						$match = false;
						break;
					} else {
						$args[] = $arPath[$i];
					}
				} elseif ($arMap[$i] != $arPath[$i]) {
					$match = false;
					break;
				} 
			}
			
			// Cek apakah map sesuai dengan URL diminta
			if ($match) {
				$next = true;
				
				// Cek apakah tersedia middleware
				if (sizeof($this->middleware)) {
					foreach ($this->middleware as $middleware) {
						if ($next) {
							$middleware = 'App\\Middleware\\' . ucwords(strtolower($middleware)) . 'Middleware::handler';
							if (is_callable($middleware)) {
								$next = call_user_func($middleware);	
							}
						}
					}
				}
				
				// Cek apakah middleware menghasilkan true
				if ($next) {
					if (is_callable($callback)) {
						// Panggil callback function
						$this->content = call_user_func_array($callback, $args);
					} else {
						$namespaces = $this->namespace;
						if (!empty($namespaces)) {
							$namespaces .= $namespaces . '\\';	
						}

						$arCallback = explode('@', $callback);
						$controller = 'App\\Controllers\\' . $namespaces . $arCallback[0];
						$method = isset($arCallback[1]) ? $arCallback[1] : 'index';
						
						// Cek apakah controller dan method tersedia
						if (class_exists($controller) && method_exists($controller, $method)) {
							// Jalankan controller
							$instance = new $controller();
							$this->content = $instance->$method(...$args);
						} else {
							$this->conteht = 'Controller or method not found (' . $controller . '->' . $method . ')';
						}
					}

					static::$routed = true;
				}
			}
		}
		
		// Method untuk membuat router berjalan secara otomatis
		protected function auto() {
			$path = substr($this->path(), strlen($this->prefix) + 1, strlen($this->path()));
			$arPath = explode('/', $path);
			$controller = '';
			$method = '';
			
			// Looping bagian URL dan terjemahkan kedalam bentuk map dan controller
			for ($i = 0; $i < sizeof($arPath); $i++) {
				if ($i == 0) {
					$controller = ucwords(strtolower($arPath[0])) . 'Controller'; 
				} elseif ($i == 1) {
					$method = $arPath[1];	
				}
			}
			
			$method = empty($method) ? 'index' : $method;
			if (!empty($controller)) {
				$this->map($path, $controller . '@' . $method);
			}
		}

		public static function __callStatic($method, $args) {
			$instance = new Route();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}
?>