<?php

namespace App\Model\Settings;


use App\Model\Services\BaseService;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

class Settings extends BaseService
{
	/** @var ArrayHash */
	private $settings;

	public function __construct(EntityManager $em)
	{
		parent::__construct($em, $em->getRepository(AOption::class));
	}

	public function fetchAll()
	{
		if ($this->settings) {
			return $this->settings;
		}
		$settings = new ArrayHash();

		$result = $this->findBy([], ['handle' => 'ASC']);

		/** @var AOption $option */
		foreach ($result as $option) {
			$settings->{$option->handle} = $option;
		}

		return $this->settings = $settings;
	}
}
