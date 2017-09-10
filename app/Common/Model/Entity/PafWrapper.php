<?php

namespace App\Common\Model\Entity;


use App\Common\Model\Traits\Slug;
use App\Common\Model\Traits\SoftDelete;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="paf__wrapper")
 *
 */
class PafWrapper extends BaseEntity
{
    const MAX_LENGTH = 72;

    use Identifier;
    use SoftDelete;

    /**
     * @var string
     * @ORM\Column(type="string", length=72)
     */
    protected $name;

    /**
     * @var Quote
     * @ORM\OneToOne(targetEntity="Quote", inversedBy="wrapper")
     */
    protected $quote;

    /**
     * @var PafCase
     * @ORM\OneToOne(targetEntity="PafCase", inversedBy="wrapper")
     */
    protected $case;

    /**
     * @var Fursuit
     * @ORM\OneToOne(targetEntity="Fursuit", inversedBy="wrapper")
     */
    protected $fursuit;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        if (strlen($name) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException("Name length must be at most " . self::MAX_LENGTH . " characters long");
        }

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getMaxProgress()
    {
        if ($this->fursuit) {
            return 'fursuit';
        }
        if ($this->case) {
            return 'case';
        }
        if ($this->quote) {
            return 'quote';
        }

        return 'something';
    }

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param Quote $quote
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return PafCase
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * @param PafCase $case
     */
    public function setCase($case)
    {
        $this->case = $case;
    }

    /**
     * @return PafCase
     */
    public function getFursuit()
    {
        return $this->fursuit;
    }

    /**
     * @param PafCase $fursuit
     */
    public function setFursuit($fursuit)
    {
        $this->fursuit = $fursuit;
    }
}
