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

        $linkEl = self::getIconHtml($this->contactDefinitions, $this->contact);
        
        echo $linkEl;
    }

    public static function getIconHtml(ContactDefinitions $definitions, Contact $contact): Html
    {
        $iconEl = Html::el('i');
        $iconEl->class[] = 'fa ' . $definitions->getIconClass($contact->type);

        $linkEl = Html::el('a');
        $linkEl->class[] = 'btn btn-default';
        $linkEl->href($definitions->formatHref($contact->type, $contact->value));
        $linkEl->addHtml($iconEl);

        return $linkEl;
    }
}
