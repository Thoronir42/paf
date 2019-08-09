<?php declare(strict_types=1);
namespace SeStep\GeneralSettings;

use IteratorAggregate;
use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Model\INode;

class Settings implements IteratorAggregate
{
    /** @var IOptionsAdapter */
    public $options;

    public function __construct(IOptionsAdapter $options)
    {
        $this->options = $options;
    }

    public function findNode($fullName, string $filterType = null)
    {
        $node = $this->options->getNode($fullName);
        if ($node) {
            if ($filterType && !$node instanceof $filterType) {
                $node = null;
            }
        }

        return $node;
    }

    /**
     * @param mixed $fullName
     * @return Model\IOptionSection
     * @throws SectionNotFoundException
     */
    public function getSection($fullName)
    {
        $node = $this->findNode($fullName);

        if (!$node instanceof Model\IOptionSection) {
            throw new SectionNotFoundException($fullName, $node);
        }

        return $node;
    }

    /**
     * @param string $fullName
     * @return Model\IOption
     * @throws NodeNotFoundException
     */
    public function getOption(string $fullName)
    {
        $node = $this->findNode($fullName);

        if (!$node instanceof Model\IOption) {
            throw new NodeNotFoundException($fullName, $node);
        }

        return $node;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getValue($name)
    {
        return $this->options->getValue($name);
    }

    public function setValue($name, $value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $this->setValue(DomainLocator::concatFQN($key, $name), $item);
            }

            return;
        }

        $this->options->setValue($value, $name);
    }

    /**
     * @return INode[]|\Traversable
     */
    public function getIterator()
    {
        return $this->options->getIterator();
    }
}
