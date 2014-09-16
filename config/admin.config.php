<?php

return array(
	'admin' => array(
		'auth' => array(
			'password_salt' => '',
			'session_namespace' => 'atp_admin',
		),
		'models' => array(
			'atpadmin_users' => array(
				'displayName' => 'Admin User',
				'class' => 'ATPAdmin\Model\User',
				'category' => 'Admin',
				'displayColumns' => array('Username', 'Email'),
				'defaultOrder' => 'username ASC',
			),
		),
		'reports' => array(),
	),
);
