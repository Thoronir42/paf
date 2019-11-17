<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use SeStep\Typeful\PropertyType;

class TextType extends PropertyType
{
    public static function getName(): string
    {
        return 'text';
    }
}
