<?php

namespace App\Modules\Admin\Presenters;


use Kdyby\Console\StringOutput;
use Nette\Application\BadRequestException;
use SeStep\Migrations\Migrations;

class UtilitiesPresenter extends AdminPresenter
{
    /** @var Migrations @inject */
    public $migrations;

    public function startup()
    {
        parent::startup();

        $this->authenticationCheck('You need to be logged in to access Utilities', ['migrate']);
    }

    public function actionDefault()
    {
        throw new BadRequestException();
    }

    public function actionMigrate($key)
    {
        $output = new StringOutput();
        $this->migrations->setOutput($output);

        $this->migrations->tryRun($key);

        $this->template->log = $output->getOutput();
    }
}
