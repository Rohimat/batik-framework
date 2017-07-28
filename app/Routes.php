<?php
	
	use Core\Framework\Route;
	use Core\Framework\Request;
	use Core\Framework\View;
	use Core\Framework\Response;
	
	Route::any('/', 'HomeController@index');

	Route::prefix('admin')->middleware(['age'])->group(function() {
		Route::get('home', 'HomeController');
	});

	Route::run();

?>