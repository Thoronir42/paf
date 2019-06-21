<?php declare(strict_types=1);

namespace SeStep\LeanSettings;


use SeStep\GeneralSettings\IOptions;
use SeStep\GeneralSettings\Options\INode;
use SeStep\LeanSettings\Repository\LeanOptionNodeRepository;

class LeanOptions implements IOptions
{
    /** @var LeanOptionNodeRepository */
    private $nodeRepository;

    /** @var LeanSection */
    private $rootSection;

    public function __construct(LeanOptionNodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
        $this->rootSection = $this->nodeRepository->getRootSection();
    }

    public function addSection($name)
    {
        // TODO: Implement addSection() method.
    }

    public function setValue($value, string $name, string $domain = '')
    {
        $entry = $this->nodeRepository->find($name, $domain);
        if ($entry instanceof LeanOption) {

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

    public function getValue(string $name, $domain)
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
}