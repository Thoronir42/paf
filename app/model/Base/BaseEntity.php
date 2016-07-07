<?php

namespace App\Model\Entity;

use Kdyby\Doctrine;
use Kdyby\Doctrine\Entities\MagicAccessors;

abstract class BaseEntity
{
	use MagicAccessors;

	public abstract function getId();

	public function toArray()
	{
		$array = [];
		foreach ($this as $field => $value) {
			if ($value instanceof BaseEntity) {
				$value = $value->getId();
			}
			$array[$field] = $value;

		}
		return $array;
	}

}
