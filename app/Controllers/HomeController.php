<?php

	namespace App\Controllers;

	use Core\Framework\Controller;
	use Core\Framework\View;
	use Core\Framework\DB;

	use App\Models\User;

	class HomeController extends Controller{
		public function index() {
			return View::show('home');
		}
	}

?>