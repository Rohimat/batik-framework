<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	use Core\Framework\Session;
	use Core\Framework\DB;
	/**
	* Class for authenticaton
	*
	* @package Core\Framework\Auth
	*/
	class Auth {
		/**
		* Return of error login
		*
		* @var mixed $errors
		*/
		public static $errors = null;

		/**
		* Determine if user has authenticated
		* 
		* @return boolean
		*/
		public static function check() {
			return Session::get("LOGIN");
		}

		/**
		* Attempt to authenticate the user
		* 
		* @param mixed $user
		* @return boolean
		*/
		public static function attempt($user = array()) {
			$found = false;
			$data = null;

			foreach ($user as $key => $value) {
				if ($key != 'password') {
					$data = DB::table('app_users')->where([$key => $value])->first();
					
					if (isset($data->password) && !empty($data->password)) {
						$found = true;
						break;
					}
				}
			}

			if ($found) {
				if ($data->active == 'Y') {
					if (decrypt($data->password) == $user['password']) {
						unset($data->password);

						Session::set("UNIQUE_ID", base64_encode(uniqid() . rand(11111, 99999)));
						Session::set("LOGIN", true);
						Session::set("USER", $data);
						Session::cookie("GROUP", $data->group_id);

						DB::table('app_users')->where('id', $data->id)->update(['last_login' => date('Y-m-d H:i:s')]);

						return true;
					} else {
						static::$errors['code'] = '903';
						static::$errors['message'] = 'Password tidak sesuai';	

						return false;
					}
				} else {
					static::$errors['code'] = '902';
					static::$errors['message'] = 'User tidak aktif';

					return false;
				}
			} else {
				static::$errors['code'] = '901';
				static::$errors['message'] = 'User tidak ditemukan';

				return false;
			}
		}

		/**
		* Get the authenticated errors
		* 
		* @return mixed
		*/
		public static function errors() {
			$errors = new \stdClass();
	
			if (static::$errors != null) {
				$errors->code = static::$errors["code"];		
				$errors->message = static::$errors["message"];		
			} else {
				$errors->code = 0;
				$errors->message = 'No Errors Found';
			}

			return $errors;
		}

		/**
		* Get the authenticated user
		* 
		* @return mixed
		*/
		public static function user() {
			return Session::get("USER");
		}

		/**
		* Logout current session
		*/
		public static function logout() {
			Session::destroy();
		}
	}

?>