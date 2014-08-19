<?php

namespace ATPAdmin;

class Module extends \ATP\Module
{
	protected $_moduleName = "ATPAdmin";
	protected $_moduleBaseDir = __DIR__;
	
    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
		$config = $sm->get('Config');
		
		//Add service manager and config to auth class
		\ATPAdmin\Auth::setServiceManager($sm);
		\ATPAdmin\Auth::setConfig($config['admin']['auth']);
	}
	
	protected function getInstallDbQueries()
	{
		return array(
			"CREATE TABLE `atpadmin_users` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`username` char(64) COLLATE utf8_unicode_ci NOT NULL,
				`password` char(255) COLLATE utf8_unicode_ci NOT NULL,
				`email` char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`is_active` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id`),
				UNIQUE KEY `username_UNIQUE` (`username`),
				UNIQUE KEY `email_UNIQUE` (`email`)
			) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
		);
	}
}
