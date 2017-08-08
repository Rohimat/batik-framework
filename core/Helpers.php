<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	use Core\Facades\Request;
	use Core\Facades\Session;
	use Core\Facades\View;
	use Core\Classes\Response;

	/**
	* Return base URL
	* 
	* @return string
	*/
	function base_url() {
		$uri = $_SERVER['REQUEST_URI'];
		$base = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
	
		return $base;
	}
	
	/**
	* Return URL with hostname
	* 
	* @param string $url
	* @return string
	*/
	function full_url($url = '') {
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . url($url);
	}

	/**
	* Return URL without hostname
	* 
	* @param string $url
	* @return string
	*/
	function url($url = '') {
		return base_url() . $url;
	}
	
	/**
	* Return the asset filename
	* 
	* @param string $asset
	* @return string
	*/
	function asset($asset = '') {
		return base_url() . $asset;	
	}
	
	/**
	* Determine if the csrf token is valid
	* 
	* @return boolean
	*/
	function csrf_check() {
		$token = isset($_SERVER['HTTP_X-CSRF-TOKEN']) ? $_SERVER['HTTP_X-CSRF-TOKEN'] : '';
		$token = empty($token) ? Request::get('csrf_token') : $token;

		if (Session::get('csrf_token') == $token) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* Generate new csrf_token
	* 
	* @return string
	*/
	function csrf_token() {
		$token = md5(uniqid(rand(), TRUE));
		Session::set('csrf_token', $token);
		Session::set('csrf_token_time', time());

		return $token;
	}

	/**
	* Create csrf token field inside form
	* 
	* @return string
	*/
	function csrf_field() {
		return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
	}
	
	/**
	* Get flashing data from cookies
	* 
	* @param string $key
	* @return string
	*/
	function flash($key) {
		return Request::cookie($key);
	}
	
	/**
	* Parsing string into directory format
	* 
	* @param string $dir
	* @return string
	*/
	function parse_dir($dir) {
		if (!empty($dir)) {
			return $dir . '/';
		} else {
			return '';	
		}
	}
	
	/**
	* Get the base directory
	* 
	* @return string
	*/
	function base_path() {
		return DIR;
	}
	
	/**
	* Get the app directory
	* 
	* @param string $dir
	* @return string
	*/
	function app_path($dir = '') {
		return DIR . 'app/' . parse_dir($dir);
	}
	
	/**
	* Get the public directory
	* 
	* @param string $dir
	* @return string
	*/
	function public_path($dir = '') {
		return DIR . 'public/' . parse_dir($dir);
	}
	
	/**
	* Get the storage directory
	* 
	* @param string $dir
	* @return string
	*/
	function storage_path($dir = '') {
		return DIR . 'storage/' . parse_dir($dir);
	}
	
	/**
	* Get the view directory
	* 
	* @param string $dir
	* @return string
	*/
	function views_path($dir = '') {
		return app_path('Views') . parse_dir($dir);
	}
	
	/**
	* Show the variable values and exit
	* 
	* @param string $var
	* @return string
	*/
	function vd($var) {
		var_dump($var);
		exit();
	}
	
	/**
	* Get the config file values
	* 
	* @param string $name
	* @return string
	*/
	function config($name = '') {
		$config = require DIR . 'config/config.php';
		if (!empty($name)) {
			$arr = explode('.', $name);
			foreach ($arr as $n) {
				$config = $config[$n];
			}
		}

		return $config;
	}
	
	/**
	* Short function to create the view
	*
	* @param string | null $name
	* @param mixed $data
	* @return Core\Framework\View
	*/
	function view($name, $data = array()) {
		return View::make($name, $data);
	}
	
	/**
	* Short function to create the response
	*
	* @param string | null $response
	* @return Core\Framework\Response
	*/
	function response($response = null) {
		return new Response($response);
	}
	
	/**
	* Determine if the request is ajax
	* 
	* @return boolean
	*/
	function is_ajax() {
		return Request::isAjax();
	}
	
	/**
	* Determine if the array is associative
	* 
	* @param array $arr
	* @return boolean
	*/
	function is_assoc(array $arr) {
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/**
	* Check the debug config and return show the debug content
	* 
	* @param string $debug
	* @return string | boolean
	*/
	function debug($debug) {
		if (config('app.debug')) {
			echo $debug;
			exit();
		} else {
			return true;
		}
	}
	
	/**
	* Copy whole directory
	* 
	* @param string $src
	* @param string $dst
	*/
	function recurse_copy($src, $dst) { 
		$dir = opendir($src); 

		if (!is_dir($dst)) {
			@mkdir($dst); 
		}

		while($file = readdir($dir)) { 
			if (($file != '.' ) && ($file != '..')) { 
				if (is_dir($src . '/' . $file)) { 
					recurse_copy($src . '/' . $file, $dst . '/' . $file); 
				} else { 
					copy($src . '/' . $file, $dst . '/' . $file); 
				} 
			} 
		} 

		closedir($dir); 
	} 

?>