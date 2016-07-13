<?php

namespace App\Model\Settings;


use App\Model\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * @property    int $id
 * @property    string $handle
 * @property 	string $title
 *
 * @ORM\Entity
 * @ORM\Table(name="option")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn("option_type", columnDefinition="ENUM('string', 'bool', 'int')")
 * @ORM\DiscriminatorMap({"string" = "OptionString", "bool" = "OptionBool", "int" = "OptionInt"})
 */
abstract class AOption extends BaseEntity
{
	const TYPE_STRING = 'string';
	const TYPE_BOOL = 'bool';
	const TYPE_INT = 'int';

	use Identifier;

	/** @ORM\Column(type="string") */
	protected $handle;

	/** @ORM\Column(type="string") */
	protected $title;
}
