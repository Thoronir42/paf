<?php

namespace App\Utils\Migrations;


use Nette\Utils\ArrayHash;

class MigraLog
{
	const
		LVL_PRIMARY = 'primary',
		LVL_SUCCESS = 'success',
		LVL_INFO = 'info',
		LVL_WARNING = 'warning',
		LVL_DANGER = 'danger';

	const
		TYPE_SUCCESS = 'success',
		TYPE_DUPLICATION = 'duplication',
		TYPE_ERROR = 'error';

	/** @var ArrayHash */
	private $messages;

	public function __construct()
	{
		$this->messages = ArrayHash::from([
			self::TYPE_SUCCESS => [],
			self::TYPE_DUPLICATION => [],
			self::TYPE_ERROR => [],
		], false);
	}
}
