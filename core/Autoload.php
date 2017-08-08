<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/
	
	define('APPLICATION_START', microtime());
	define('VERSION', '1.0');

	/**
	* Register all class from composer
	*/

	require DIR . 'vendor/autoload.php';
	
	/**
	* Auto register the class
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