<?php

namespace App\Common\Model\Entity;


use App\Common\Model\Traits\Slug;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
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
    const TYPE_PARTIAL = 'partial';
    const TYPE_HALF_SUIT = 'halfsuit';
    const TYPE_FULL_SUIT = 'fullsuit';

    use Identifier;
    use Slug;

    /**
     * @var PafWrapper
     * @ORM\OneToOne(targetEntity="PafWrapper", mappedBy="fursuit")
     */
    protected $wrapper;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fursuits")
     */
    protected $user;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $issuedOn;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $completedOn;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;





    public static function getTypes()
    {
        return [
            self::TYPE_PARTIAL   => self::TYPE_PARTIAL  ,
            self::TYPE_HALF_SUIT => self::TYPE_HALF_SUIT,
            self::TYPE_FULL_SUIT => self::TYPE_FULL_SUIT,
        ];
    }
}
