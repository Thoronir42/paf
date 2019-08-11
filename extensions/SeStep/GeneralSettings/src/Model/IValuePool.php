<?php declare(strict_types=1);

namespace SeStep\GeneralSettings\Model;

interface IValuePool extends \Countable
{
    public function getValues(): array;

    public function isValueValid($value): bool;
}
