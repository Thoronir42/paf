<?php declare(strict_types=1);

namespace SeStep\LeanSettings;

use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Model as GeneralModel;
use SeStep\GeneralSettings\IValuePoolsAdapter;
use SeStep\LeanSettings\Model\SectionValuePool;
use SeStep\LeanSettings\Repository\OptionNodeRepository;

class LeanValuePoolsAdapter implements IValuePoolsAdapter
{
    private LeanOptionsAdapter $optionAdapter;

    public function __construct(OptionNodeRepository $nodeRepository, string $rootName = '')
    {
        $this->optionAdapter = new LeanOptionsAdapter($nodeRepository, "pools" . ($rootName ? ".$rootName" : ''));
    }

    /**
     * Retrieves value pool by its name
     *
     * @param string $name
     * @return GeneralModel\IValuePool|null
     */
    public function getPool(string $name)
    {
        $node = $this->optionAdapter->getNode($name);
        if (!$node) {
            return null;
        }
        
        if (!$node instanceof Model\Section) {
            throw new SectionNotFoundException(DomainLocator::concatFQN($name, $this->optionAdapter->getFQN()), $node);
        }

        return new Model\SectionValuePool($node);
    }

    /** @inheritDoc */
    public function createPool(string $name, array $values): GeneralModel\IValuePool
    {
        $section = $this->optionAdapter->addSection($name);

        $this->optionAdapter->setMultipleValues($section, $values);
        $section->clearOptionsCache();

        return new SectionValuePool($section);
    }

    /** @inheritDoc */
    public function updateValues(GeneralModel\IValuePool $pool, array $values)
    {
        if (!$pool instanceof Model\SectionValuePool) {
            throw new \InvalidArgumentException("This adapter can only set values of "
                . Model\SectionValuePool::class);
        }

        $this->optionAdapter->setMultipleValues($pool->getSection(), $values);

        return true;
    }

    /** @inheritDoc */
    public function deletePool($pool): bool
    {
        if (is_string($pool)) {
            $poolSection = $this->optionAdapter->getNode($pool);
        } elseif ($pool instanceof Model\SectionValuePool) {
            $poolSection = $pool->getSection();
        } else {
            $type = is_object($pool) ? get_class($pool) : gettype($pool);
            throw new \InvalidArgumentException("String or instance of " . Model\SectionValuePool::class
                . " expected. Got " . $type);
        }

        $this->optionAdapter->removeNode($poolSection->getFQN());

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
}
