<?php
	
	
	define('APPLICATION_START', microtime());

	/*
	|--------------------------------------------------------------------------
	| Register The Composer Auto Loader
	|--------------------------------------------------------------------------
	|
	| Composer provides a convenient, automatically generated class loader
	| for our application. We just need to utilize it! We'll require it
	| into the script here so we do not have to manually load any of
	| our application's PHP classes. It just feels great to relax.
	|
	*/

	require DIR . 'vendor/autoload.php';
	
	/*
	|--------------------------------------------------------------------------
	| Register The Framework Auto Loader
	|--------------------------------------------------------------------------
	| 
	| Framework akan otomatis di require berdasarkan nama class dan nama file
	| begitu juga dengan controller, model, middleware dan view
	|
	*/

	spl_autoload_register(function ($classname) {
		$arDir = explode('\\', $classname);
		$basePath = DIR . strtolower($arDir[0]);
		$filePath = $basePath . str_replace('\\', '/', substr($classname, strlen($arDir[0]), strlen($classname))) . '.php';
		
		if (file_exists($filePath)) {
			require $filePath;
		}
	});

?>