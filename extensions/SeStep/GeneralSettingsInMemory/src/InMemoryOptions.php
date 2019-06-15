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

    public function setValue($value, string $name, string $domain = '')
    {
        $dl = DomainLocator::create($name, $domain);

        $section = $this;
        while ($dl->getDomain()) {
            $domainPart = $dl->shiftDomain();

            if (!isset($section[$domainPart])) {
                $section = $section->addSection($domainPart);

                continue;
            }

            if (!$section[$domainPart] instanceof IOptionSection) {
                throw new SectionNotFoundException(DomainLocator::concatFQN($section->getFQN(), $domainPart));
            }

            $section = $section[$domainPart];
        }

        $section[$dl->getName()] = $value;
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