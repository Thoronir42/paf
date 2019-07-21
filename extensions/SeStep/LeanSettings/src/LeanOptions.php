<?php declare(strict_types=1);

namespace SeStep\LeanSettings;


use Nette\InvalidStateException;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\IOptions;
use SeStep\GeneralSettings\Options\INode;
use SeStep\GeneralSettings\Options\OptionTypeEnum;
use SeStep\GeneralSettings\SectionNavigator;
use SeStep\LeanSettings\Model\Option;
use SeStep\LeanSettings\Model\OptionNode;
use SeStep\LeanSettings\Model\Section;
use SeStep\LeanSettings\Repository\OptionNodeRepository;

class LeanOptions implements IOptions
{
    /** @var OptionNodeRepository */
    private $nodeRepository;

    /** @var Section */
    private $rootSection;

    public function __construct(OptionNodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;

        $this->rootSection = $this->nodeRepository->findSection('.');
        if(!$this->rootSection) {
            $this->rootSection = $this->nodeRepository->createSection('.', 'Root entry for options');
        }
    }

    public function addSection(string $name): ?Section
    {
        $dl = new DomainLocator($name, '');
        return $this->nodeRepository->getSection(($dl), true);
    }

    public function addValue($value)
    {
        $nodes = $this->getNodes();
        for ($freeIndex = 0; $freeIndex < count($nodes) && array_key_exists("$freeIndex", $nodes); $freeIndex++);

        $this->setValue($value, "$freeIndex");
    }

    public function setValue($value, string $name)
    {
        $fqn = DomainLocator::concatFQN($name, '');
        $entry = $this->nodeRepository->find($fqn);

        if ($entry) {
            if ($entry instanceof Option) {
                $entry->value = $value;
                $this->nodeRepository->persist($entry);
            } else {
                throw new InvalidStateException("Can not set value of a section '{$entry->getFQN()}'");
            }
        } else {
            $parent = $this->nodeRepository->getSection((new DomainLocator($fqn))->getDomain(), true);

            $option = new Option();
            $option->type = OptionTypeEnum::infer($value);
            $option->fqn = $fqn;
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
        $parent = SectionNavigator::getSectionByDomain($this->rootSection, $dl);
        if(!$parent->hasNode($dl->getName())) {
            // todo: notify error?
            return;
        }

        $this->nodeRepository->delete($parent->getNode($dl->getName()));
        $parent->clearOptionsCache();
    }



    public function getIterator()
    {
        return $this->rootSection->getIterator();
    }

    public function offsetExists($offset)
    {
        return $this->rootSection->offsetExists($offset);
    }

    public function offsetUnset($offset)
    {
        return $this->rootSection->offsetUnset($offset);
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

    public function getCaption(): string
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
        $this->rootSection->clearOptionsCache();
        return $this->rootSection->getNodes();
    }

    public function getValue(string $name)
    {
        return $this->rootSection->getValue($name);
    }

    public function offsetGet($offset)
    {
        return $this->rootSection->offsetGet($offset);
    }

    /**
     * @param mixed $offset
     * @param INode $value
     */
    public function offsetSet($offset, $value)
    {
        $this->rootSection->offsetSet($offset, $value);
    }
}