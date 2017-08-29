<?php

	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;

	/**
	* Manage session data
	*
	* @package Core\Framework\Session
	*/
	class Session {
		/**
		* Indicates if session has been started
		*
		* @var boolean $started
		*/
		public static $started = false;

		/**
		* The prefix that used in the session naming
		*
		* @var boolean $prefix
		*/
		public static $prefix;
		
		/**
		* Check whether the session has been started or not
		*
		* @return boolean 
		*/
		public static function check() {
			if(static::$started === false && session_status() == PHP_SESSION_NONE){
				session_start();

				static::$started = true;
				static::$prefix = config('app.key');
			}

			return true;
		}
		
		/**
		* Get a session by key
		* 
		* @param string $key A string that specified the key of session
		* @param string $default A string that specified default value of return
		* @return string
		*/
		public static function get($key, $default = '') {
			$value = $default;
			if (static::check()) {
				if (isset($_SESSION[static::$prefix . $key])) {
					$value = $_SESSION[static::$prefix . $key];
				}
			}

			return $value;
		}
		
		/**
		* Set a session value by key
		* 
		* @param string $key A string that specified the key of session
		* @param string $value A string that specified the value
		*/
		public static function set($key, $value = '') {
			if (static::check()) {
				$_SESSION[static::$prefix . $key] = $value;
			}
		}
		
		/**
		* Destroying the session
		*/
		public static function destroy() {
			if(static::check()){
				session_unset();
				session_destroy();
			}
		}
		
		/**
		* Regenerate new session ID
		*/
		public static function regenerate() {
			if (static::check()) {
				session_regenerate_id();
			}
		}
		
		/**
		* Delete a session by key
		* 
		* @param string $key A string that specified the key of session
		*/
		public static function forget($key) {
			if (static::check()) {
				unset($_SESSION[$key]);
			}
		}
		
		/**
		* Delete a session by key
		* 
		* @param string $key A string that specified the key of session
		* @param string $value A string that specified the value of flashing data
		*/
		public static function flash($key, $values = '') {
			setcookie("COOKIE_" . static::$prefix . $key, $values, time() + 60, "/");
		}

		/**
		* Set a cookie value by key
		* 
		* @param string $key A string that specified the key of cookie
		* @param string $value A string that specified the value
		*/
		public static function cookie($key, $value = '') {
			setcookie("COOKIE_" . session_key() . "_" . $key, $value, time() + (3600 * 24), "/");
		}
	}

?>