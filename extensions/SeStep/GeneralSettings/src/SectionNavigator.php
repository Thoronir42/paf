<?php declare(strict_types=1);


namespace SeStep\GeneralSettings;

use Nette\InvalidStateException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Model\IOptionSection;
use SeStep\GeneralSettings\Model\IOptionSectionWritable;

class SectionNavigator
{
    const NONE = 0x00;
    /** If section is missing, attempt to create it */
    const CREATE_IF_MISSING = 0x01;
    /** Does not throw section not found exception */
    const NULL_IF_MISSING = 0x02;

    private function __construct()
    {
        // HIC SUNT DRACONES
    }


    public static function getSectionByDomain(
        IOptionSection $section,
        DomainLocator &$domainLocator,
        int $options = self::NONE
    ): IOptionSection {
        $createIfMissing = (bool)($options & self::CREATE_IF_MISSING);
        $nullIfMissing = (bool)($options & self::NULL_IF_MISSING);

        while ($domainLocator->getDomain()) {
            $subSectionName = $domainLocator->shiftDomain();
            $section = self::getSubsection($section, $subSectionName, $createIfMissing, $nullIfMissing);
        }

        return $section;
    }

    private static function getSubsection(
        IOptionSection $section,
        string $subSectionName,
        bool $createIfMissing = false,
        bool $nullIfMissing = false
    ) {
        if (!$section->hasNode($subSectionName)) {
            if (!($createIfMissing)) {
                if ($nullIfMissing) {
                    return null;
                }

                $fqn = DomainLocator::concatFQN($subSectionName, $section->getFQN());
                throw new SectionNotFoundException($fqn);
            }
            if (!$section instanceof IOptionSectionWritable) {
                $message = "Tried to create subSection in section that is not '" . IOptionSectionWritable::class . "'";
                throw new InvalidStateException($message);
            }

            $subSection = $section->addSection($subSectionName);
        } else {
            $subSection = $section->getNode($subSectionName);
            if (!$subSection instanceof IOptionSection) {
                throw new InvalidStateException("'" . $subSection->getFQN() . "' is not a Section node");
            }
        }

        return $subSection;
    }
}
