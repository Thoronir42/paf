<?php

namespace App\Common\Model\Exceptions;


use Throwable;

class EnumValueException extends \InvalidArgumentException
{
    /**
     * EnumValueException constructor.
     * @param string $value
     * @param string[] $values
     */
    public function __construct($value, $values)
    {
        $message = "$value is not valid value of " . implode(", ", $values);
        parent::__construct($message);
    }
}