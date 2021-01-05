<?php declare(strict_types=1);
namespace SeStep\GeneralSettings;

use IteratorAggregate;
use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Model\INode;
use Traversable;

class Settings implements IteratorAggregate
{
    public IOptionsAdapter $options;

    public function __construct(IOptionsAdapter $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $fullName
     * @param string|null $filterType
     * @return INode|null
     */
    public function findNode(string $fullName, string $filterType = null): ?INode
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
    public function getSection($fullName): Model\IOptionSection
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
    public function getOption(string $fullName): Model\IOption
    {
        $node = $this->findNode($fullName);

        if (!$node instanceof Model\IOption) {
            throw new NodeNotFoundException($fullName, $node);
        }

        return $node;
    }

    /**
     * @param string $fullName
     * @return mixed
     */
    public function getValue(string $fullName)
    {
        return $this->options->getValue($fullName);
    }

    public function setValue(string $fullName, $value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $this->setValue(DomainLocator::concatFQN($key, $fullName), $item);
            }

            return;
        }

        $this->options->setValue($value, $fullName);
    }

    /**
     * @return INode[]|Traversable
     */
    public function getIterator()
    {
        return $this->options->getIterator();
    }
}
