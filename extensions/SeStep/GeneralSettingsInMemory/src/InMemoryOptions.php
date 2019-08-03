<?php declare(strict_types=1);
namespace SeStep\GeneralSettingsInMemory;

use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\IOptions;
use SeStep\GeneralSettings\Options\INode;
use SeStep\GeneralSettings\Options\IOptionSection;
use SeStep\GeneralSettings\Options\IValuePool;

final class InMemoryOptions extends InMemoryOptionSection implements IOptions
{

    private $rootData;

    public function __construct()
    {
        $this->rootData = [];
        parent::__construct($this, '', $this->rootData);
    }


    public function getFQN(): string
    {
        return $this->getName();
    }

    public function getNode($name): INode
    {
        if ($name == INode::DOMAIN_DELIMITER) {
            return $this;
        }

        return parent::getNode($name);
    }


    /** @internal */
    public function &getData()
    {
        return $this->rootData;
    }

    /** @internal */
    public function setData(&$data)
    {
        $this->rootData = $data;
    }

    public function getPool(string $name): ?IValuePool
    {
        // todo: implement
        return null;
    }
}
