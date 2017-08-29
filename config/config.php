<?php

	return [
		'app' => [
			'name'     => 'Batik Framework',
			'key'      => base64_encode('batik-framework'), 
			'debug'    => false,
			'csrf'     => false,
			'themes'   => '',
			'locale'   => 'id'
		],

		'database' => [
			'host'     => 'localhost',
			'port'     => '3306',
			'username' => 'imat',
			'password' => 'imat', 
			'database' => 'framework'
		], 

		'session' => [
			'driver'   => 'default',
			'lifetime' => 'forever',
			'path'     => 'default'
		],

		// For encryption must 32 digit key
		'encryption' => [
			'keyA'     => '@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@',
			'keyB'	   => '@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@',
		]
	]

?>