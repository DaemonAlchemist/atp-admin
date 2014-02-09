<?php

namespace ATPAdmin\Model;

class User extends \ATP\ActiveRecord
{
	private static $_passwordSalt = "";

	private $_passwordHash = null;

	protected function createDefinition()
	{
		$this->hasData('Username', 'Password', 'Email', 'IsActive')
			->isIdentifiedBy("Username")
			->tableNamespace("admin");
	}
	
	public static function hasUsers()
	{
		$user = new self();
		$results = $user->getAdapter()->query("SELECT count(*) users FROM admin_users")->execute();

		foreach($results as $row)
		{
			return $row['users'] > 0;
		}
	}
	
	public static function setPasswordSalt($salt)
	{
		self::$_passwordSalt = $salt;
	}
	
	public function copyFrom($obj)
	{
		parent::copyFrom($obj);
		
		$this->_passwordHash = $obj->_passwordHash;
	}
	
	public function hashPassword($password)
	{
		return md5(static::$_passwordSalt . $password);
	}
	
	public function getHashedPassword()
	{
		return $this->_passwordHash;
	}
	
	public function validatePassword($password)
	{
		return $this->hashPassword($password) == $this->getHashedPassword();
	}
	
	public function displayName()
	{
		return $this->username;
	}
	
	public function postLoadPassword($password)
	{
		$this->_passwordHash = $password;
		return null;
	}
	
	public function filterPassword($password)
	{
		$this->_passwordHash = empty($password)
			? $this->_passwordHash
			: self::hashPassword($password);
			
		return null;
	}
	
	public function getPassword()
	{
		return $this->_passwordHash;
	}}
User::init();