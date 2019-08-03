<?php declare(strict_types=1);
namespace SeStep\GeneralSettings\Exceptions;

use SeStep\GeneralSettings\Options\INode;

class SectionNotFoundException extends NodeNotFoundException
{
    public function __construct($sectionFQN, INode $nodeIfExists = null)
    {
        $otherTypeMsg = $nodeIfExists ? ", the name contains an node of type {$nodeIfExists->getType()}." : '.';
        $message = "Section '$sectionFQN' was not found" . $otherTypeMsg;

        parent::__construct($sectionFQN, $message);
    }
}
