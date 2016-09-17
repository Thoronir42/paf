<?php

namespace App\Model\Entity;


use Kdyby\Doctrine\Entities\Attributes\Identifier;

use Doctrine\ORM\Mapping as ORM;
use SeStep\Model\BaseEntity;

/**
 * Class Fursuit
 * @package App\Model\Entity
 *
 * @ORM\Entity
 */
class Fursuit extends BaseEntity
{
    use Identifier;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user")
     */
    protected $user;

    public static function getTypes()
    {
        return [
            'partial' => 'Partial',
            'half-suit' => 'Half-Suit',
            'full-suit' => 'Full-Suit',
        ];
    }
}
