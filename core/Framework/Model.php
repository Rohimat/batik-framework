<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Framework;
	
	/**
	* Base of model class
	*
	* @package Core\Framework\Model
	*/
	class Model extends Database {
		
		/**
		* Specify the model table name
		*
		* @var string $table
		*/
		protected $table = '';

		/**
		* Specify the field  to be fillable
		*
		* @var mixed $fillable
		*/
		protected $fillable = array();
		
		/**
		* Create new model
		*/
		public function __construct() {
			parent::__construct();
		}	
		
		/**
		* Finding data using primary key or specified field
		* 
		* @param string | int | mixed $find
		* @return mixed
		*/
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
		
		/**
		* Finding the primary key of table
		* 
		* @return string
		*/
		protected function primaryField() {
			$field = '';
			
			foreach ($this->schema() as $schema) {
				if (strtoupper($schema->Key) == 'PRI') {
					$field = $schema->Field;		
				}
			}

			return $field;
		}
		
		/**
		* Saving model data
		* 
		* @return boolean
		*/
		protected function save() {
			$data = array();
			foreach ($this->schema() as $schema) {
				if (in_array($schema->Field, $this->fillable) || sizeof($fillable) <= 0) { 
					$data[$schema->Field] = isset($this->{$schema->Field}) ? $this->{$schema->Field} : '';	
					$this->column[] = $schema->Field;
				}
			}

			return $this->insert($data);
		}
		
		/**
		* Override calling method
		*/
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