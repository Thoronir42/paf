<?php declare(strict_types=1);

namespace SeStep\LeanSettings;


use Dibi\NotImplementedException;
use Nette\InvalidStateException;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\IOptions;
use SeStep\GeneralSettings\Options\INode;
use SeStep\GeneralSettings\Options\OptionTypeEnum;
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
        $this->rootSection = $this->nodeRepository->getRootSection();
    }

    public function addSection($name)
    {
        return $this->getSection($name, true);
    }

    public function addValue($value)
    {
        $nodes = $this->getNodes();
        for ($freeIndex = 0; $freeIndex < count($nodes) && array_key_exists("$freeIndex", $nodes); $freeIndex++);

        $this->setValue($value, ".$freeIndex");
    }

    public function setValue($value, string $name)
    {
        $entry = $this->nodeRepository->find($name);

        if ($entry) {
            if ($entry instanceof Option) {
                $entry->value = $value;
                $this->nodeRepository->persist($entry);
            } else {
                throw new InvalidStateException("Can not set value of a section '{$entry->getFQN()}'");
            }
        } else {
            $parent = $this->getSection((new DomainLocator($name))->getDomain(), true);

            $option = new Option();
            $option->type = OptionTypeEnum::infer($value);
            $option->fqn = $name;
            $option->value = $value;
            $option->parentSection = $parent;

            $this->nodeRepository->persist($option);
            $parent->clearOptionsCache();
        }
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

    /** @return INode[] */
    public function getNodes(): array
    {
        return $this->rootSection->getNodes();
    }

    public function getValue(string $name, $domain = '')
    {
        return $this->rootSection->getValue($name, $domain);
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

    /**
     * @param OptionNode|string $node
     */
    protected function findParent($node)
    {
        if ($node instanceof OptionNode) {
            return $node->parentSection;
        }

        if (is_string($node)) {
            return $this->getSection((new DomainLocator($node))->getDomain());
        }

        // todo, exception throwing
        throw new \InvalidArgumentException();
    }

    protected function getSection($fqn, bool $create = false): ?Section
    {
        $section = $this->nodeRepository->find($fqn);
        if (!$section) {
            return $create ? $this->createSection($fqn) : null;
        } else {
            if (!$section instanceof Section) {
                $err = "Option node '$fqn' is not instance of Section, got: " . get_class($section);
                throw new InvalidStateException($err);
            }
        }


        return $section;
    }

    protected function createSection($fqn)
    {
        $dl = new DomainLocator($fqn);

        $section = new Section();
        $section->fqn = $fqn;
        if(!$dl->getDomain()) {
            $section->parentSection = $this->rootSection;
        } else {
            $parentNames = [];

            for (; $dl->getDomain(); $dl->pop()) {
                $parentNames[] = $dl->getDomain();
            }

            $parents = $this->nodeRepository->findSections($parentNames);
            $section->parentSection = null;

            throw new NotImplementedException('Parent section not implemented');
        }

        $this->nodeRepository->persist($section);

        return $section;
    }
}