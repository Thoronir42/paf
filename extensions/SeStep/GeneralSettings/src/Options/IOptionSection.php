<?php

namespace SeStep\GeneralSettings\Options;


use ArrayAccess;
use Countable;
use IteratorAggregate;

interface IOptionSection extends INode, ArrayAccess, IteratorAggregate, Countable
{
    const TYPE_SECTION = 'section';

    /** @return INode[] */
    public function getNodes();

    public function getValue(string $name, $domain);

    /**
     * @param mixed $name
     * @return IOptionSection
     */
    public function addSection($name);

    /**
     * @param mixed $offset
     * @return INode
     */
    public function offsetGet($offset);

    /**
     * @param mixed $offset
     * @param INode $value
     */
    public function offsetSet($offset, $value);

}
