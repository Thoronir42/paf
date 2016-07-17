<?php

namespace App\Model\Settings;

use Doctrine\ORM\Mapping as ORM;
use Nette\InvalidArgumentException;

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
		if(!is_string($int)){
			throw new InvalidArgumentException('String option must not receive a ' . gettype($int) .' value');
		}
		$this->string = $int;
	}
}
