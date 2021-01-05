<?php declare(strict_types=1);

namespace SeStep\GeneralSettings;

use InvalidArgumentException;
use SeStep\GeneralSettings\Model\INode;

class DomainLocator
{
    protected string $fqn;

    protected ?int $firstDelimiter;
    protected ?int $lastDelimiter;

    public function __construct(string $name, $domain = null)
    {
        $this->setFQN(self::concatFQN($name, $domain));
    }

    public function getName(): string
    {
        if ($this->lastDelimiter === null) {
            return $this->fqn;
        }

        return substr($this->fqn, $this->lastDelimiter + 1);
    }

    public function getDomain(): string
    {
        if ($this->lastDelimiter === null) {
            return '';
        }

        return substr($this->fqn, 0, $this->lastDelimiter);
    }

    public function getTopDomain()
    {
        if ($this->firstDelimiter === null) {
            return '';
        }

        return substr($this->fqn, 0, $this->firstDelimiter);
    }

    public function shiftDomain(): string
    {
        if ($this->firstDelimiter === null) {
            return '';
        }

        $domain = substr($this->fqn, 0, $this->firstDelimiter);
        $this->fqn = substr($this->fqn, $this->firstDelimiter + 1);

        if ($this->firstDelimiter === $this->lastDelimiter) {
            $this->firstDelimiter = $this->lastDelimiter = null;
        } else {
            $removedLength = $this->firstDelimiter + 1;
            $this->lastDelimiter -= $removedLength;
            $this->firstDelimiter = $this->makePos(strpos($this->fqn, INode::DOMAIN_DELIMITER));
        }

        return $domain;
    }


    public function pop(): string
    {
        if ($this->lastDelimiter === null) {
            $result = $this->fqn;
            $this->fqn = '';
        } else {
            $result = substr($this->fqn, $this->lastDelimiter + 1);
            $this->fqn = substr($this->fqn, 0, $this->lastDelimiter);

            if ($this->firstDelimiter === $this->lastDelimiter) {
                $this->firstDelimiter = $this->lastDelimiter = null;
            } else {
                $this->lastDelimiter = $this->makePos(strrpos($this->fqn, INode::DOMAIN_DELIMITER));
            }
        }

        return $result;
    }

    private function setFQN(string $fqn)
    {
        $this->fqn = $fqn;

        $this->firstDelimiter = $this->makePos(strpos($fqn, INode::DOMAIN_DELIMITER));
        $this->lastDelimiter = $this->makePos(strrpos($fqn, INode::DOMAIN_DELIMITER));
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
     * @param int|false $pos
     * @return int|null
     */
    private function makePos($pos): ?int
    {
        return is_int($pos) ? $pos : null;
    }


    public function with(string...$parts): DomainLocator
    {
        $partsStr = implode(INode::DOMAIN_DELIMITER, $parts);
        return new DomainLocator($partsStr, $this->getFQN());
    }

    /**
     * @param string $name
     * @param string|INode $domain
     * @return string
     */
    public static function concatFQN($name, $domain = null): string
    {
        if ($domain instanceof INode) {
            $domain = $domain->getFQN();
        } elseif ($domain && !is_string($domain)) {
            if (is_scalar($domain)) {
                $domain = (string)$domain;
            } else {
                throw new InvalidArgumentException('Argument domain expected to be string or instance of ' .
                    INode::class . ', ' . gettype($domain) . ' given');
            }
        }

        $fqn = '';
        if ($domain) {
            $fqn .= $domain;
        }
        if (is_string($name) && strlen($name) > 0 || is_numeric($name)) {
            if ($domain) {
                $fqn .= INode::DOMAIN_DELIMITER;
            }
            $fqn .= $name;
        }

        return $fqn;
    }
}
