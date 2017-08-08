<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	/**
	* Class for authenticaton
	*
	* @package Core\Framework\Auth
	*/
	class Auth {
		/**
		* Determine if user has authenticated
		* 
		* @return boolean
		*/
		public static function check() {
		
		}

		/**
		* Attempt to authenticate the user
		* 
		* @param mixed $user
		* @return boolean
		*/
		public static function attempt($user = array()) {
			
		}

		/**
		* Attempt to authenticate via remember
		* 
		* @return boolean
		*/
		public static function viaRemember($user = array()) {
			
		}

		/**
		* Get the authenticated user
		* 
		* @return mixed
		*/
		public static function user() {
			
		}
	}

?>