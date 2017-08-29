<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	use Core\Framework\Request;
	use Core\Framework\Session;
	use Core\Framework\Response;
	use Core\Framework\View;
	use Core\Framework\Auth;

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
	* Return the cdn URL
	* 
	* @param string $url
	* @return string
	*/
	function cdn($url = '') {
		return config('app.cdn') . '/' . $url;	
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
				$config = isset($config[$n]) ? $config[$n] : '';
			}
		}

		return $config;
	}

	/**
	* Get the references file values
	* 
	* @param string $name
	* @return string
	*/
	function references($name = '', $key = '') {
		$ref = require DIR . 'config/references.php';
		if (!empty($name)) {
			$arr = explode('.', $name);
			foreach ($arr as $n) {
				$ref = isset($ref[$n]) ? $ref[$n] : '';
			}
		}

		if (!empty($key)) {
			$ref = isset($ref[$key]) ? $ref[$key] : '';
		}

		return $ref;
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
	function is_assoc($arr) {
		if (array() === $arr) return false;
		return array_keys((array)$arr) !== range(0, count($arr) - 1);
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
	* Return the user auth
	*/
	function user() {
		return Auth::user();
	}

	/**
	* Redirect the page
	* 
	* @param string $url
	*/
	function redirect($url) {
		header("Location:" . url($url));
	}

	/**
	* Encrypt the string
	* 
	* @param string $text
	* @return string
	*/
	function encrypt($value) {
		$key1 = substr(config('encryption.keyA') . '$$$$$$$$$$@@@@@@@@@@##########$$', 0, 32);
		$key2 = substr(config('encryption.keyB') . '12345678900987654321012345678901', 0, 32);
		$output = "";
		$value = trim($value);

		if (!empty($value)){
			$output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key1, $value, MCRYPT_MODE_ECB, $key2);
			$output = trim(base64_encode($output));
		}
		
		return trim($output);
	}

	/**
	* Decrypt the string
	* 
	* @param string $text
	* @return string
	*/
	function decrypt($value) {
		$key1 = substr(config('encryption.keyA') . '$$$$$$$$$$@@@@@@@@@@##########$$', 0, 32);
		$key2 = substr(config('encryption.keyB') . '12345678900987654321012345678901', 0, 32);
		$output = "";
		$value = trim($value);

		try {
			if (!empty($value)){
				$output = @base64_decode(trim($value));
				
				if (!empty($output)) {
					$output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key1, $output, MCRYPT_MODE_ECB, $key2);
				}
			}
		} catch (Exception $e) {
			$output = $value;
		}

		return trim($output);
	}

	/**
	* Get the session unique ID
	* 
	* @return string
	*/
	function session_key() {
		return Session::get('UNIQUE_ID');
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

	/**
	* Convert date format
	* 
	* @param string $Date
	* @param string $Format
	* @return string
	*/
	function format_date($Date = '00-00-0000', $Format = 'LongDate'){
		global $Ref;
		if (substr($Date, 0, 2) != '00' && !empty($Date)){
			$arHari = array("Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu");
			$arBulan = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

			if (strlen($Date) == 6){
				$Tt = intval(substr($Date, -2, 2)) > 50 ? "19" . substr($Date, -2, 2) : "20" . substr($Date, -2, 2) ;
				$Date = substr($Date, 0, 2) . "-" . substr($Date, 2, 2) . "-" . $Tt;
			}
			$Day = substr($Date, 2, 1) == '-' || substr($Date, 2, 1) == '/' ? substr($Date, 0, 2) : substr($Date, 8, 2);
			$Month = substr($Date, 2, 1) == '-' || substr($Date, 2, 1) == '/' ? substr($Date, 3, 2) : substr($Date, 5, 2);
			$Year = substr($Date, 2, 1) == '-' || substr($Date, 2, 1) == '/' ? substr($Date, 6, 4) : substr($Date, 0, 4);
			$Year2 = substr($Year, 2, 2);
			$Hari = $arHari[date("w", mktime(0, 0, 0, $Month, $Day, $Year))];
			$Bulan = $arBulan[($Month * 1) - 1];
			switch ($Format){
				case "SQL":return "$Year-$Month-$Day";break;
				case "IDN":return "$Day-$Month-$Year";break;
				case "UN":return "{$Day}{$Month}{$Year2}";break;
				case "ShortDate":return "$Day $Bulan $Year";break;
				case "ShortDateNum":return "$Day$Month$Year2";break;
				case "LongDate":default:return "$Hari, $Day $Bulan $Year";break;
			}
		}
		else return "";
	}

?>