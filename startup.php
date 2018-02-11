<?php

$START_UP = array(
	// For core to calculate url path. [NECESSARY]
	'url' => array(
		'domain' => '{domain}',
		'root' => '{url-root}',
	),
	// For basic member authorization. [NECESSARY]
	'session' => array(
		'token' => 'RD_TOKEN',
		'encrypt' => '{encrypt-key}',
	),
	// Application routing. [NECESSARY]
	'application' => array(
		'activity' => array(
			'main' => array(
				'launch' => 'MainApp',
				'storage' => '{file-storage-path}',
				'languageSource' => 'ini',
				'language' => array('en', 'zt'),
			)
		),
	)
);
