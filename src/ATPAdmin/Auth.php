<?php

namespace ATPAdmin;

class Auth
{
	private static $_sm = null;
	private static $_config = null;
	private static $_session = null;

	public static function setServiceManager($sm)
	{
		self::$_sm = $sm;
	}
	
	public static function setConfig($config)
	{
		self::$_config = $config;
	}
	
	private static function _getSession()
	{
		if(is_null(self::$_session))
		{
			self::$_session = new \Zend\Session\Container(self::$_config['session_namespace']);
		}
		
		return self::$_session;
	}
	
	public static function isLoggedIn()
	{
		$session = self::_getSession();		
		return isset($session->user);
	}
	
	public static function authenticatedUser($username, $password)
	{
		$user = new \ATPAdmin\Model\User($username);
		return $user->validatePassword($password) ? $user : null;
	}
	
	public static function login($user)
	{
		$session = self::_getSession();
		$session->user = $user;
	}
	
	public static function currentUser()
	{
		return self::_getSession()->user;
	}
	
	public static function logout($user)
	{
		$session = self::_getSession();
		unset($session->user);
	}
}
