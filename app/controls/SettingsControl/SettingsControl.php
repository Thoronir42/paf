<?php

namespace App\Controls\Settings;


use App\Model\Settings\AOption;
use App\Model\Settings\Settings;

use Nette\Application\UI;
use Nette\Application\UI\Multiplier;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;

/**
 * @property	boolean	$enableQuotes
 */
class SettingsControl extends UI\Control
{
	/** @var Settings */
	private $settings;

	public function __construct(Settings $settings)
	{
		parent::__construct();
		$this->settings = $settings;
	}

	public function renderTable()
	{
		$this->template->options = $this->settings->fetchAll();

		$this->template->setFile(__DIR__ . '/settingsControlTable.latte');
		$this->template->render();
	}

	public function handleSet($pk, $value, $name)
	{
		// fixme: blee?!!
		$pk = $_POST['pk'];
		$value = $_POST['value'];

		try{
			$this->settings->$pk = $value;
		} catch (InvalidArgumentException $ex){
			$this->sendJson([
				'status' => 'error',
				'message' => $ex->getMessage(),
			]);
		}
		$this->sendJson([
			'status' => 'success',
			'message' => "$pk set to $value",
		]);
	}

	public function createComponentOption()
	{
		return new Multiplier(function ($id) {
			return new OptionControl($this->settings);
		});
	}

	private function sendJson($data)
	{
		$this->presenter->sendJson($data);
	}
}

interface ISettingsControlFactory
{
	/**
	 * @param AOption $option
	 * @return SettingsControl
	 */
	public function create(Settings $settings);
}
