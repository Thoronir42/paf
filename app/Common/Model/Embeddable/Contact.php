<?php

namespace App\Common\Model\Embeddable;

use App\Common\Model\Traits\EntitySerialization;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Contact
{
    use EntitySerialization;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $telegram;

    /**
     * @var string
     * @ORM\Column(type="string", length=420, nullable=true)
     */
    protected $email;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /** @return string */
    public function getTelegram()
    {
        return $this->telegram;
    }

    /**
     * @param string $telegram
     * @return self
     */
    public function setTelegram($telegram)
    {
        $this->telegram = $telegram;

        return $this;
    }

    /** @return string */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }


}
