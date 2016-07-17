<?php

namespace App\Model\Settings;

use Doctrine\ORM\Mapping as ORM;
use Nette\InvalidArgumentException;

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
		if(!is_integer($int)){
			throw new InvalidArgumentException('Int option must not receive a ' . gettype($int) .' value');
		}
		$this->int = $int;
	}


}
