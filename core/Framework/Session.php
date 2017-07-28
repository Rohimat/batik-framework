<?php

	/*
	|--------------------------------------------------------------------------
	| Facades Session Class
	|--------------------------------------------------------------------------
	| 
	| Facades Session class adalah class untuk mempermudah dalam pengelolaan Session
	|
	*/

	namespace Core\Framework;

	class Session {
		public static $started = false;
		public static $prefix;
		
		// Method untuk memeriksa apakan session sudah di mulai atau belum
		public static function check() {
			if(static::$started == false){
				session_start();

				static::$started = true;
				static::$prefix = config('app.key');
			}

			return true;
		}
		
		// Method untuk mengambil value dari session
		public static function get($key, $default = '') {
			$value = $default;
			if (static::check()) {
				if (isset($_SESSION[static::$prefix . $key])) {
					$value = $_SESSION[static::$prefix . $key];
				}
			}

			return $value;
		}
		
		// Method untuk mengeset value dari session
		public static function set($key, $value = '') {
			if (static::check()) {
				$_SESSION[static::$prefix . $key] = $value;
			}
		}
		
		// Method untuk menghancurkan session
		public static function destroy() {
			if(static::$started == true){
				session_unset();
				session_destroy();
			}
		}
		
		// Method untuk menggenerate ulang session ID
		public static function regenerate() {
			if (static::check()) {
				session_regenerate_id();
			}
		}
		
		// Method untuk menghapus value dari session
		public static function forget($key) {
			if (static::check()) {
				unset($_SESSION[$key]);
			}
		}
		
		// Method untuk membuat flash notification
		public static function flash($key, $values = '') {
			setcookie("COOKIE_" . static::$prefix . $key, $values, time() + 60, "/");
		}
	}

?>