<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Components\ContactControl;

use PAF\Modules\DirectoryModule\Model\Contact;

interface ContactControlFactory
{
    public function create(Contact $contact): ContactControl;
}
