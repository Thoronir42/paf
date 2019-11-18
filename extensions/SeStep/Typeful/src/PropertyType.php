<?php declare(strict_types=1);

namespace SeStep\Typeful;

abstract class PropertyType
{

    abstract public static function getName(): string;

    public function renderValue($value, array $options = [])
    {
        return $value;
    }
}
