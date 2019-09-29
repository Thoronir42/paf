<?php declare(strict_types=1);

namespace SeStep\EntityIds;

interface HasIdGenerator
{
    public function injectEntityIdGenerator(IdGenerator $generator);
}
