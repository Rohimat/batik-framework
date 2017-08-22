<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	/**
	* Manage application response
	*
	* @package Core\Framework\Response
	*/
	class Response {

		/**
		* Specify header for response
		*
		* @var mixed $header
		*/
		protected $header = array();

		/**
		* Specify content type of response
		*
		* @var string $type
		*/
		protected $type = '';
		
		/**
		* Create a new routing
		*/
		public function __construct() { }
		
		/**
		* Create a new standard class for response
		*
		* @return stdClass
		*/
		protected function create() {
			return new \stdClass();
		}
		
		/**
		* Set content type of response data
		* 
		* @param string $type A string that specified the content type 
		* @return Core\Framework\Response
		*/
		protected function type($type) {
			$this->type = $type;
			return $this;
		}
		
		/**
		* Set header of response data
		* 
		* @param string $type A string that specified the header data
		* @return Core\Framework\Response
		*/
		protected function header($header) {
			$this->header = $header;
			return $this;
		}
		
		/**
		* Show the response
		* 
		* @param string $response A string that specified the response content
		* @return string
		*/
		protected function make($response) {
			foreach ($this->header as $k => $v) {
				header($k . ': ' . $v);
			}

			if (!empty($this->type)) {
				header('Content-Type: ' . $this->type);
			}

			echo $response;
		}
		
		/**
		* Show the json data
		* 
		* @param mixed $data A mixed that specified the json
		* @return string | json
		*/
		protected function json($data) {
			header("Content-type: application/json");
			echo json_encode($data);
		}
		
		/**
		* Create download from response content
		* 
		* @param string $string A string that specified the reponse content
		* @param string $filename A string that specified the filename
		* @return string | download attachment
		*/
		protected function download($string, $filename) {
			set_time_limit(0);

			header("Content-Disposition: attachment; filename=" . urlencode($filename));
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");             
			header("Content-Length: " . filesize($filename));

			echo $string;
		}
		
		/**
		* Create download from filename
		* 
		* @param string $filename A string that specified the filename
		* @return string | download attachment
		*/
		protected function file($filename) {
			set_time_limit(0);

			header("Content-Disposition: attachment; filename=" . urlencode($filename));
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");             
			header("Content-Length: " . filesize($filename));

			flush();
			
			$fp = fopen($filename, "r"); 
			while (!feof($fp)) {
				echo fread($fp, 65536); 
				flush();
			}  

			fclose($fp); 
		}

		/**
		* Override calling method
		*/
		public static function __callStatic($method, $args) {
			$instance = new Response();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}