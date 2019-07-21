<?php


namespace SeStep\GeneralSettings;


use Nette\InvalidStateException;
use SeStep\GeneralSettings\Options\IOptionSection;
use SeStep\GeneralSettings\Options\IOptionSectionWritable;

class SectionNavigator
{
    private function __construct()
    {
    }


    public static function getSectionByDomain(IOptionSection $section, DomainLocator &$domainLocator, bool $create = false): IOptionSection
    {
        while ($domainLocator->getDomain()) {
            $subSectionName = $domainLocator->shiftDomain();
            if (!$section->hasNode($subSectionName)) {
                if (!$create) {
                    throw new InvalidStateException("Could not find section '" . DomainLocator::concatFQN($subSectionName,
                            $section->getFQN()) . "''");
                }
                if(!$section instanceof IOptionSectionWritable) {
                    throw new InvalidStateException("Tried to create subSection in section that is not '" . IOptionSectionWritable::class . "'");
                }

                $subSection = $section->addSection($subSectionName);
            } else {
                $subSection = $section->getNode($subSectionName);
                if (!$subSection instanceof IOptionSection) {
                    throw new InvalidStateException("'" . $subSection->getFQN() . "' is not a Section node");
                }
            }

            $section = $subSection;
        }

        return $section;
    }
}