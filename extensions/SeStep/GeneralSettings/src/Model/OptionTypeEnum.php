<?php declare(strict_types=1);

namespace SeStep\GeneralSettings\Model;

class OptionTypeEnum
{
    const TYPE_STRING = 'string';
    const TYPE_BOOL = 'bool';
    const TYPE_INT = 'int';

    private function __construct()
    {
    }

    public static function infer($value): string
    {
        $type = gettype($value);
        switch ($type) {
            case 'integer':
                return IOption::TYPE_INT;
            case 'boolean':
                return IOption::TYPE_BOOL;
        }

        return $type;
    }
}
