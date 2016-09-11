<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
Use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use Thoronir42\Model\BaseEntity;

/**
 * @property    string $username
 * @property    string $password
 * @property	DateTime $registered
 * @property    DateTime $lastActivity
 * @property	ArrayCollection $roles
 *
 * @ORM\Entity
 */
class User extends BaseEntity
{
	use Identifier;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	protected $username;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 * */
	protected $password;

	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $registered;

	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $lastActivity;

	/**
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 */
	protected $active = true;

	public function __construct()
	{

	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = Passwords::hash($password);
	}

	/**
	 * @return DateTime
	 */
	public function getLastActivity()
	{
		return $this->lastActivity;
	}

	/**
	 * @param DateTime $lastActivity
	 */
	public function setLastActivity($lastActivity)
	{
		$this->lastActivity = $lastActivity;
	}

	/**
	 * @return DateTime
	 */
	public function getRegistered()
	{
		return $this->registered;
	}

	/**
	 * @param DateTime $registered
	 */
	public function setRegistered($registered)
	{
		$this->registered = $registered;
	}

	/**
	 * @return boolean
	 */
	public function isActive()
	{
		return $this->active;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}

}
