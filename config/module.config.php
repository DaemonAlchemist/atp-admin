<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
	'block_filters' => array(
		'adminUser' => 'ATPAdmin\View\Filter\AdminUser',
	),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/admin[/[:controller[/[:action[/[:model[/[:id]]]]]]]]',
                    'defaults' => array(
                        'controller'    => 'default-admin',
                        'action'        => 'index',
						'model'			=> null,
						'id'			=> null,
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'default-admin' => 'ATPAdmin\Controller\IndexController'
        ),
    ),
	'view_helpers' => array(
		'invokables' => array(
		)
	),
);
