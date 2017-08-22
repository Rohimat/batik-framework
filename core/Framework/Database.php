<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/
	
	namespace Core\Framework;	
	use \PDO as PDO;
	
	/**
	* Manage the database using PDO extension
	*
	* @package Core\Framework\Database
	*/
	class Database extends PDO {

		/**
		* Specify the table name of database
		*
		* @var string $table
		*/
		protected $table = '';

		/**
		* Specify the query to run
		*
		* @var string $query
		*/
		protected $query = '';

		/**
		* All of the data fethced by query
		*
		* @var array result
		*/
		protected $result = array();

		/**
		* Specify the order of query
		*
		* @var string | mixed $order
		*/
		protected $order = array();

		/**
		* Specify the group of query
		*
		* @var string | mixed $group
		*/
		protected $group = array();

		/**
		* Specify the column 
		*
		* @var string | mixed $column
		*/
		protected $column = '*';

		/**
		* Limit the query
		*
		* @var string $limit
		*/
		protected $limit = '';

		/**
		* Where clause in query
		*
		* @var mixed $where
		*/
		protected $where = array();

		/**
		* Parameter to be binding in query
		*
		* @var mixed $params
		*/
		protected $params = array();

		/**
		* Current query statement
		*
		* @var statement $statement
		*/
		protected $statement = null;

		/**
		* Initializing database connection
		*/
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

		/**
		* Set the table to be processed
		* 
		* @param string $table A string that specified the table name 
		* @return Core\Framework\Database
		*/
		protected function table($table) {
			$this->table = $table;
			return $this;
		}
		
		/**
		* Set the query to be processed
		* 
		* @param string $query A string that specified the query
		* @param mixed $params Parameter to be binding
		* @return Core\Framework\Database
		*/
		protected function select($query = '', $params = array()) {
			$this->query = $query;
			$this->params = $params;

			return $this;
		}
		
		/**
		* Set the column name
		* 
		* @param string | mixed $column
		* @return Core\Framework\Database
		*/
		protected function column($column) {
			$this->column = $column;
			return $this;
		}
		
		/**
		* Exclude the column
		* 
		* @param string | mixed $column
		* @return Core\Framework\Database
		*/
		protected function exclude($column) {
			$this->column = array();
			foreach ($this->schema() as $col) {
				if ((is_string($column) && $col->Field != $column) || (is_array($column) && !in_array($col->Field, $column))) {
					$this->column[] = $col->Field;
				}
			} 

			return $this;
		}
		
		/**
		* Set the result limit of query
		* 
		* @param int $start
		* @param int $limit
		* @return Core\Framework\Database
		*/
		protected function limit($start = 0, $limit = 0) {
			$this->limit .= ' limit ' . $start;

			if ($limit > 0) {
				$this->limit .= ', ' . $limit;
			}

			return $this;
		}

		/**
		* Set where clause
		* 
		* @param string | mixed $where
		* @return Core\Framework\Database
		*/
		protected function where($where, $params = null) {
			if ($params != null) {
				$this->where = array($where => $params);
			} else {
				$this->where = $where;
			}
			return $this;
		}
		
		/**
		* Set the query order
		* 
		* @param string | mixed $order
		* @return Core\Framework\Database
		*/
		protected function orderBy($order) {
			$this->order = $order;
			return $this;
		}
		
		/**
		* Set the query grouping
		* 
		* @param string | mixed $group
		* @return Core\Framework\Database
		*/
		protected function groupBy($group) {
			$this->group = $group;
			return $this;
		}

		protected function reset() {
			$this->query = '';
			$this->limit = '';
			$this->column = '*';
		}
		
		/**
		* Return the count of data
		* 
		* @return int
		*/
		protected function count() {
			$this->reset();
			$this->column = 'count(*) as jumlah';
			$this->get(PDO::FETCH_NUM);
			$this->reset();

			return $this->result ? $this->result[0][0] : 0;
		}
		
		/**
		* Return the minimum of data by fieldname
		* 
		* @param string $field
		* @return int | string
		*/
		protected function min($field = '') {
			$this->reset();
			$this->column = 'min(' . $field . ') as minimal';
			$this->get(PDO::FETCH_NUM);
			$this->reset();

			return $this->result ? $this->result[0][0] : 0;
		}
		
		/**
		* Return the maximum of data by fieldname
		* 
		* @param string $field
		* @return int | string
		*/
		protected function max($field = '') {
			$this->reset();
			$this->column = 'max(' . $field . ') as maximal';
			$this->get(PDO::FETCH_NUM);
			$this->reset();

			return $this->result ? $this->result[0][0] : 0;
		}
		
		/**
		* Return the average of data by fieldname
		* 
		* @param string $field
		* @return int | string
		*/
		protected function avg($field = '') {
			$this->reset();
			$this->column = 'avg(' . $field . ') as avgs';
			$this->get(PDO::FETCH_NUM);
			$this->reset();

			return $this->result ? $this->result[0][0] : 0;
		}

		/**
		* Get the first result data
		* 
		* @return mixed
		*/
		protected function first() {
			$this->get();

			if (sizeof($this->result)) {
				return $this->result[0];
			} else {
				return new \stdClass();
			}
		}
		
		/**
		* Get the last result data
		* 
		* @return mixed
		*/
		protected function last() {
			$this->get();

			if (sizeof($this->result)) {
				return $this->result[sizeof($this->result) - 1];
			} else {
				return new \stdClass();
			}
		}

		protected function prepareQuery() {
			if (empty($this->query)) {
				$field = $this->parseField();
				$where = $this->parseWhere();
				$order = $this->parseOrder();
				$group = $this->parseGroup();

				$this->query = 'select ' . $field . ' from ' . $this->table . $where . $group . $order;
			}

			$this->statement = $this->prepare($this->query . $this->limit);
			$this->bind();
		}
		
		/**
		* Return the count of result
		* 
		* @return int
		*/
		protected function resultCount() {
			$this->prepareQuery();
			$this->statement->execute();

			return $this->statement->rowCount();
		}

		/**
		* Set the result from current query
		* 
		* @param PDO::FETCH_TYPE $fetchType
		* @return array 
		*/
		protected function get($fetchType = PDO::FETCH_OBJ) {
			$this->prepareQuery();
			$this->statement->execute();
			$this->result = $this->statement->fetchAll($fetchType);

			return $this->result;
		}
		
		/**
		* Parsing group into query syntax
		* 
		* @return string
		*/
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
		
		/**
		* Parsing order into query syntax
		* 
		* @return string
		*/
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
		
		/**
		* Parsing where clause into query syntax
		* 
		* @return string
		*/
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
		
		/**
		* Parsing fieldname into query syntax
		* 
		* @return string
		*/
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
		
		/**
		* Get the type of binding values
		* 
		* @param int | bool | null | string $value
		* @return PDO::PARAM_TYPE
		*/
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

		/**
		* Bind all parameter into PDO
		* 
		* @return Core\Framework\Database
		*/
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
		
		/**
		* Inserting record into database
		* 
		* @param mixed $data A data to be saved into database
		* @return boolean
		*/
		protected function insert($data = array()) {
			$data = (array) $data;
			$params = array();

			if ($this->column == '*') {
				$this->column = array();
				foreach($this->schema() as $col) {
					$this->column[] = $col->Field;
					$params[$col->Field] = isset($data[$col->Field]) ? $data[$col->Field] : '';
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
		
		/**
		* Updating record into database
		* 
		* @param mixed $data A data to be saved into database
		* @return boolean
		*/
		protected function update($data) {
			$data = (array) $data;
			$update = '';

			if ($this->column == '*') {
				$this->column = array();
				foreach($this->schema() as $col) {
					if (isset($data[$col->Field])) {
						$update .= $col->Field . '=:' . $col->Field . ', ';
						$this->column[] = $col->Field;
						$this->params[$col->Field] = $data[$col->Field];
					}
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
		
		/**
		* Replacing record into database
		* 
		* @param mixed $data A data to be saved into database
		* @return boolean
		*/
		protected function replace($data) {
			$data = (array) $data;
			$params = array();
			$update = '';

			if ($this->column == '*') {
				$this->column = array();
				foreach($this->schema() as $col) {
					$update .= $col->Field . '=:' . $col->Field . ', ';
					$this->column[] = $col->Field;
					$params[$col->Field] = isset($data[$col->Field]) ? $data[$col->Field] : '';
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
		
		/**
		* Deleting record
		* 
		* @return boolean
		*/
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
		
		/**
		* Get the table schema
		* 
		* @return array
		*/
		protected function schema() {
		    $query = $this->prepare("DESC `" . $this->table . "`");
			$query->execute();
			$field = $query->fetchAll(PDO::FETCH_OBJ);

			return $field;
		}
		
		/**
		* Override calling method
		*/
		public static function __callStatic($method, $args) {
			$instance = new Database();
			return $instance->$method(...$args);
		}

		public function __call($method, $args) {
			return $this->$method(...$args);
		}
	}

?>