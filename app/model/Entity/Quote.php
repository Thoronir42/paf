<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;

/**
 * @ORM\Entity
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
