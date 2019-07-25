<?php

namespace SeStep\GeneralSettings\Exceptions;


use SeStep\GeneralSettings\Options\INode;

class OptionNotFoundException extends NodeNotFoundException
{
    public function __construct($optionFQN, INode $nodeIfExists = null)
    {
        $otherTypeMsg = $nodeIfExists ? ", the name contains an node of type {$nodeIfExists->getType()}." : '.';
        $message = "Option '$optionFQN' was not found" . $otherTypeMsg;

        parent::__construct($optionFQN, $message);
    }
}