<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use SeStep\Typeful\PropertyType;

class IntType extends PropertyType
{
    public static function getName(): string
    {
        return 'int';
    }
}
