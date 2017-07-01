<?php

namespace App\Common\Model\Entity;


use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Fursuit
 * @package App\Model\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="paf__fursuit")
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
