<?php

namespace App\Model\Auth;


use Nette\Security\IAuthorizator;
use Nette\Security\IResource;
use Nette\Security\IRole;
use Nette\Security\Permission;

class Authorisator implements IAuthorizator
{
	/** @var Permission */
	private $acl;

	public function __construct()
	{
		$acl = new Permission();
		

		$this->acl = $acl;
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
		return $this->acl->isAllowed($role, $resource, $privilege);
	}
}
