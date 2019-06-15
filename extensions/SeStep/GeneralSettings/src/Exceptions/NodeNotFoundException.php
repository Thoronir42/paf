<?php

namespace SeStep\GeneralSettings\Exceptions;


use RuntimeException;

class NodeNotFoundException extends RuntimeException
{
    public function __construct($nodeFQN)
    {
        parent::__construct("Node '$nodeFQN' could not be found.");
    }
}

