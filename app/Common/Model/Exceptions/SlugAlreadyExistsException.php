<?php

namespace App\Common\Model\Exceptions;


class SlugAlreadyExistsException extends \Exception
{
    public function __construct($slugValue, $entityName = "")
    {
        $message = "Slug value '$slugValue' already exists" . ($entityName ? " on $entityName" : "") . ".";
        parent::__construct($message);
    }
}