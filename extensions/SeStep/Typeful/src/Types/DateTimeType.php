<?php declare(strict_types=1);

namespace SeStep\Typeful\Types;

use Latte\Runtime\Filters;
use SeStep\Typeful\PropertyType;

class DateTimeType extends PropertyType
{
    public static function getName(): string
    {
        return 'datetime';
    }

    public function renderValue($value, array $options = [])
    {
        // TODO: Avoid using internal class Filters
        return Filters::date($value, 'Y-m-d H:i');
    }
}
