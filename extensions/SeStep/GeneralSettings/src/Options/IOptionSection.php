<?php

namespace SeStep\GeneralSettings\Options;


use ArrayAccess;
use Countable;
use IteratorAggregate;

interface IOptionSection extends INode, IteratorAggregate, Countable
{
    const TYPE_SECTION = 'section';

    /** @return INode[] */
    public function getNodes(): array;

    public function getValue(string $name);

    /**
     * @param mixed $offset
     * @return INode
     */
    public function offsetGet($offset);
}
