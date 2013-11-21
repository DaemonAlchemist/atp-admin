<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
	'asset_manager' => array(
		'resolver_configs' => array(
			'paths' => array(
				__DIR__ . '/../public',
			),
		),
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
