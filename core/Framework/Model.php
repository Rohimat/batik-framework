<?php
	
	/*
	|--------------------------------------------------------------------------
	| Controller Class
	|--------------------------------------------------------------------------
	| 
	| Controller Class adalah class utama untuk membangun sebuah controller,
	| Class ini akan di extend di semua Controller
	|
	*/

	namespace Core\Framework;

	class Model extends Database {
		protected $table = '';
		protected $fillable = array();
		
		// Method untuk menginisialisasi db
		public function __construct() {
			parent::__construct();
		}	

		protected function find($find) {
			$data = array();

			if (is_string($find)) {
				$data = $this->where([$this->primaryField => $find])->first();
			} else {
				$data = $this->where($find)->first();
			}

			foreach ($data as $key => $value) {
				$this->{$key} = $value;
			}
		}

		protected function primaryField() {
			$field = '';
			
			foreach ($this->schema() as $schema) {
				if (strtoupper($schema->Key) == 'PRI') {
					$field = $schema->Field;		
				}
			}

			return $field;
		}

		protected function save() {
			$data = array();
			foreach ($this->schema() as $schema) {
				$data[$schema->Field] = isset($this->{$schema->Field}) ? $this->{$schema->Field} : '';	
			}

			$this->insert($data);
		}

		public static function __callStatic($method, $args) {
			$instance = new Model();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}

?>