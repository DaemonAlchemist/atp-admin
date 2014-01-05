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
		
		//Set password salt for user
		\ATPAdmin\Model\User::setPasswordSalt($config['admin']['auth']['password_salt']);
	}	
}
