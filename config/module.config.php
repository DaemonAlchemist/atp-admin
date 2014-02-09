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
			'admin_users' => array(
				'displayName' => 'Username',
				'class' => 'ATPAdmin\Model\User',
				'category' => 'Admin',
				'displayColumns' => array('Username', 'Email'),
				'defaultOrder' => 'username ASC',
				'fields' => array(
					'Username' => array(
						'type' => 'Text',
						'label' => 'Username',
					),
					'Email' => array(
						'type' => 'Text',
						'label' => 'Email',
					),
					'Password' => array(
						'type' => 'Password',
						'label' => 'Password',
					),
					'IsActive' => array(
						'type' => 'Boolean',
						'label' => 'Is Active',
					),
				),
			),
		),
	),
	'asset_manager' => array(
		'resolver_configs' => array(
			'paths' => array(
				__DIR__ . '/../public',
			),
		),
	),
	'block_filters' => array(
		'adminUser' => 'ATPAdmin\View\Filter\AdminUser',
	),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/admin[/:action[/:model[/:id]]]',
                    'defaults' => array(
                        'controller'    => 'ATPAdmin\Controller\IndexController',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ATPAdmin\Controller\IndexController' => 'ATPAdmin\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
	),
	'view_helpers' => array(
		'invokables' => array(
		)
	),
);
