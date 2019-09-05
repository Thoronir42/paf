<?php declare(strict_types=1);

namespace SeStep\LeanSettings;

use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\OptionNotFoundException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Exceptions\ValuePoolAlreadyExistsException;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\GeneralSettings\Model\OptionTypeEnum;
use SeStep\GeneralSettings\SectionNavigator;

use SeStep\LeanSettings\Model\Section;
use SeStep\LeanSettings\Repository\OptionNodeRepository;

class LeanOptionsAdapter implements IOptionsAdapter
{
    /** @var OptionNodeRepository */
    private $nodeRepository;

    /** @var Model\Section */
    private $rootSection;
    /**
     * @var int
     */
    private $maxSectionItems;

    public function __construct(OptionNodeRepository $nodeRepository, string $rootName = '', int $maxSectionItems = 99)
    {
        $this->nodeRepository = $nodeRepository;
        $this->maxSectionItems = $maxSectionItems;

        $this->rootSection = $this->findOrCreateSection($rootName, 'Root entry for options');
    }

    public function addSection(string $name): Model\Section
    {
        if ($this->hasNode($name)) {
            throw new ValuePoolAlreadyExistsException("Section $name already exists");
        }

        $fqn = DomainLocator::concatFQN($name, $this->getFQN());
        return $this->nodeRepository->createSection($fqn);
    }

    public function addValue($value, string $section = '')
    {
        $parent = $this->getNode($section);
        if (!$parent instanceof Section) {
            throw new SectionNotFoundException($section, $parent);
        }

        for ($freeIndex = 0; $freeIndex < $this->maxSectionItems && $parent->hasNode($freeIndex); $freeIndex++) {
        }

        if ($freeIndex >= $this->maxSectionItems) {
            return false;
        }

        $this->setValue($value, "$freeIndex");
        return true;
    }

    public function setValue($value, string $name)
    {
        $entry = $this->nodeRepository->find($name);

        if ($entry) {
            if (!$entry instanceof Model\Option) {
                throw new OptionNotFoundException($name, $entry);
            }

            $entry->value = $value;
            $this->nodeRepository->persist($entry);
        } else {
            $parent = $this->nodeRepository->getSection((new DomainLocator($name))->getDomain(), true);

            $option = new Model\Option();
            $option->type = OptionTypeEnum::infer($value);
            $option->fqn = $name;
            $option->value = $value;
            $option->parentSection = $parent;

            $this->nodeRepository->persist($option);
            $parent->clearOptionsCache();
            $this->rootSection->clearOptionsCache();
        }
    }

    /**
     * @param Model\Section $section
     * @param array $values
     */
    public function setMultipleValues($section, array $values)
    {
        if (is_string($section)) {
            $sec = $this->nodeRepository->getSection($section);
            if (!$sec) {
                throw new SectionNotFoundException($sec);
            }
        } else {
            if (!$section instanceof Model\Section) {
                throw new \InvalidArgumentException("Parameter section expected to be fqn or instane of "
                    . Model\Section::class);
            }
            $sec = $section;
        }
        $childNodes = $sec->getNodes();
        if (!empty($childNodes)) {
            $this->nodeRepository->deleteMany($childNodes);
        }
        $sec->clearOptionsCache();

        $parentFqn = $sec->getFQN();
        foreach ($values as $key => $value) {
            $fqn = DomainLocator::concatFQN($key, $parentFqn);
            $this->setValue($value, $fqn);
        }
    }

    public function removeNode(string $name)
    {
        $dl = new DomainLocator($name);
        if ($dl->getTopDomain() === $this->getFQN()) {
            $dl->shiftDomain();
        }

        /** @var Model\Section $parent */
        $parent = SectionNavigator::getSectionByDomain($this->rootSection, $dl);
        if (!$parent->hasNode($dl->getName())) {
            // todo: notify error?
            return;
        }

        $this->nodeRepository->deleteNode(DomainLocator::concatFQN($dl->getFQN(), $parent), true);
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


    public function getNode($name): ?Model\OptionNode
    {
        $dl = new DomainLocator($name);

        /** @var Model\Section $parent */
        $parent = SectionNavigator::getSectionByDomain($this->rootSection, $dl);

        $optionNode = $dl->getName() ? $parent->getNode($dl->getName()) : $parent;

        return $optionNode;
    }


    /** @return Model\OptionNode[] */
    public function getNodes(): array
    {
        return $this->rootSection->getNodes();
    }

    public function getValue(string $name)
    {
        return $this->rootSection->getValue($name);
    }

    private function findOrCreateSection(string $fqn, string $caption)
    {
        $section = $this->nodeRepository->findSection($fqn);
        if (!$section) {
            $section = $this->nodeRepository->createSection($fqn, $caption);
        }

        return $section;
    }
}
