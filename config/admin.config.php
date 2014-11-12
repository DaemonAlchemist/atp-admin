<?php

return array(
	'admin' => array(
		'auth' => array(
			'password_salt' => '',
			'session_namespace' => 'atp_admin',
		),
		'models' => array(
			'atpadmin_user' => array(
				'displayName' => 'Admin User',
				'class' => 'ATPAdmin\Model\User',
				'category' => 'Admin',
				'displayColumns' => array('Username', 'Email'),
				'defaultOrder' => 'username ASC',
			),
		),
		'reports' => array(),
		'parameters' => array(
			'admin-per-page-options' => array(
				'displayName' => 'Per Page Options',
				'group' => 'Admin',
				'subGroup' => 'General',
				'type' => 'Text',
				'default' => '20,50,100',
				'options' => array(
				),
			),
		),
	),
);
