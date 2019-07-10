<?php

namespace SeStep\GeneralSettings;


class Settings implements \IteratorAggregate
{
    /** @var IOptions */
    public $options;

    public function __construct(IOptions $options)
    {
        $this->options = $options;
    }

    public function findAll(): LazySectionVisitor
    {
        return new LazySectionVisitor($this->options);
    }

    public function getOption(string $fullName): Options\IOption
    {
        return $this->options->getOption($fullName);
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