<?php

namespace App\Utils\Migrations;

use App\Utils\EntityInitializer;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Kdyby\Console\StringOutput;
use Kdyby\Doctrine\Console\SchemaUpdateCommand;
use Nette\DI\Container;
use Nette\DI\Extensions\InjectExtension;
use Nette\MemberAccessException;
use SeStep\Model\BaseDoctrineService;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class Migrations
 * @package BaseModule\Utils\Migrations
 *
 * Migrations are executable command batches that supply easy to use changes of database state. In example, with
 * migration it is possible to add several entities with one query. Migrations should be deterministic, which means
 * that once migration is complete, it should not repeat changes.
 *
 * This class works as a big factory for individual migrations and can either give them itself so that the migration
 * can access whole entity service or it can give the migration its EntityInitializer, which serves as united adding
 * interface.
 */
final class Migrations
{
	/** @var EntityInitializer */
	private $initializer;
	/** @var Container */
	private $context;

	/** @var SchemaUpdateCommand @inject */
	public $schemaUpdateCommand;

	private $all_migrations;

	/** @var BufferedOutput */
	private $log;

	public function __construct(Container $context)
	{
		$this->context = $context;
		$this->log = new BufferedOutput();
		$this->initializer = new EntityInitializer($this);
	}

	/**
	 * @param $type
	 * @return BaseDoctrineService
	 */
	public function getService($type)
	{
		$service = $this->context->getByType($type);

		if (!($service instanceof BaseDoctrineService)) {
			throw new MemberAccessException('Migrations can only use model services of BaseService type, '
				. gettype($service) . ' requested.');
		}

		return $service;
	}

	/**
	 * @return BufferedOutput
	 */
	public function getLog(){
		return $this->log;
	}

	public function getAll()
	{
		if (!$this->all_migrations) {
			$this->all_migrations = $this->buildMigrationsList();
		}

		return $this->all_migrations;
	}

	public function get($key)
	{
		$migrations = $this->getAll();
		if (!array_key_exists($key, $migrations)) {
			$this->log->writeln('Migration of handle ' . $key . ' was not found.');
			return null;
		}
		/** @var BaseMigration $migration */
		$migration = $migrations[$key];
		$migration->injectMessageBuffer($this->log);

		return $migration;
	}

	private function buildMigrationsList()
	{
		return [
			BaseMigration::HANDLE_000_INIT => new ProjectInitialiseMigration($this->initializer),
		];
	}

	protected function updateDatabase()
	{
		$input = new ArrayInput(array('--force' => true));
		$output = new StringOutput();

		InjectExtension::callInjects($this->context, $this->schemaUpdateCommand);
		$this->schemaUpdateCommand->setHelperSet(new HelperSet(['em' => new EntityManagerHelper($this->em)]));
		$this->schemaUpdateCommand->run($input, $output);

		echo 'db schema updated<br />';
	}

	public function get_000_initialize()
	{
		return new ProjectInitialiseMigration($this->initializer);
	}
}
