<?php

namespace App\Common\Model\Entity;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="paf__quote")
 *
 *
 * @property User $user
 * @property string $status
 * @property string $url
 */
class Quote extends BaseEntity
{
    const STATUS_NEW = 'new';
    const STATUS_SELECTED = 'selected';
    const STATUS_DENIED = 'denied';

    use Identifier;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('new', 'selected', 'wip', 'denied')")
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $url;

    public function __construct()
    {

    }


}
