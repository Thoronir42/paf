<?php


namespace SeStep\GeneralSettingsInMemory;


use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\IOptions;
use SeStep\GeneralSettings\Options\IOptionSection;

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
        return '';
    }


    /** @internal */
    function &getData()
    {
        return $this->rootData;
    }

    /** @internal */
    function setData(&$data)
    {
        $this->rootData = $data;
    }
}