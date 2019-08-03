<?php declare(strict_types=1);

namespace PAF\Common\Model\Exceptions;

use Nette\InvalidStateException;

class SlugAlreadyExistsException extends InvalidStateException
{
    public function __construct(string $slugValue, string $entityName = "")
    {
        $message = "Slug value '$slugValue' already exists" . ($entityName ? " on $entityName" : "") . ".";
        parent::__construct($message);
    }
}
