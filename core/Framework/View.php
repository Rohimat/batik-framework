<?php

	/*
	|--------------------------------------------------------------------------
	| Facades View Class
	|--------------------------------------------------------------------------
	| 
	| Facades View class adalah class untuk mengelola komponen Twig Templeating
	|
	*/

	namespace Core\Framework;
	
	class View {
		// Method untuk membuat view twig
		public static function make($name, $data = array()) {
			if (static::exists($name)) {
				$filename = static::filename($name);

				$view = static::init(dirname($filename));	
				return $view->render(basename($filename), $data);
			} else {
				return null;
			}
		}
		
		// Method untuk membuat view twig sekaligus menampilkannya
		public static function show($name, $data = array()) {
			echo static::make($name, $data);
		}
		
		// Method untuk memeriksa apakah file view tersedia atau tidak
		public static function exists($name) {
			if (file_exists(static::filename($name))) {
				return true;
			} else {
				return false;
			}
		}
		
		// Method untuk membentuk nama file dari view
		public static function filename($name) {
			return views_path() . str_replace('.', '/', $name) . '.blade.php';
		}
		
		// Method untuk menginisialiasi komponen Twig
		public static function init($folder) {
			\Twig_Autoloader::register();

			$loader = new \Twig_Loader_Filesystem($folder);
			$twig = new \Twig_Environment($loader);

			return $twig;
		}
	}

?>