<?php

namespace App\Common\Model\Embeddable;

use App\Common\Model\Traits\EntitySerialization;
use Doctrine\ORM\Mapping as ORM;
use Nette\InvalidArgumentException;

/**
 * @ORM\Embeddable();
 */
class FursuitProgress
{
    const NOT_INTERESTED = -1;

    use EntitySerialization;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $head = self::NOT_INTERESTED;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $body = self::NOT_INTERESTED;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $armSleeves = self::NOT_INTERESTED;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $paws = self::NOT_INTERESTED;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $legSleeves = self::NOT_INTERESTED;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $hindPaws = self::NOT_INTERESTED;

    public function getPercentage() {

    }

    /** @return int */
    public function getHead()
    {
        return $this->head;
    }

    /** @param int $value */
    public function setHead($value)
    {
        $this->validatePercentage($value);
        $this->head = $value;
    }

    /** @return int */
    public function getBody()
    {
        return $this->body;
    }

    /** @param int $value */
    public function setBody($value)
    {
        $this->validatePercentage($value);
        $this->body = $value;
    }

    /** @return int */
    public function getArmSleeves()
    {
        return $this->armSleeves;
    }

    /** @param int $value */
    public function setArmSleeves($value)
    {
        $this->validatePercentage($value);
        $this->armSleeves = $value;
    }

    /** @return int */
    public function getPaws()
    {
        return $this->paws;
    }

    /** @param int $value */
    public function setPaws($value)
    {
        $this->validatePercentage($value);
        $this->paws = $value;
    }

    /** @return int */
    public function getLegSleeves()
    {
        return $this->legSleeves;
    }

    /** @param int $legSleeves */
    public function setLegSleeves($legSleeves)
    {
        $this->validatePercentage($legSleeves);
        $this->legSleeves = $legSleeves;
    }

    /** @return int */
    public function getHindPaws()
    {
        return $this->hindPaws;
    }

    /** @param int $value */
    public function setHindPaws($value)
    {
        $this->validatePercentage($value);
        $this->hindPaws = $value;
    }

    private function validatePercentage($value) {
        if($value === -1) {
            return true;
        }
        if(0 < $value || $value > 100) {
            throw new InvalidArgumentException("Percentage value $value is not valid");
        }
    }
}
