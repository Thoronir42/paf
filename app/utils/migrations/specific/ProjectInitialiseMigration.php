<?php

namespace App\Utils\Migrations;


use App\Model\Settings\AOption;
use App\Utils\EntityInitializer;

class ProjectInitialiseMigration extends BaseMigration
{
	public function __construct(EntityInitializer $initializer)
	{
		parent::__construct($initializer);
		$this->title = 'Project initialisation';
		$this->description = 'Creates basic entities for proper functionality';
	}

	public function run()
	{
		$this->addUsers();
		$this->addOptions();
	}

	private function addOptions()
	{
		$this->log->writeln($this->add->option(AOption::TYPE_BOOL, 'Enable quotes', true));
	}

	private function addUsers()
	{
		$this->log->writeln($this->add->user('Toanir', 'test'));
	}
}
