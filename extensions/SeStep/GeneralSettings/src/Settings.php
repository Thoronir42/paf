<?php

namespace SeStep\GeneralSettings;


use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;

class Settings implements \IteratorAggregate
{
    /** @var IOptions */
    public $options;

    public function __construct(IOptions $options)
    {
        $this->options = $options;
    }

    public function findNode($fullName, string $filterType = null)
    {
        $node = $this->options->getNode($fullName);
        if($node) {
            if($filterType && !$node instanceof $filterType) {
                $node = null;
            }
        }

        return $node;
    }

    /**
     * @param mixed $fullName
     * @return Options\IOptionSection
     * @throws SectionNotFoundException
     */
    public function getSection($fullName)
    {
        $node = $this->findNode($fullName);

        if(!$node instanceof Options\IOptionSection) {
            throw new SectionNotFoundException($fullName, $node);
        }

        return $node;
    }

    /**
     * @param string $fullName
     * @return Options\IOption
     * @throws NodeNotFoundException
     */
    public function getOption(string $fullName)
    {
        $node = $this->findNode($fullName);

        if(!$node instanceof Options\IOption) {
            throw new NodeNotFoundException($fullName, $node);
        }

        return $node;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getValue($name)
    {
        return $this->options->getValue($name);
    }

    public function setValue($name, $value)
    {
        $this->options->setValue($value, $name);
    }

    public function getIterator()
    {
        return $this->options->getIterator();
    }
}
