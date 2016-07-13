<?php

namespace App\Utils\Migrations;

use App\Model\Services\BaseService;
use App\Utils\EntityInitializer;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Kdyby\Console\StringOutput;
use Kdyby\Doctrine\Console\SchemaUpdateCommand;
use Nette\DI\Container;
use Nette\DI\Extensions\InjectExtension;
use Nette\MemberAccessException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Tracy\Debugger;

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
	/** @var MigraLog */
	private $log;

	public function __construct(Container $context, MigraLog $log)
	{
		$this->context = $context;
		$this->log = $log;
		$this->initializer = new EntityInitializer($this);
	}

	/**
	 * @param $type
	 * @return BaseService
	 */
	public function getService($type)
	{
		$service = $this->context->getByType($type);

		if (!($service instanceof BaseService)) {
			throw new MemberAccessException('Migrations can only use model services of BaseService type, '
				. gettype($service) . ' requested.');
		}

		return $service;
	}

	public function getLog(){
		return $this->log;
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
		return new Migration_000_ProjectInitialise($this->initializer);
	}
}
