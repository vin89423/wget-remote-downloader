<?php

$START_UP = array(
	// For core to calculate url path. [NECESSARY]
	'url' => array(
		'domain' => '{domain}',
		'root' => '{url-root}',
	),
	// For basic member authorization.
	'session' => array(
		'token' => 'APP_TOKEN',
		'encrypt' => '{encrypt-key}',
	),
	// Application routing. [NECESSARY]
	'application' => array(
		'activity' => array(
			'main' => array(
				'launch' => 'MainApp',
				'storage' => '{file-storage-path}',
				'languageSource' => 'test_db',
				'language' => array('en', 'zt', 'zs'),
			),
			'cms' => array(
				'launch' => 'CmsApp',
				'storage' => '{file-storage-path}',
				'languageSource' => 'ini',
				'language' => array('en', 'zt', 'zs'),
				'inherit' => array(
					array('modules/cms_module_1.0/', 'CmsAppBase')
				)
			),
		),
	),
	// Database connection.
	'database' => array(
		'test_db' => array(
			'host' => '{sql-host}',
			'user' => '{user-name}',
			'password' => '{user-password}',
			'db_name' => '{database-name}',
		)
	),
);
