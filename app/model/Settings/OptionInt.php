<?php

namespace App\Model\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property	int		$value
 *
 * @ORM\Entity
 */
class OptionInt extends AOption
{
	/** @ORM\Column(type="integer")  */
	protected $int;

	/**
	 * @return int
	 */
	public function getValue()
	{
		return $this->int;
	}

	/**
	 * @param int $int
	 */
	public function setValue($int)
	{
		$this->int = $int;
	}


}
