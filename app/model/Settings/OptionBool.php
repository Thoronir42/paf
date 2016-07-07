<?php

namespace App\Model\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OptionBool extends AOption
{
	/** @ORM\Column(type="boolean")  */
	protected $bool;

	/**
	 * @return int
	 */
	public function getValue()
	{
		return $this->bool;
	}

	/**
	 * @param int $int
	 */
	public function setValue($int)
	{
		$this->bool = $int;
	}
}
