<?php declare(strict_types=1);

namespace SeStep\EntityIds;

trait HasIdGenerator
{
    protected $idGenerator;

    public function injectEntityIdGenerator(IdGenerator $generator)
    {
        $this->idGenerator = $generator;
    }
}
