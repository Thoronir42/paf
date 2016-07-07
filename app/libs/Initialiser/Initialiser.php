<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 05.06.2016
 * Time: 11:57
 */

namespace App\Libs;


use App\Model\Services\States;
use App\Model\State;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Kdyby\Console\StringOutput;
use Kdyby\Doctrine\Console\SchemaUpdateCommand;
use Kdyby\Doctrine\EntityManager;
use Nette\DI\Container;
use Nette\DI\Extensions\InjectExtension;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;

class Initialiser
{
	/** @var SchemaUpdateCommand  */
	private $schemaUpdateCommand;

	/** @var EntityManager */
	private $em;
	/** @var Container */
	private $context;

	/** @var string[]  */
	public $messages;



	public function __construct(SchemaUpdateCommand $schemaUpdateCommand, EntityManager $em, Container $context)
	{
		$this->schemaUpdateCommand = $schemaUpdateCommand;
		$this->em = $em;
		$this->context = $context;

		$this->messages = [];
	}

	public function initialise()
	{
		$this->initialiseSchema();
	}

	private function initialiseSchema()
	{
		$input = new ArrayInput(array('--force' => true));
		$output = new StringOutput();

		InjectExtension::callInjects($this->context, $this->schemaUpdateCommand);
		$this->schemaUpdateCommand->setHelperSet(new HelperSet(['em' => new EntityManagerHelper($this->em)]));
		$this->schemaUpdateCommand->run($input, $output);

		$this->messages[] = $output->getOutput();
	}
}
