<?php

namespace SeStep\GeneralSettings;

use InvalidArgumentException;
use SeStep\GeneralSettings\Options\INode;

class DomainLocator
{
    /** @var int */
    protected $depth;
    /** @var string */
    protected $fqn;
    /** @var string */
    protected $name;
    /** @var string */
    protected $domain;

    public function __construct(string $name, $domain = '')
    {
        $this->fqn = $fqn = self::concatFQN($name, $domain);

        $nameParts = explode(INode::DOMAIN_DELIMITER, $fqn);
        $this->depth = count($nameParts);

        $this->name = array_pop($nameParts);
        $this->domain = implode(INode::DOMAIN_DELIMITER, $nameParts);
    }

    /**
     * @param $name
     * @param string|INode $domain
     * @return DomainLocator
     * @deprecated - use constructor
     */
    public static function create(string $name, $domain = ''): self
    {
        return new DomainLocator($name, $domain);
    }

    /**
     * @param string $name
     * @param string|INode $domain
     * @return string
     */
    public static function concatFQN(string $name, $domain = ''): string
    {
        if ($domain && !is_string($domain)) {
            if (!($domain instanceof INode)) {
                throw new InvalidArgumentException('Argument domain expected to be string or instance of ' .
                    INode::class . ', ' . gettype($domain) . ' given');
            }
            $domain = $domain = $domain->getFQN();
        }

        return $domain ? ($domain . INode::DOMAIN_DELIMITER . $name) : $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }


    public function shiftDomain(): string
    {
        if (!$this->domain) {
            return '';
        }

        $domain = $this->domain;
        $separatorPos = strpos($this->domain, INode::DOMAIN_DELIMITER);
        if (!$separatorPos) {
            $separatorPos = strlen($this->domain);
        }

        $this->domain = substr($this->domain, $separatorPos + 1);

        return substr($domain, 0, $separatorPos);
    }

    public function getFQN(): string
    {
        return $this->fqn;
    }

    public function __toString()
    {
        return $this->getFQN();
    }
}
