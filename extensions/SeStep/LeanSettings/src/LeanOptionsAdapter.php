<?php declare(strict_types=1);

namespace SeStep\LeanSettings;

use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\OptionNotFoundException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Exceptions\ValuePoolAlreadyExistsException;
use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\GeneralSettings\IValuePoolsAdapter;
use SeStep\GeneralSettings\Model as GeneralModel;
use SeStep\GeneralSettings\Model\OptionTypeEnum;
use SeStep\GeneralSettings\SectionNavigator;

use SeStep\LeanSettings\Model\SectionValuePool;
use SeStep\LeanSettings\Repository\OptionNodeRepository;

class LeanOptionsAdapter implements IOptionsAdapter, IValuePoolsAdapter
{
    const POOLS_PREFIX = 'valuePools';

    /** @var OptionNodeRepository */
    private $nodeRepository;

    /** @var Model\Section */
    private $rootSection;

    /** @var Model\Section */
    private $poolsSection;
    /**
     * @var int
     */
    private $maxSectionItems;

    public function __construct(OptionNodeRepository $nodeRepository, string $rootName = '', int $maxSectionItems = 99)
    {
        if ($rootName === self::POOLS_PREFIX) {
            throw new \InvalidArgumentException("Parameter rootName must not be " . self::POOLS_PREFIX);
        }

        $this->nodeRepository = $nodeRepository;
        $this->maxSectionItems = $maxSectionItems;

        $this->rootSection = $this->findOrCreateSection($rootName, 'Root entry for options');
        $poolFqn = $rootName ? DomainLocator::concatFQN($rootName, self::POOLS_PREFIX) : self::POOLS_PREFIX;
        $this->poolsSection = $this->findOrCreateSection($poolFqn, 'Root entry of value pools');
    }

    public function addSection(string $name): ?Model\Section
    {
        return $this->nodeRepository->getSection($name, true);
    }

    public function addValue($value, string $section = '')
    {
        $parent = $this->nodeRepository->getSection($section);
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
            $this->setValue($value, DomainLocator::concatFQN($key, $parentFqn));
        }
    }

    public function removeNode(string $name)
    {
        $dl = new DomainLocator($name);
        /** @var Model\Section $parent */
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


    public function getNode($name): ?Model\OptionNode
    {
        $dl = new DomainLocator($name);

        /** @var Model\Section $parent */
        $parent = SectionNavigator::getSectionByDomain($this->rootSection, $dl);

        $optionNode = $parent->getNode($dl->getName());

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

    /**
     * Retrieves value pool by its name
     *
     * @param string $name
     * @return GeneralModel\IValuePool|null
     */
    public function getPool(string $name)
    {
        if (!$this->poolsSection->hasNode($name)) {
            return null;
        }

        $node = $this->poolsSection->getNode($name);
        if (!$node instanceof Model\Section) {
            throw new SectionNotFoundException(DomainLocator::concatFQN($name, $this->poolsSection->getFQN()), $node);
        }

        return new Model\SectionValuePool($node);
    }

    /** @inheritDoc */
    public function createPool(string $name, array $values): GeneralModel\IValuePool
    {
        if ($this->poolsSection->hasNode($name)) {
            throw new ValuePoolAlreadyExistsException("Value pool $name already exists");
        }

        $fqn = DomainLocator::concatFQN($name, $this->poolsSection->getFQN());
        $section = $this->addSection($fqn);

        $this->setMultipleValues($section, $values);
        $section->clearOptionsCache();
        $this->poolsSection->clearOptionsCache();

        return new SectionValuePool($section);
    }

    /** @inheritDoc */
    public function updateValues(GeneralModel\IValuePool $pool, array $values)
    {
        if (!$pool instanceof Model\SectionValuePool) {
            throw new \InvalidArgumentException("This adapter can only set values of "
                . Model\SectionValuePool::class);
        }

        $this->setMultipleValues($pool->getSection(), $values);

        return true;
    }

    /** @inheritDoc */
    public function deletePool($pool): bool
    {
        if (is_string($pool)) {
            $poolSection = $this->poolsSection->getNode($pool);
        } elseif ($pool instanceof Model\SectionValuePool) {
            $poolSection = $pool->getSection();
        } else {
            $type = is_object($pool) ? get_class($pool) : gettype($pool);
            throw new \InvalidArgumentException("String or instance of " . Model\SectionValuePool::class
                . " expected. Got " . $type);
        }

        $this->nodeRepository->deleteMany($poolSection->getNodes());
        $this->nodeRepository->delete($poolSection);

        $this->poolsSection->clearOptionsCache();

        return true;
    }

    /** @inheritDoc */
    public function setOptionsPool(GeneralModel\IOption $option, ?GeneralModel\IValuePool $pool)
    {
        if (!$option instanceof Model\Option) {
            throw new \InvalidArgumentException("Parameter option expected to be instance of "
                . Model\Option::class);
        }
        if (!$pool instanceof Model\SectionValuePool) {
            throw new \InvalidArgumentException("Parameter pool expected to be instance of "
                . Model\SectionValuePool::class);
        }

        $option->setValuePool($pool);
        return true;
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
