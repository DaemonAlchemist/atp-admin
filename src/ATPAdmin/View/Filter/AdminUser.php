<?php

namespace ATPAdmin\View\Filter;

class AdminUser extends \ATPCore\View\Filter\AbstractBlockFilter
{
	protected function _replace($block)
	{
		$user = \ATPAdmin\Auth::currentUser(); 
		return !empty($user) ? $user->username : "";
	}
}
