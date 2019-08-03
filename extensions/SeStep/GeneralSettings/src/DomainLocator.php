<?php declare(strict_types=1);
namespace SeStep\GeneralSettings;

use InvalidArgumentException;
use SeStep\GeneralSettings\Options\INode;

class DomainLocator
{
    /** @var string */
    protected $fqn;

    /** @var int */
    protected $firstDelimiter;
    /** @var int */
    protected $lastDelimiter;

    public function __construct(string $name, $domain = null)
    {
        $this->setFQN(self::concatFQN($name, $domain));
    }

    public function getName(): string
    {
        if ($this->lastDelimiter === false) {
            return $this->fqn;
        }

        return substr($this->fqn, $this->lastDelimiter + 1);
    }

    public function getDomain(): string
    {
        return substr($this->fqn, 0, $this->lastDelimiter);
    }

    public function shiftDomain(): string
    {
        if ($this->firstDelimiter === false) {
            return '';
        }

        $domain = substr($this->fqn, 0, $this->firstDelimiter);
        $this->fqn = substr($this->fqn, $this->firstDelimiter + 1);

        if ($this->firstDelimiter === $this->lastDelimiter) {
            $this->firstDelimiter = $this->lastDelimiter = false;
        } else {
            $removedLength = $this->firstDelimiter + 1;
            $this->lastDelimiter -= $removedLength;
            $this->firstDelimiter = strpos($this->fqn, INode::DOMAIN_DELIMITER);
        }

        return $domain;
    }


    public function pop(): string
    {
        if ($this->lastDelimiter === false) {
            $result = $this->fqn;
            $this->fqn = '';
        } else {
            $result = substr($this->fqn, $this->lastDelimiter + 1);
            $this->fqn = substr($this->fqn, 0, $this->lastDelimiter);

            if ($this->firstDelimiter === $this->lastDelimiter) {
                $this->firstDelimiter = $this->lastDelimiter = false;
            } else {
                $this->lastDelimiter = strrpos($this->fqn, INode::DOMAIN_DELIMITER);
            }
        }

        return $result;
    }

    private function setFQN(string $fqn)
    {
        $this->fqn = $fqn;

        $this->firstDelimiter = strpos($fqn, INode::DOMAIN_DELIMITER);
        $this->lastDelimiter = strrpos($fqn, INode::DOMAIN_DELIMITER);
    }

    public function getFQN(): string
    {
        return $this->fqn;
    }

    public function __toString()
    {
        return $this->getFQN();
    }

    /**
     * @param string $name
     * @param string|INode $domain
     * @return string
     */
    public static function concatFQN($name, $domain = null): string
    {
        if ($domain && !is_string($domain)) {
            if (($domain instanceof INode)) {
                $domain = $domain->getFQN();
            } elseif (is_scalar($domain)) {
                $domain = (string)$domain;
            } else {
                throw new InvalidArgumentException('Argument domain expected to be string or instance of ' .
                    INode::class . ', ' . gettype($domain) . ' given');
            }
        }

        return !is_null($domain) ? ($domain . INode::DOMAIN_DELIMITER . $name) : $name;
    }
}
