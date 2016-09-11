<?php

namespace App\Model\Entity;


use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Thoronir42\Model\BaseEntity;

class Fursuit extends BaseEntity
{
	use Identifier;

	public static function getTypes()
	{
		return [
			'partial' => 'Partial',
			'half-suit' => 'Half-Suit',
			'full-suit' => 'Full-Suit',
		];
	}

	protected $type;
}
