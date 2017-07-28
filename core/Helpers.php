<?php
	
	use Core\Facades\Request;
	use Core\Facades\Session;
	use Core\Facades\View;
	use Core\Classes\Response;

	/*
	|--------------------------------------------------------------------------
	| Fungsi Pembantu
	|--------------------------------------------------------------------------
	| 
	| File ini berisi fungsi-fungsi untuk membantu memudahkan, 
	| semua fungsi tanpa Class akan dimasukkan di file ini
	|
	*/

	// Fungsi untuk mengambil URL dasar
	function base_url() {
		$uri = $_SERVER['REQUEST_URI'];
		$base = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
	
		return $base;
	}
	
	// Fungsi untuk mengambil URL berikut dengan hostnamenya
	function full_url($url = '') {
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . url($url);
	}

	// Fungsi untuk mengambil URL
	function url($url = '') {
		return base_url() . $url;
	}
	
	// Fungsi untuk mengambil asset URL
	function asset($asset = '') {
		return base_url() . $asset;	
	}
	
	// Fungsi untuk memeriksa keabsahan token
	function csrf_check() {
		$token = isset($_SERVER['HTTP_X-CSRF-TOKEN']) ? $_SERVER['HTTP_X-CSRF-TOKEN'] : '';
		$token = empty($token) ? Request::get('csrf_token') : $token;

		if (Session::get('csrf_token') == $token) {
			return true;
		} else {
			return false;
		}
	}
	
	// Fungsi untuk membuat token baru
	function csrf_token() {
		$token = md5(uniqid(rand(), TRUE));
		Session::set('csrf_token', $token);
		Session::set('csrf_token_time', time());

		return $token;
	}

	// Fungsi untuk menampilkan csrf field pada form
	function csrf_field() {
		return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
	}
	
	// Fungsi untuk mengambil teks untuk informasi flash
	function flash($key) {
		return Request::cookie($key);
	}
	
	// Fungsi untuk mengkonversi text ke bentuk folder
	function parse_dir($dir) {
		if (!empty($dir)) {
			return $dir . '/';
		} else {
			return '';	
		}
	}
	
	// Fungsi untuk mengambil folder utama
	function base_path() {
		return DIR;
	}
	
	// Fungsi untuk mengambil folder App
	function app_path($dir = '') {
		return DIR . 'app/' . parse_dir($dir);
	}
	
	// Fungsi untuk mengambil folder public
	function public_path($dir = '') {
		return DIR . 'public/' . parse_dir($dir);
	}
	
	// Fungsi untuk mengambil folder storage
	function storage_path($dir = '') {
		return DIR . 'storage/' . parse_dir($dir);
	}
	
	// Fungsi untuk mengambil folder views
	function views_path($dir = '') {
		return app_path('Views') . parse_dir($dir);
	}
	
	// Fungsi untuk menamilkan variabel dan menghentikan eksekusi
	function dd($var) {
		var_dump($var);
		exit();
	}
	
	// Fungsi untuk mengambil file configurasi
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
	
	// Fungsi untuk membuat view
	function view($name, $data = array()) {
		return View::make($name, $data);
	}
	
	// Fungsi untuk membuat response
	function response($response = null) {
		return new Response($response);
	}
	
	// Fungsi untuk memeriksa apakah request berbentuk ajax
	function is_ajax() {
		return Request::isAjax();
	}
	
	// Fungsi untuk memerikan apakah variable berbentuk array associative
	function is_assoc(array $arr) {
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	// Fungsi untuk menampilkan debug
	function debug($debug) {
		if (config('app.debug')) {
			echo $debug;
			exit();
		} else {
			return true;
		}
	}

?>