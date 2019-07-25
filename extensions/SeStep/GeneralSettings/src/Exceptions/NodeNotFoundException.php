<?php

namespace SeStep\GeneralSettings\Exceptions;


use RuntimeException;

class NodeNotFoundException extends RuntimeException
{
    /** @var string */
    private $fqn;

    public function __construct(string $nodeFQN, string $message = null)
    {
        $this->fqn = $nodeFQN;
        parent::__construct($message ?: "Node '$nodeFQN' could not be found.");
    }

    public function getFqn(): string
    {
        return $this->fqn;
    }
}

