<?php declare(strict_types=1);
namespace SeStep\GeneralSettings\Exceptions;

use SeStep\GeneralSettings\Model\INode;

class OptionNotFoundException extends NodeNotFoundException
{
    public function __construct($optionFQN, INode $nodeIfExists = null)
    {
        $otherTypeMsg = $nodeIfExists ? ", the name contains an node of type {$nodeIfExists->getType()}." : '.';
        $message = "Option '$optionFQN' was not found" . $otherTypeMsg;

        parent::__construct($optionFQN, $message);
    }
}
