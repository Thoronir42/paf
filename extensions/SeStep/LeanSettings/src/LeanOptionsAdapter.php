<?php declare(strict_types=1);

namespace SeStep\LeanSettings;

use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\OptionNotFoundException;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\GeneralSettings\Options\OptionTypeEnum;
use SeStep\GeneralSettings\SectionNavigator;
use SeStep\LeanSettings\Model\Option;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Model\Section;
use SeStep\LeanSettings\Repository\OptionNodeRepository;

class LeanOptionsAdapter implements IOptionsAdapter
{
    /** @var OptionNodeRepository */
    private $nodeRepository;

    /** @var Section */
    private $rootSection;
    /**
     * @var int
     */
    private $maxSectionItems;

    public function __construct(OptionNodeRepository $nodeRepository, int $maxSectionItems = 99)
    {
        $this->nodeRepository = $nodeRepository;
        $this->maxSectionItems = $maxSectionItems;

        $this->rootSection = $this->nodeRepository->findSection('');
        if (!$this->rootSection) {
            $this->rootSection = $this->nodeRepository->createSection('', 'Root entry for options');
        }
    }

    public function addSection(string $name): ?Section
    {
        return $this->nodeRepository->getSection($name, true);
    }

    public function addValue($value, string $section = '')
    {
        $parent = $this->nodeRepository->getSection($section);
        for ($freeIndex = 0; $freeIndex < $this->maxSectionItems && $parent->hasNode($freeIndex); $freeIndex++) {
        }

        if($freeIndex >= $this->maxSectionItems) {
            return false;
        }

        $this->setValue($value, "$freeIndex");
    }

    public function setValue($value, string $name)
    {
        $entry = $this->nodeRepository->find($name);

        if ($entry) {
            if (!$entry instanceof Option) {
                throw new OptionNotFoundException($name, $entry);
            }

            $entry->value = $value;
            $this->nodeRepository->persist($entry);
        } else {
            $parent = $this->nodeRepository->getSection((new DomainLocator($name))->getDomain(), true);

            $option = new Option();
            $option->type = OptionTypeEnum::infer($value);
            $option->fqn = $name;
            $option->value = $value;
            $option->parentSection = $parent;

            $this->nodeRepository->persist($option);
            $parent->clearOptionsCache();
            $this->rootSection->clearOptionsCache();
        }
    }

    public function removeNode(string $name)
    {
        $dl = new DomainLocator($name);
        /** @var Section $parent */
        $parent = SectionNavigator::getSectionByDomain($this->rootSection, $dl);
        if (!$parent->hasNode($dl->getName())) {
            // todo: notify error?
            return;
        }

        $toDelete = $parent->getNode($dl->getName());
        $this->nodeRepository->delete($toDelete);
        $parent->clearOptionsCache();
    }


    public function getIterator()
    {
        return $this->rootSection->getIterator();
    }

    public function count()
    {
        return $this->rootSection->count();
    }

    public function getFQN(): string
    {
        return $this->rootSection->getFQN();
    }

    public function getType(): string
    {
        return $this->rootSection->getType();
    }

    public function getCaption(): ?string
    {
        return $this->rootSection->getCaption();
    }

    public function hasNode($name): bool
    {
        return $this->rootSection->hasNode($name);
    }


    public function getNode($name): ?OptionNode
    {
        return $this->rootSection->getNode($name);
    }


    /** @return OptionNode[] */
    public function getNodes(): array
    {
        return $this->rootSection->getNodes();
    }

    public function getValue(string $name)
    {
        return $this->rootSection->getValue($name);
    }
}
