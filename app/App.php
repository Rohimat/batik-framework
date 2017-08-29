<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/
	

	// Require Helpers
	require DIR . 'core/Helpers.php';
	
	// Cek If Development Or Production
	if (config('app.debug')) {
		error_reporting(E_ALL);
	} else {
		error_reporting(0);
	}
	
	// Requires Routes Data
	require DIR . 'app/Routes.php';
	
?>