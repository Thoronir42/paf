<?php declare(strict_types=1);


namespace SeStep\GeneralSettingsInMemory;

use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Options\INode;
use SeStep\GeneralSettings\Options\IOptionSection;
use SeStep\GeneralSettings\Options\IOptionSectionWritable;
use SeStep\GeneralSettings\Options\OptionTypeEnum;
use SeStep\GeneralSettings\SectionNavigator;

class InMemoryOptionSection extends InMemoryNode implements IOptionSection, IOptionSectionWritable, \ArrayAccess
{

    protected $nodes = [];

    public function __construct(InMemoryOptionSection $parent, string $name, array &$data)
    {
        parent::__construct($parent, $name, $data);
        if (!isset($this->data['nodes'])) {
            $this->data['nodes'] = [];
        }

        $maxIntOffset = $this->data['maxIntOffset'] ?? -1;
        if (!is_finite($maxIntOffset) || $maxIntOffset < 0) {
            $this->data['maxIntOffset'] = 0;
        }
    }


    public function getIterator()
    {
        return new \ArrayIterator($this->getNodes());
    }

    public function hasNode($name): bool
    {
        return isset($this->nodes[$name]) || isset($this->data['nodes'][$name]);
    }

    public function getNode($name): INode
    {
        if (!isset($this->nodes[$name])) {
            if (!isset($this->data['nodes'][$name])) {
                throw new NodeNotFoundException(DomainLocator::concatFQN($name, $this->getFQN()));
            }

            $type = $this->data['nodes'][$name]['type'];
            if ($type == IOptionSection::TYPE_SECTION) {
                $node = new InMemoryOptionSection($this, "$name", $this->data['nodes'][$name]);
            } else {
                $node = new InMemoryOption($this, "$name", $this->data['nodes'][$name]);
            }

            $this->nodes[$name] = $node;
        }

        return $this->nodes[$name];
    }

    /** @return INode[] */
    public function getNodes(): array
    {
        $result = [];
        foreach (array_keys($this->data['nodes']) as $node) {
            $result[$node] = $this->getNode($node);
        }

        return $result;
    }

    public function getValue(string $name)
    {
        $dl = new DomainLocator($name);

        $section = SectionNavigator::getSectionByDomain($this, $dl);

        return $section[$dl->getName()]->getValue();
    }

    public function addSection(string $name): InMemoryOptionSection
    {
        $dl = new DomainLocator($name);
        /** @var InMemoryOptionSection $section */
        $section = SectionNavigator::getSectionByDomain($this, $dl, SectionNavigator::CREATE_IF_MISSING);

        $name = $dl->getName();
        $section->data['nodes'][$name] = [];

        return $section->nodes[$name] = new InMemoryOptionSection($section, $name, $section->data['nodes'][$name]);
    }

    public function setValue($value, string $name)
    {
        $dl = new DomainLocator($name);
        /** @var InMemoryOptionSection $section */
        $section = SectionNavigator::getSectionByDomain($this, $dl, SectionNavigator::CREATE_IF_MISSING);

        $name = $dl->getName();
        if ($value instanceof InMemoryNode) {
            $section->data['nodes'][$name] = $value->data;
            $section->nodes[$name] = $value;
        } else {
            if (!$section->hasNode($name)) {
                $section->data['nodes'][$name] = [
                    'value' => $value,
                    'type' => OptionTypeEnum::infer($value)
                ];
            } else {
                $node = $section->getNode($name);

                if ($node instanceof InMemoryOption) {
                    $node->setValue($value);
                } else {
                    throw new \RuntimeException("Could not set value");
                }
            }
        }
    }

    public function addValue($value)
    {
        $offset = $this->data['maxIntOffset']++;
        $this->setValue($value, "$offset");
    }

    public function removeNode($name)
    {
        unset($this->data['nodes'][$name]);
        unset($this->nodes[$name]);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data['nodes'][$offset]);
    }

    public function offsetUnset($offset)
    {
        $this->removeNode($offset);
    }

    /**
     * @param mixed $offset
     * @return INode
     */
    public function offsetGet($offset): INode
    {
        return $this->getNode($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->addValue($value);
        }

        $this->setValue($value, $offset);
    }

    public function count()
    {
        return count($this->data['nodes']);
    }
}
