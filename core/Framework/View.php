<?php

	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/
	

	namespace Core\Framework;
	
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
				return $view->render(basename($filename), $data);
			} else {
				return null;
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
		* @return \Twig_Environment
		*/
		public static function init($folder) {
			\Twig_Autoloader::register();

			$loader = new \Twig_Loader_Filesystem($folder);
			$twig = new \Twig_Environment($loader);

			return $twig;
		}
	}

?>