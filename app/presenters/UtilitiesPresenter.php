<?php

namespace App\Presenters;


use App\Utils\Migrations\BaseMigration;
use App\Utils\Migrations\Migrations;
use Nette\Application\BadRequestException;

class UtilitiesPresenter extends AdminPresenter
{
    /** @var Migrations @inject */
    public $migrations;

    public function startup()
    {
        parent::startup();
        if ($this->action != 'migrate' || $this->getParameter('handle') != BaseMigration::HANDLE_000_INIT) {
            $this->authenticationCheck('You need to be logged in to access Utilities', []);
        }
    }

    public function actionDefault()
    {
        throw new BadRequestException();
    }

    public function actionMigrate($handle)
    {
        $message_buffer = $this->migrations->getLog();
        $migration = $this->migrations->get($handle);
        if ($migration) {
            $message_buffer->writeln('Starting migration');

            $migration->run();

            $message_buffer->writeln($migration->title . ' finished.');
        }

        $this->template->log = $message_buffer->fetch();
    }
}
