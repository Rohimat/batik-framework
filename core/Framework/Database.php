<?php
	
	/*
	|--------------------------------------------------------------------------
	| Database Class
	|--------------------------------------------------------------------------
	| 
	| Database Class adalah class mengatur semua lalu lintas ke database,
	| Class ini menggunakan PDO sebagai dasar nya
	|
	*/
	
	namespace Core\Framework;	
	use \PDO as PDO;

	class Database extends PDO {
		protected $table = '';
		protected $query = '';
		protected $result = array();
		protected $order = array();
		protected $group = array();
		protected $column = '*';
		protected $limit = '';
		protected $where = array();
		protected $params = array();
		protected $statement = null;

		// Method constructor sekaligus untuk membentuk sebuah koneksi
		public function __construct() {
			try {
				$host = config('database.host');
				$port = config('database.port');
				$user = config('database.username');
				$pass = config('database.password');
				$data = config('database.database');

				parent::__construct('mysql:host='.$host.';port='.$port.';dbname='.$data, $user, $pass);

				$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
			} catch(PDOException $e) {
				echo $e;
				exit();
			}
		}	

		// Method untuk menentukan tabel yang akan dieksekusi
		protected function table($table) {
			$this->table = $table;
			return $this;
		}
		
		// Method untuk membangun sebuah query
		protected function select($query = '', $params = array()) {
			$this->query = $query;
			$this->params = $params;

			return $this;
		}
		
		// Method untuk menentukan kolom yang akan diambil
		protected function column($column) {
			$this->column = $column;
			return $this;
		}
		
		// Method untuk memisahkan kolom yang tidak akan dipakai
		protected function exclude($column) {
			$this->column = array();
			foreach ($this->schema() as $col) {
				if ((is_string($column) && $col->Field != $column) || (is_array($column) && !in_array($col->Field, $column))) {
					$this->column[] = $col->Field;
				}
			} 

			return $this;
		}
		
		// Method untuk membatasi hasil query
		protected function limit($start = 0, $limit = 0) {
			$this->limit .= ' limit ' . $start;

			if ($limit > 0) {
				$this->limit .= ', ' . $limit;
			}

			return $this;
		}

		// Method untuk menentukan kondiri
		protected function where($where) {
			$this->where = $where;
			return $this;
		}
		
		// Method untuk urutan hasil query
		protected function orderBy($order) {
			$this->order = $order;
			return $this;
		}
		
		// Method untuk pengelompokan hasil query
		protected function groupBy($group) {
			$this->group = $group;
			return $this;
		}
		
		// Method untuk menghitung jumlah data
		protected function count() {
			$this->column = 'count(*) as jumlah';
			$this->get(PDO::FETCH_NUM);

			return $this->result[0][0];
		}
		
		// Method untuk mencari data paling kecil
		protected function min($field = '') {
			$this->column = 'min(' . $field . ') as minimal';
			$this->get(PDO::FETCH_NUM);

			return $this->result[0][0];
		}
		
		// Method untuk mencari data paling besar
		protected function max($field = '') {
			$this->column = 'max(' . $field . ') as maximal';
			$this->get(PDO::FETCH_NUM);

			return $this->result[0][0];
		}
		
		// Method untuk mencari rata-rata
		protected function avg($field = '') {
			$this->column = 'avg(' . $field . ') as avgs';
			$this->get(PDO::FETCH_NUM);

			return $this->result[0][0];
		}

		// Method untuk mengambil data pertama dari hasil query
		protected function first() {
			$this->get();
			return $this->result[0];
		}
		
		// Method untuk mengambil dari terakhir dari hasil query
		protected function last() {
			$this->get();
			return $this->result[sizeof($this->result) - 1];
		}
		
		// Method untuk mengubah sebuah query kedalam bentuk result array
		protected function get($fetchType = PDO::FETCH_OBJ) {
			if (empty($this->query)) {
				$field = $this->parseField();
				$where = $this->parseWhere();
				$order = $this->parseOrder();
				$group = $this->parseGroup();

				$this->query = 'select ' . $field . ' from ' . $this->table . $where . $group . $order;
			}

			$this->statement = $this->prepare($this->query . $this->limit);
			$this->bind();
			$this->statement->execute();
			$this->result = $this->statement->fetchAll($fetchType);

			return $this->result;
		}
		
		// Method untuk membentuk group pada query
		protected function parseGroup() {
			$group = '';
			
			// Cek type of group field
			if (is_string($this->group)) {
				$group .= $this->group . ', ';
			} else {
				foreach($this->group as $key){
					$group .= $key . ', ';
				}
			}
			
			// Cek if group exists
			if (!empty($group)) {
				return ' GROUP BY ' . substr($group, 0, -2) . ' ';
			} else {
				return $group;
			}
		}
		
		// Method untuk membentuk order pada query
		protected function parseOrder() {
			$order = '';
			
			// Cek type of order field
			if (is_string($this->order)) {
				$order .= $this->order . ', ';
			} elseif (is_assoc($this->order)) {
				foreach($this->order as $key => $value){
					$order .= $key . ' ' . $value . ', ';
				}
			} else {
				foreach($this->order as $key){
					$order .= $key . ', ';
				}
			}
			
			// Cek if order exists
			if (!empty($order)) {
				return ' ORDER BY ' . substr($order, 0, -2) . ' ';
			} else {
				return $order;
			}
		}
		
		// Method untuk membentuk where pada query
		protected function parseWhere() {
			$where = '';
			
			// Cek type of where field
			if (is_assoc($this->where)) {
				foreach($this->where as $key => $value){
					$where .= $key . '=:wh_' . $key . ' AND ';
					$this->params['wh_' . $key] = $value;
				}
			} else {
				if (sizeof($this->where) == 1) {
					$key = $this->where[0];
					$op = $this->where[1];
					$value = $this->where[2];

					$where .= $key . $op . ':wh_' . $key . ' AND ';				
					$this->params['wh_' . $key] = $value;
				} else {
					for ($i = 0; $i < sizeof($this->where); $i++) {
						$key = $this->where[$i][0];
						$op = $this->where[$i][1];
						$value = $this->where[$i][2];
						
						$where .= $key . $op . ':wh_' . $key . ' AND ';
						$this->params['wh_' . $key] = $value;
					}
				}
			}
			
			// Cek if where exists
			if (!empty($where)) {
				return ' WHERE ' . substr($where, 0, -4);
			} else {
				return $where;
			}
		}
		
		// Method untuk membentuk field dari kolom
		protected function parseField() {
			if (is_string($this->column)) {
				return $this->column;
			} elseif (is_array($this->column)) {
				$column = '';

				foreach ($this->column as $col) {
					$column .= $col . ', ';
				}

				return substr($column, 0, -2);
			}
		}
		
		// Method untuk mencari tau tipe dari parameter
		protected function type($value) {
			if(is_int($value))
				$param = PDO::PARAM_INT;
			elseif(is_bool($value))
				$param = PDO::PARAM_BOOL;
			elseif(is_null($value))
				$param = PDO::PARAM_NULL;
			elseif(is_string($value))
				$param = PDO::PARAM_STR;
			else
				$param = FALSE;
				
			return $param;
		}

		// Method untuk memasukan parameter kedalam query
		protected function bind() {
			if (is_assoc($this->params)) {
				foreach($this->params as $key => $value){
					$value = is_null($value) ? '' : $value;
					$type = $this->type($value);
					if ($type) {
						$this->statement->bindValue(":$key", $value, $type);
					}
				}
			} else {
				for ($i = 1; $i <= sizeof($this->params); $i++) {
					$value = $this->params[$i - 1];
					$value = is_null($value) ? '' : $value;
					$type = $this->type($value);
					if ($type) {
						$this->statement->bindValue($i, $value, $type);
					}
				}
			}
		}
		
		// Method untuk insert
		protected function insert($data = array()) {
			$data = (array) $data;
			$params = array();

			if ($this->column == '*') {
				foreach($this->schema() as $col) {
					$this->column[] = $col->Field;
					$params[$col->Field] = $data[$col->Field];
				}
			} else {
				foreach($this->column as $col) {
					$params[$col] = $data[$col];
				}
			}

			$field = implode(',', $this->column);
			$values = ':'. implode(', :', $this->column);
			$this->query = 'INSERT INTO ' . $this->table . ' (' . $field . ') VALUES (' . $values . ')';
			
			$this->params = $params;
			$this->statement = $this->prepare($this->query);
			$this->bind();
			
			if ($this->statement->execute()) {
				return true;
			} else {
				debug($this->statement->errorCode());
			}
		}
		
		// Method untuk update
		protected function update($data) {
			$data = (array) $data;
			$update = '';

			if ($this->column == '*') {
				foreach($this->schema() as $col) {
					$update .= $col->Field . '=:' . $col->Field . ', ';
					$this->column[] = $col->Field;
					$this->params[$col->Field] = $data[$col->Field];
				}
			} else {
				foreach($this->column as $col) {
					$update .= $col . '=:' . $col . ', ';
					$this->params[$col] = $data[$col];
				}
			}

			$where = $this->parseWhere();
			$order = $this->parseOrder();
			$update = !empty($update) ? rtrim($update, ', ') : '';
			$this->query = 'UPDATE ' . $this->table . ' SET ' . $update . $where . $order . $this->limit;
			
			$this->statement = $this->prepare($this->query);
			$this->bind();
			
			if ($this->statement->execute()) {
				return true;
			} else {
				debug($this->statement->errorCode());
			}
		}
		
		// Method untuk replace
		protected function replace($data) {
			$data = (array) $data;
			$params = array();
			$update = '';

			if ($this->column == '*') {
				foreach($this->schema() as $col) {
					$update .= $col->Field . '=:' . $col->Field . ', ';
					$this->column[] = $col->Field;
					$params[$col->Field] = $data[$col->Field];
				}
			} else {
				foreach($this->column as $col) {
					$update .= $col . '=:' . $col . ', ';
					$params[$col] = $data[$col];
				}
			}

			$field = implode(',', $this->column);
			$values = ':'. implode(', :', $this->column);
			$update = !empty($update) ? rtrim($update, ', ') : '';
			$this->query = 'INSERT INTO ' . $this->table . ' (' . $field . ') VALUES (' . $values . ') ON DUPLICATE KEY UPDATE ' . $update;
			
			$this->params = $params;
			$this->statement = $this->prepare($this->query);
			$this->bind();
			
			if ($this->statement->execute()) {
				return true;
			} else {
				debug($this->statement->errorCode());
			}
		}
		
		// Method untuk delete
		protected function delete() {
			$where = $this->parseWhere();
			$order = $this->parseOrder();
			$this->query = 'DELETE FROM ' . $this->table . $where . $order . $this->limit;
			
			$this->statement = $this->prepare($this->query);
			$this->bind();
			
			if ($this->statement->execute()) {
				return true;
			} else {
				debug($this->statement->errorCode());
			}
		}
		
		// Method untuk menampilkan skema dari tabel
		protected function schema() {
		    $query = $this->prepare("DESC `" . $this->table . "`");
			$query->execute();
			$field = $query->fetchAll(PDO::FETCH_OBJ);

			return $field;
		}

		public static function __callStatic($method, $args) {
			$instance = new Database();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}

?>