<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	/**
	* Base of controller class
	*
	* @package Core\Framework\Controller
	*/
	class Controller {
		
		/**
		* Specify database of controller
		*
		* @var Core\Framework\Database $db
		*/
		public $db = null;

		/**
		* Specify response of controller
		*
		* @var Core\Framework\Response $response
		*/
		public $response = null;

		/**
		* Specify request of controller
		*
		* @var Core\Framework\Request $request
		*/
		public $request = null;
		
		/**
		* Create a new controller
		*/
		public function __construct() {
			$this->db = new Database();
			$this->request = new Request();
			$this->response = new Response();
		}	

		/**
		* Check the variable request
		*
		* @return boolean 
		*/
		public function validate($field = array(), $data = array()) {
			$valid = true;
			$data = (array) $data;

			if (is_assoc($field)) {
			
			} else {
				foreach($field as $f) {
					if (empty($data[$f]) || is_null($data[$f])) {
						$valid = false;
					}
				}
			}

			return $valid;
		}
	}

?>