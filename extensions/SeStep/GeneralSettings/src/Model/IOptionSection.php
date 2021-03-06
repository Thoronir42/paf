<?php declare(strict_types=1);

namespace SeStep\GeneralSettings\Model;

use Countable;
use IteratorAggregate;

interface IOptionSection extends INode, IteratorAggregate, Countable
{
    const TYPE_SECTION = 'section';

    /**
     * @param mixed $name
     * @return bool
     */
    public function hasNode($name): bool;

    /**
     * @param mixed $name
     * @return INode|null
     */
    public function getNode($name);

    /** @return INode[] */
    public function getNodes(): array;


    public function getValue(string $name);
}
