<?php declare(strict_types=1);

namespace SeStep\LeanSettings\Model;

use Dibi\NotSupportedException;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\OptionNotFoundException;
use SeStep\GeneralSettings\Options\INode;
use SeStep\GeneralSettings\Options\IOptionSection;
use SeStep\GeneralSettings\SectionNavigator;

/**
 * @property OptionNode[] $childNodes m:belongsToMany(parent_section_id)
 */
class Section extends OptionNode implements IOptionSection
{
    protected function initDefaults()
    {
        $this->type = self::TYPE_SECTION;
    }

    public function setType(string $type)
    {
        if ($type !== self::TYPE_SECTION) {
            throw new NotSupportedException("Changing type of section to '$type' is not valid operation");
        }

        $this->row->type = $type;
    }

    public function getType(): string
    {
        return IOptionSection::TYPE_SECTION;
    }


    public function getIterator()
    {
        return new \ArrayIterator($this->childNodes);
    }

    public function offsetExists($offset)
    {
        $childNodes = $this->childNodes;

        return isset($childNodes[$offset]);
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->removeFromChildNodes($this->childNodes[$offset]);
        }
    }

    public function count()
    {
        return count($this->childNodes);
    }

    public function hasNode($name): bool
    {
        return isset($this->getNodes()[$name]);
    }

    /**
     * @param $name
     * @return OptionNode|null
     */
    public function getNode($name)
    {
        return $this->getNodes()[$name] ?? null;
    }


    /** @return OptionNode[] */
    public function getNodes(): array
    {
        $byFqn = [];
        $ownFqnLength = strlen($this->fqn);
        if ($ownFqnLength > 0) {
            $ownFqnLength += 1;
        }

        foreach ($this->childNodes as $node) {
            $key = substr($node->fqn, $ownFqnLength);
            $byFqn[$key] = $node;
        }

        return $byFqn;
    }

    public function getValue(string $name)
    {
        $dl = new DomainLocator($name);

        $parent = SectionNavigator::getSectionByDomain($this, $dl);

        $valueNode = $parent->getNode($dl->getName());
        if (!$valueNode instanceof Option) {
            $fqn = DomainLocator::concatFQN($dl->getName(), $parent->getFQN());
            throw new OptionNotFoundException($fqn, $valueNode);
        }

        return $valueNode->getValue();
    }

    public function offsetGet($offset)
    {
        return $this->getNode($offset);
    }

    public function clearOptionsCache()
    {
        $this->row->cleanReferencedRowsCache();
        $this->row->cleanReferencingRowsCache();
    }
}
