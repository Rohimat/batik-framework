<?php

	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/
	

	namespace Core\Framework;
	use duncan3dc\Laravel\Blade;
	use duncan3dc\Laravel\BladeInstance;
	/**
	* Display data using twig component
	*
	* @package Core\Framework\View
	*/
	class View {

		/**
		* Create view from blade file
		*
		* @param string $name A string that specified the name of view
		* @param mixed[] $data Array contains data to be displayed
		* @return string | null
		*/
		public static function make($name, $data = array()) {
			if (static::exists($name)) {
				$filename = static::filename($name);

				$view = static::init(dirname($filename));
				$name = substr(basename($filename), 0, -10);

				return $view->render($name, $data);
			} else {
				return 'View not found';
			}
		}
		
		/**
		* Create and display the view
		*
		* @param string $name A string that specified the name of view
		* @param mixed[] $data Array contains data to be displayed
		* @return string | null
		*/
		public static function show($name, $data = array()) {
			echo static::make($name, $data);
		}
		
		
		/**
		* Checking the availability of views
		*
		* @param string $name A string that specified the name of view
		* @return boolean 
		*/
		public static function exists($name) {
			if (file_exists(static::filename($name))) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		* Create a path of filename
		* 
		* @param string $name A string that specified the name of view
		* @return string
		*/
		public static function filename($name) {
			return views_path() . str_replace('.', '/', $name) . '.blade.php';
		}
		
		/**
		* Initializing twig component 
		*
		* @param string $name A string that specified the name of view
		* @return BladeInstance
		*/
		public static function init($folder) {
			$blade = new BladeInstance($folder, storage_path('cache'));
			return $blade;
		}
	}

?>