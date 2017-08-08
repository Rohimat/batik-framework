<?php
	
	/**
	* @author Rohimat Nuryana <rohimat@gmail.com>
	* @copyright 2017 BangunTeknologi.com
	*/

	use Core\Console\Create;
	use Core\Console\Upgrade;

	require DIR . 'core/Autoload.php';
	require DIR . 'core/Helpers.php';

	$syntax = $argv[1];
	switch($syntax) {
		case 'create:controller':
			Create::controller();
		break;
		case 'create:view':
			Create::view();
		break;
		case 'create:model':
			Create::model();
		break;
		case 'create:middleware':
			Create::middleware();
		break;
		case 'create:helper':
			Create::helper();
		break;
		case 'upgrade':
			recurse_copy(DIR . '../framework/core', DIR . 'core');
		break;
		case 'version': default:
			echo 'Batik Framework version ' . VERSION;
		break;
	}

?>