<?php


namespace SeStep\LeanSettings;


use SeStep\GeneralSettings\Options\INode;
use SeStep\GeneralSettings\Options\IOptionSection;

/**
 * @property LeanOptionNode[] $childNodes m:belongsToMany(parentNode)
 */
class LeanSection extends LeanOptionNode implements IOptionSection
{
    public function getType(): string
    {
        return IOptionSection::TYPE_SECTION;
    }


    public function getIterator()
    {
        return new \ArrayIterator($this->childNodes);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        $childNodes = $this->childNodes;

        return isset($childNodes[$offset]);
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if($this->offsetExists($offset)) {
            $this->removeFromChildNodes($this->childNodes[$offset]);
        }
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->childNodes);
    }

    /** @return INode[] */
    public function getNodes(): array
    {
        return $this->childNodes;
    }

    public function getValue(string $name, $domain)
    {
        // TODO: Implement getValue() method.
    }

    /**
     * @param mixed $name
     * @return IOptionSection
     */
    public function addSection($name)
    {
        // TODO: Implement addSection() method.
    }

    /**
     * @param mixed $offset
     * @return INode
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    /**
     * @param mixed $offset
     * @param INode $value
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }
}