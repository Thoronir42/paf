<?php

namespace SeStep\GeneralSettings\Options;


use ArrayAccess;
use Countable;
use IteratorAggregate;

interface IOptionSection extends INode, IteratorAggregate, Countable
{
    const TYPE_SECTION = 'section';

    public function hasNode($name): bool;

    /**
     * @param $name
     * @return INode
     */
    public function getNode($name);

    /** @return INode[] */
    public function getNodes(): array;

    public function getValue(string $name);

    /**
     * @param mixed $offset
     * @return INode
     */
    public function offsetGet($offset);
}
