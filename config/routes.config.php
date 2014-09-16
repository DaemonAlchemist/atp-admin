<?php

return array(
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
);
