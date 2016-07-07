<?php

namespace App\Model\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OptionString extends AOption
{
	/** @ORM\Column(type="string", length=512)  */
	protected $string;

	/**
	 * @return int
	 */
	public function getValue()
	{
		return $this->string;
	}

	/**
	 * @param int $int
	 */
	public function setValue($int)
	{
		$this->string = $int;
	}
}
