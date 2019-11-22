<?php


namespace PAF\Modules\CommonModule\Components\ContactControl;

use Nette\Application\UI\Control;
use Nette\Utils\Html;
use PAF\Modules\CommonModule\Model\Contact;
use PAF\Modules\CommonModule\Services\ContactDefinitions;

class ContactControl extends Control
{
    /** @var Contact */
    private $contact;
    /** @var ContactDefinitions */
    private $contactDefinitions;

    public function __construct(Contact $contact, ContactDefinitions $contactDefinitions)
    {
        $this->contact = $contact;
        $this->contactDefinitions = $contactDefinitions;
    }

    public function renderIconBtn()
    {
        if (!$this->contact->value) {
            return;
        }

        $iconEl = Html::el('i');
        $iconEl->class[] = 'fa ' . $this->contactDefinitions->getIconClass($this->contact->type);

        $linkEl = Html::el('a');
        $linkEl->class[] = 'btn btn-default';
        $linkEl->href($this->contactDefinitions->formatHref($this->contact->type, $this->contact->value));
        $linkEl->addHtml($iconEl);
        
        echo $linkEl;
    }
}
