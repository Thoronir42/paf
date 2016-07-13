<?php

namespace App\Presenters;


use App\Utils\Migrations\Migrations;
use Nette\Application\BadRequestException;

class UtilitiesPresenter extends AdminPresenter
{
	/** @var Migrations @inject */
	public $migrations;

	public function startup()
	{
		parent::startup();
		$this->authenticationCheck();
	}

	public function actionDefault(){
		throw new BadRequestException();
	}

	public function actionInitialise()
	{
		$migration = $this->migrations->get_000_initialize();

		$log = $migration->run();

		$this->flashMessage('Initial setup was succesfull');
		
	}
}
