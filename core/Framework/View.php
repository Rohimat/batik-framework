<?php

	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/
	

	namespace Core\Framework;

	use Core\Framework\Request;
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

				return $view->render($name, (array)$data);
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
			echo static::make($name, (array)$data);
		}

		/**
		* Create and display javascript file
		*
		* @param string $name A string that specified the name of view
		* @param mixed[] $data Array contains data to be displayed
		* @return string | null
		*/
		public static function javascript($name, $data = array()) {
			$filename = views_path('javascript') . str_replace('.', '/', $name) . '.js';
			if (file_exists($filename)) {
				$script = file_get_contents($filename);
				if (Request::isAjax()) {
					return '<script>' . $script . '</script>';
				} else {
					return View::make('javascript', array("script" => $script));
				}
			} else {
				return View::make('errors.404');
			}
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