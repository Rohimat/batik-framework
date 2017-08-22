<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	namespace Core\Console;

	class Create {
		/**
		* Create a new controller
		*/
		public static function controller() {
			static::render('Controllers');
		}
		
		/**
		* Create a new helper
		*/
		public static function helper() {
			static::render('Helpers');
		}
		
		/**
		* Create a new middleware
		*/
		public static function middleware() {
			static::render('Middleware');
		}
		
		/**
		* Create a new model
		*/
		public static function model() {
			static::render('Models');
		}
		
		/**
		* Create a new view
		*/
		public static function view() {
			static::render('Views');
		}
		
		/**
		* Render created component
		* @param string $templates
		* @return string
		*/
		public static function render($templates) {
			global $argv;
			$filename = $argv[2];

			if ($templates != 'Views') {	
				$folder = '';
				$namespace = '';
				$path = DIR . "app/{$templates}";
				$arr = explode('\\', $filename);
				$classname = $arr[sizeof($arr) - 1];
			
				if (sizeof($arr) > 1) {
					$folder = substr($filename, 0, strlen($filename) - strlen($classname) - 1);
					$namespace = '\\' . $folder;
					$path = $path . "/{$folder}";
				}

				$destination = "{$path}/{$classname}.php";

				if (!is_dir($path)) {
					mkdir( $path, 0777, true );
					chmod( $path, 0777);
				}

				$content = file_get_contents(DIR . "core/Console/Scripts/{$templates}.php");
				$content = str_replace('{NAMESPACE}', $namespace, $content);
				$content = str_replace('{CLASSNAME}', $classname, $content);

				file_put_contents($destination, $content);
			} else {
				$arr = explode('.', $filename);
				$file = $arr[sizeof($arr) - 1];
				$folder = substr($filename, 0, strlen($filename) - strlen($file) - 1);

				$path = DIR . "app/{$templates}" . (sizeof($arr) > 1 ? "/{$folder}" : "");
				$destination = "{$path}/{$file}.blade.php";

				if (!is_dir($path)) {
					mkdir( $path, 0777, true );
					chmod( $path, 0777);
				}

				copy(DIR . "core/Console/Scripts/{$templates}.php", $destination);
			}

			echo $templates . ' successfuly created..';
		}
	}

?>