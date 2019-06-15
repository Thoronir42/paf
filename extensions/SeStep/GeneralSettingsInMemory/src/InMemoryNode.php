<?php


namespace SeStep\GeneralSettingsInMemory;


use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Options\INode;

abstract class InMemoryNode implements INode
{
    /** @var array */
    protected $data;

    /** @var InMemoryOptionSection */
    private $parent;
    /** @var string */
    private $name;

    public function __construct(InMemoryOptionSection $parent, string $name, array &$data)
    {
        $this->data = $data;
        $this->parent = $parent;
        $this->name = $name;
    }

    /**
     * Returns fully qualified name. That is in most cases concatenated getDomain() and getName().
     * @return mixed
     */
    public function getFQN(): string
    {
        return DomainLocator::concatFQN($this->name, $this->parent);
    }

    public function getType(): string
    {
        return $this->data['type'];
    }

    public function getCaption(): string
    {
        return $this->data['caption'];
    }

    protected final function getRoot(): InMemoryOptions
    {
        if ($this->parent instanceof InMemoryOptions) {
            return $this->parent;
        }

        return $this->parent->getRoot();
    }
}