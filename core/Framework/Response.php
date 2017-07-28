<?php
	
	/*
	|--------------------------------------------------------------------------
	| Response Class
	|--------------------------------------------------------------------------
	| 
	| Response Class adalah class untuk mengatur semua bentuk output selain twig,
	| class ini akan menampilkan beberapa jenis output
	|
	*/

	namespace Core\Framework;

	class Response {
		protected $header = array();
		protected $type = '';
		
		// Method constructor
		public function __construct() { }
		
		// Method membuat standar class baru
		protected function create() {
			return new \stdClass();
		}
		
		// Method untuk menentukan type dari konten yang akan dijadikan output
		protected function type($type) {
			$this->type = $type;
			return $this;
		}
		
		// Method untuk menambahkan header kedalam output
		protected function header($header) {
			$this->header = $header;
			return $this;
		}
		
		// Method untuk membuat output baru
		protected function make($response) {
			foreach ($this->header as $k => $v) {
				header($k . ': ' . $v);
			}

			if (!empty($this->type)) {
				header('Content-Type: ' . $this->type);
			}

			echo $response;
		}
		
		// Method untuk menampilkan output dalam bentuk JSON
		protected function json($data) {
			header("Content-type: application/json");
			echo json_encode($response);
		}
		
		// Method untuk mengubah string ke dalam bentuk download
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
		
		// Method untuk mendownload file
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

		public static function __callStatic($method, $args) {
			$instance = new Response();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}