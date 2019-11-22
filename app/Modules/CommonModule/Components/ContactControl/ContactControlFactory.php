<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Components\ContactControl;

use PAF\Modules\CommonModule\Model\Contact;

interface ContactControlFactory
{
    public function create(Contact $contact): ContactControl;
}
