<?php

namespace ATPAdmin\View\Filter;

class AdminUser extends \ATPCore\View\Filter\AbstractBlockFilter
{
	protected function _replace($block)
	{
		return \ATPAdmin\Auth::currentUser()->username;
	}
}
