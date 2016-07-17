<?php

namespace App\Controls\Settings;

use App\Model\Settings\AOption;
use App\Model\Settings\OptionBool;
use App\Model\Settings\OptionInt;
use App\Model\Settings\OptionString;
use App\Model\Settings\Settings;
use Libs\LatteFilters\YesNoFilter;
use Nette\Application\UI;

class OptionControl extends UI\Control
{
	const
		TYPE_SELECT = 'select',
		TYPE_TEXT = 'text',
		TYPE_INT = 'int';

	/** @var Settings */
	private $settings;

	public function __construct(Settings $settings)
	{
		parent::__construct();
		$this->settings = $settings;
	}

	public function render(AOption $option){
		$this->template->option = $option;
		$this->template->type = $this->findType($option);
		
		$this->template->readableValue = $this->humanifyValue($option);

		$this->template->setFile(__DIR__ . '/optionValue.latte');
		$this->template->render();
	}

	public function handleValues($handle){
		$option = $this->settings->option($handle);

		if($option instanceof OptionBool){
			$this->presenter->sendJson($option->getValues());
		}
		$this->presenter->sendJson(['status' => 'error', 'message' => 'This option does not support values']);
	}

	private function findType(AOption $option)
	{
		$class = get_class($option);
		switch ($class){
			case OptionBool::class:
				return self::TYPE_SELECT;
			case OptionString::class:
				return self::TYPE_TEXT;
			case OptionInt::class:
				return self::TYPE_INT;
			default:
				return $class;
		}
	}

	private function humanifyValue(AOption $option)
	{
		switch (get_class($option)){
			default:
				return $option->value;
			case OptionBool::class:
				$filter = new YesNoFilter();
				break;
		}
		return $filter($option->value);
	}
}
