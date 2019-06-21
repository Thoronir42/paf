<?php


namespace SeStep\GeneralSettingsInMemory;


use Nette\InvalidStateException;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Options\INode;
use SeStep\GeneralSettings\Options\IOption;
use SeStep\GeneralSettings\Options\IOptionSection;

class InMemoryOptionSection extends InMemoryNode implements IOptionSection
{

    protected $nodes = [];

    public function __construct(InMemoryOptionSection $parent, string $name, array &$data)
    {
        parent::__construct($parent, $name, $data);
        if (!isset($this->data['nodes'])) {
            $this->data['nodes'] = [];
        }

        if (!isset($this->data['maxIntOffset']) || !is_finite($this->data['maxIntOffset']) || $this->data['maxIntOffset'] < 0) {
            $this->data['maxIntOffset'] = 0;
        }
    }


    public function getIterator()
    {
        return new \ArrayIterator($this->getNodes());
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

    public function getValue(string $name, $domain = '')
    {
        $dl = DomainLocator::create($name, $domain);

        $section = $this;
        while ($dl->getDomain()) {
            if (!$section instanceof IOptionSection) {
                throw new SectionNotFoundException(DomainLocator::concatFQN($section->getFQN(), $dl->getDomain()));
            }

            $section = $section[$dl->shiftDomain()];
        }

        return $section[$dl->getName()]->getValue();
    }

    public function addSection($name): InMemoryOptionSection
    {
        $section = $this;

        if (strpos($name, self::DOMAIN_DELIMITER)) {
            $dl = new DomainLocator($name);

            while ($dl->getDomain()) {
                $child = $dl->shiftDomain();
                if (!isset($section[$child])) {
                    $section = $section->addSection($child);
                    continue;
                }

                if ($section[$child] instanceof IOption) {
                    $optNode = new DomainLocator($child, $section->getFQN());
                    throw new InvalidStateException("Can not add section '$name'. $optNode is an option node");
                }

                $section = $section[$child];
            }

            $name = $dl->getName();
        }


        $section->data['nodes'][$name] = [];

        return $section->nodes[$name] = new InMemoryOptionSection($section, $name, $section->data['nodes'][$name]);
    }


    public function getNode($name): INode
    {
        if (!isset($this->nodes[$name])) {
            if (!isset($this->data['nodes'][$name])) {
                throw new NodeNotFoundException(DomainLocator::concatFQN($name, $this->getFQN()));
            }

            $type = $this->data['nodes'][$name]['type'];
            if ($type == IOptionSection::TYPE_SECTION) {
                $node = new InMemoryOptionSection($this, $name, $this->data['nodes'][$name]);
            } else {
                $node = new InMemoryOption($this, $name, $this->data['nodes'][$name]);
            }

            $this->nodes[$name] = $node;
        }

        return $this->nodes[$name];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data['nodes'][$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data['nodes'][$offset]);
        unset($this->nodes[$offset]);
    }

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
            $offset = $this->data['maxIntOffset']++;
        }

        if ($value instanceof InMemoryNode) {
            $this->data['nodes'][$offset] = $value->data;
            $this->nodes[$offset] = $value;
            return;
        }

        if (!isset($this[$offset])) {
            if (is_string($value)) {
                $type = IOption::TYPE_STRING;
            } elseif (is_int($value)) {
                $type = IOption::TYPE_INT;
            } elseif (is_bool($value)) {
                $type = IOption::TYPE_BOOL;
            } else {
                throw new \InvalidArgumentException();
            }

            $data = ['value' => $value, 'type' => $type];
            $this[$offset] = new InMemoryOption($this, $offset, $data);
            return;
        }

        $node = $this[$offset];

        if ($node instanceof InMemoryOption) {
            $node->setValue($value);
        } else {
            throw new \RuntimeException("Could not set value");
        }
    }

    public function count()
    {
        return count($this->data['nodes']);
    }
}