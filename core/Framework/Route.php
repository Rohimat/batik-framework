<?php

	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	/**
	* Manage routing table
	*
	* @package Core\Framework\Route
	*/
	class Route {

		/**
		* Specify regex of routing variable
		*
		* @var mixed $where
		*/
		protected $where = array();

		/**
		* Specify middleware for current routing
		*
		* @var mixed $middleware
		*/
		protected $middleware = array();

		/**
		* Specify prefix for url routing
		*
		* @var string $prefix
		*/
		protected $prefix = '';

		/**
		* Specify namespace for current controller called
		*
		* @var string $namespace
		*/
		protected $namespace = '';

		/**
		* Specify current routing index
		*
		* @var int $index
		*/
		protected $index = null;

		/**
		* Specify return content of router
		*
		* @var string $content
		*/
		protected static $content = '';
		
		/**
		* Create params for current group router
		*
		* @var mixed $params
		*/
		protected static $params = array();

		/**
		* Indicates if the router has been made/found
		*
		* @var boolean $routed
		*/
		protected static $routed = false;
		
		/**
		* Create a new routing
		*/
		public function __construct() {
			if (sizeof(static::$params)) {
				$params = static::$params;
				$this->where = $params['where'];
				$this->prefix = $params['prefix'];
				$this->middleware = $params['middleware'];
				$this->namespace = $params['namespace'];
			}
		}
		
		/**
		* Set regex of routing variable 
		* 
		* @param string $where A string that specified the regex 
		* @return Core\Framework\Route
		*/
		protected function where($where) {
			$this->where = $where;
			return $this;
		}
		
		/**
		* Set namespace of routing controller
		* 
		* @param string $namespace A string that specified the namespace 
		* @return Core\Framework\Route
		*/
		protected function namespaces($namespace) {
			$this->namespace = $namespace;
			return $this;
		}
		
		/**
		* Set prefix of routing URL
		* 
		* @param string $prefix A string that specified the prefix 
		* @return Core\Framework\Route
		*/
		protected function prefix($prefix) {
			$this->prefix = $prefix;
			return $this;
		}
		
		/**
		* Set middleware function to be called before call the controller 
		* 
		* @param mixed $middleware
		* @return Core\Framework\Route
		*/
		protected function middleware($middleware) {
			$this->middleware = $middleware;
			return $this;
		}
		
		/**
		* Get the path of current request URL
		* 
		* @return string
		*/
		protected function path() {
			$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
			$uri = $uri_parts[0];
			$path = substr($uri, strlen(base_url()), strlen($uri));

			return $path;
		}
		
		/**
		* Get status if the router has been made/found
		* 
		* @return boolean
		*/
		protected function found() {
			return static::$routed;
		}
		
		/**
		* Run the router and show the data
		*/
		protected function run() {
			if ($this->found()) {
				echo static::$content;
			} else {
				if (Request::isAjax()) {
					echo 'Error 404: Controller Not Found';
					exit();
				} else {
					View::show('errors.404');
				}
			}
		}
		
		/**
		* Create a group of router 
		* 
		* @param function | string $callback A function of callback or the name of controller
		*/
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
		
		
		/**
		* Create a router from GET request method
		* 
		* @param string $map A string to set url path
		* @param function | string $callback A function of callback or the name of controller
		*/
		protected function get($map, $callback) {
			if (Request::isMethod('get')) {
				$this->map($map, $callback);
			}
		}
		
		/**
		* Create a router from POST request method
		* 
		* @param string $map A string to set url path
		* @param function | string $callback A function of callback or the name of controller
		*/
		protected function post($map, $callback) {
			if (Request::isMethod('post')) {
				$this->map($map, $callback);
			}
		}
		
		/**
		* Create a router from PUT request method
		* 
		* @param string $map A string to set url path
		* @param function | string $callback A function of callback or the name of controller
		*/
		protected function put($map, $callback) {
			if (Request::isMethod('put')) {
				$this->map($map, $callback);
			}
		}
		
		/**
		* Create a router from DELETE request method
		* 
		* @param string $map A string to set url path
		* @param function | string $callback A function of callback or the name of controller
		*/
		protected function delete($map, $callback) {
			if (Request::isMethod('delete')) {
				$this->map($map, $callback);
			}
		}
		
		/**
		* Create a router from any request method
		* 
		* @param string $map A string to set url path
		* @param function | string $callback A function of callback or the name of controller
		*/
		protected function any($map, $callback) {
			$this->map($map, $callback);
		}
		
		/**
		* Mapping the router to callback function or controller
		* 
		* @param string $map A string to set url path
		* @param function | string $callback A function of callback or the name of controller
		*/
		protected function map($map, $callback) {
			if (!empty($this->prefix)) {
				$map = str_replace('//', '/', $this->prefix . '/' . $map);
			}			
			
			$match = true;
			$path = $this->path();
			$args = array();
			
			$arMap = $map == '/' ? array('') : explode('/', $map);				
			$arPath = empty($path) ? array('') : explode('/', $path);
			
			if ($map == $path || ($path == $this->prefix && $map == $this->prefix . '/')) {
				$match = true;
			} elseif (sizeof($arMap) == sizeof($arPath)) {
				for ($i = 0; $i < sizeof($arPath); $i++) {
					$_path = isset($arPath[$i]) ? $arPath[$i] : '';
					$_map = isset($arMap[$i]) ? $arMap[$i] : '';
					$_where = $this->where;
					
					if (substr($_map, 0, 1) == '{' && !empty($_path)) {
						$var = substr($_map, 1, -1);
						$where = isset($_where[$var]) ? $_where[$var] : '';
						$preg = true;
					
						if (!empty($where)) {
							if (!preg_match('/^' . $where . '*$/', $_path)) {
								$match = false;
								break;
							}
						}
						
						$args[] = $_path;
					} elseif ($_map != $_path) {
						$match = false;
						break;
					} 
				}
			} else {
				$match = false;
			}

			if ($match) {
				$next = true;
				
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
				
				if ($next) {
					if (is_callable($callback)) {
						static::$content = call_user_func_array($callback, $args);
					} else {
						$namespaces = $this->namespace;
						if (!empty($namespaces)) {
							$namespaces .= $namespaces . '\\';	
						}

						$arCallback = explode('@', $callback);
						$controller = 'App\\Controllers\\' . $namespaces . $arCallback[0];
						$method = isset($arCallback[1]) ? $arCallback[1] : 'index';

						if (class_exists($controller) && method_exists($controller, $method)) {
							$instance = new $controller();
							static::$content = $instance->$method(...$args);
						} else {
							static::$content = 'Controller or method not found (' . $controller . '->' . $method . ')';
						}
					}

					static::$routed = true;
				}
			}
		}
		
		/**
		* Create automatic routing based on URL
		*/
		protected function auto() {
			$path = $this->path();
			$namespace = '';
			$controller = '';
			$method = '';

			if (!empty($this->prefix)) {
				$path = substr($this->path(), strlen($this->prefix) + 1, strlen($this->path()));
				$namespace = ucwords(str_replace("/", "\\", $this->prefix)) . "\\";
			}

			$arPath = explode('/', $path);
			$strPath = '';
			for ($i = 0; $i < sizeof($arPath); $i++) {
				if ($i == 0) {
					$controller = !empty($arPath[0]) ? $namespace . ucwords(strtolower($arPath[0])) . 'Controller' : ''; 
					$strPath .= $arPath[$i] . '/';
				} elseif ($i == 1) {
					$method = $arPath[1];	
					$strPath .= $arPath[$i] . '/';
				} else {
					$strPath .= '{args}/';
				}
			}
			
			$method = empty($method) ? 'index' : $method;
			if (!empty($controller)) {
				$this->map(substr($strPath, 0, -1), $controller . '@' . $method);
			}
		}
		
		/**
		* Override calling method
		*/
		public static function __callStatic($method, $args) {
			$instance = new Route();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}
?>