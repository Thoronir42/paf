<?php declare(strict_types=1);

namespace PAF\Common\Security;


use Nette\Security\IAuthorizator;
use Nette\Security\IResource;
use Nette\Security\IRole;
use Nette\Security\Permission;

class Authorizator extends Permission implements IAuthorizator
{
    const CREATE = 'create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';

    public function __construct()
    {
        $this->addRole('guest');
        $this->addRole('user');
        $this->addRole('power-user', ['user']);

        $this->addResource('admin-section');

        $this->addResource("admin-settings", 'admin-section');

        $this->allow('power-user', 'admin-section');
    }

    /**
     * Performs a role-based authorization.
     * @param    string|IRole     $role
     * @param    string|IResource $resource
     * @param    string           $privilege
     * @return bool
     */
    function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL): bool
    {
        return parent::isAllowed($role, $resource, $privilege);
    }
}
