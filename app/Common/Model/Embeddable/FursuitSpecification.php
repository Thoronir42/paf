<?php

namespace App\Common\Model\Embeddable;

use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Exceptions\EnumValueException;
use App\Common\Model\Traits\EntitySerialization;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable();
 */
class FursuitSpecification
{
    use EntitySerialization;

    /**
     * @var string
     * @ORM\Column(type="string", length=96)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=24)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=2000)
     */
    protected $characterDescription;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @return string */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        if (!in_array($type, Fursuit::getTypes())) {
            throw new EnumValueException($type, Fursuit::getTypes());
        }

        $this->type = $type;

        return $this;
    }

    /** @return string */
    public function getCharacterDescription()
    {
        return $this->characterDescription;
    }

    /**
     * @param string $characterDescription
     * @return self
     */
    public function setCharacterDescription($characterDescription)
    {
        $this->characterDescription = $characterDescription;

        return $this;
    }




}
