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
		
		// Method untuk mencari data
		protected function find($find) {
			$data = array();

			if (is_string($find) || is_int($find)) {
				$data = $this->where([$this->primaryField() => $find])->first();
			} else {
				$data = $this->where($find)->first();
			}

			foreach ($data as $key => $value) {
				$this->{$key} = $value;
			}

			return $this;
		}
		
		// Method untuk mengambil kolom primary
		protected function primaryField() {
			$field = '';
			
			foreach ($this->schema() as $schema) {
				if (strtoupper($schema->Key) == 'PRI') {
					$field = $schema->Field;		
				}
			}

			return $field;
		}
		
		// Method untuk menyimpan model
		protected function save() {
			$data = array();
			foreach ($this->schema() as $schema) {
				$data[$schema->Field] = isset($this->{$schema->Field}) ? $this->{$schema->Field} : '';	
			}

			return $this->insert($data);
		}

		public static function __callStatic($method, $args) {
			$class = get_called_class();
			$instance = new $class();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}

?>