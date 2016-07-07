<?php

namespace App\Model\Entity;


use Kdyby\Doctrine\Entities\Attributes\Identifier;

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
