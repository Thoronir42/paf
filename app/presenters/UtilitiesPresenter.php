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

        $this->authenticationCheck('You need to be logged in to access Utilities', ['migrate']);
    }

    public function actionDefault()
    {
        throw new BadRequestException();
    }

    public function actionMigrate($key)
    {
        $message_buffer = $this->migrations->getLog();
        $migration = $this->migrations->get($key);
        if ($migration) {
            $message_buffer->writeln('Starting migration');

            $migration->run();

            $message_buffer->writeln($migration->title . ' finished.');
        } else {
            $message_buffer->writeln("Migration of key '$key' was not found ");
        }

        $this->template->log = $message_buffer->fetch();
    }
}
