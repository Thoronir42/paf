<?php

namespace App\Utils\Migrations;


use App\Utils\EntityInitializer;
use Nette\Object;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @property        string $title
 * @property        string $description
 */
abstract class BaseMigration extends Object
{
	const HANDLE_000_INIT = 'init';

	/** @var EntityInitializer */
	protected $add;

	protected $title = 'Base migration (TITLE NOT SET)';

	protected $description = 'Base migration description (DESCRIPTION NOT SET)';

	/** @var  BufferedOutput */
	protected $log;

	public function __construct(EntityInitializer $initializer)
	{
		$this->add = $initializer;
	}

	public final function __invoke()
	{
		$this->run();
	}

	public abstract function run();

	public function getTitle()
	{
		return $this->title;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function injectMessageBuffer(BufferedOutput $log)
	{
		$this->log = $log;
	}
}
