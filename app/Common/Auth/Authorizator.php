<?php

namespace App\Common\Auth;


use App\Modules\Admin\Presenters\AdminPresenter;
use App\Modules\Admin\Presenters\SettingsPresenter;
use Nette\Security\IAuthorizator;
use Nette\Security\IResource;
use Nette\Security\IRole;
use Nette\Security\Permission;

class Authorizator extends Permission implements IAuthorizator
{
	public function __construct()
	{
		$this->addRole('guest');
        $this->addRole('user');
        $this->addRole('power-user', ['user']);

        $this->addResource(AdminPresenter::class);

		$this->addResource(SettingsPresenter::class, AdminPresenter::class);

		$this->allow('power-user', AdminPresenter::class);
	}

	/**
	 * Performs a role-based authorization.
	 * @param	string|IRole	$role
	 * @param	string|IResource	$resource
	 * @param	string	$privilege
	 * @return bool
	 */
	function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL)
	{
		return parent::isAllowed($role, $resource, $privilege);
	}
}
