<?php declare(strict_types=1);
namespace SeStep\LeanSettings\Exceptions;

class InvalidPoolValueException extends \RuntimeException
{
    public function __construct($value, $allowedValues)
    {
        $valuesStr = implode(', ', $allowedValues);
        parent::__construct("Value '$value' is not from allowed values: [$valuesStr]");
    }
}
