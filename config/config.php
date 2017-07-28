<?php

	return [
		'app' => [
			'name'     => 'Batik Framework',
			'key'      => base64_encode('batik-framework-12345'), 
			'debug'    => false,
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
	]

?>