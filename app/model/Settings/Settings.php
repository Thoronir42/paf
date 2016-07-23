<?php

namespace App\Model\Settings;


use App\Model\Services\BaseService;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidArgumentException;
use Nette\Utils\ArrayHash;

/**
 * @property 		boolean			$enable-quotes
 */
class Settings extends BaseService
{
	/** @var ArrayHash */
	private $settings;

	public function __construct(EntityManager $em)
	{
		parent::__construct($em, $em->getRepository(AOption::class));

		$this->settings = $this->prepareSettings();


	}

	public function fetchAll()
	{
		return $this->settings;
	}

	/**
	 * @param string $name
	 * @return AOption
	 */
	public function option($name)
	{
		if (!isset($this->settings->$name)) {
			throw new InvalidArgumentException("Option $name does not exist.");
		}
		return  $this->settings->$name;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function &__get($name)
	{
		$option = $this->option($name);
		$value = $option->getValue();
		return $value;

	}

	public function __set($name, $value)
	{
		$option = $this->option($name);
		$option->setValue($value);
		$this->save($option);
	}

	private function prepareSettings(){
		$settings = new ArrayHash();

		$result = $this->findBy([], ['handle' => 'ASC']);

		/** @var AOption $option */
		foreach ($result as $option) {
			$settings->{$option->handle} = $option;
		}
		return $settings;
	}
}
