<?php

namespace App\Presenters;


use App\Controls\Settings\ISettingsControlFactory;
use App\Controls\Settings\SettingsControl;
use App\Model\Settings\AOption;
use App\Model\Settings\Settings;
use Nette\Application\BadRequestException;

class SettingsPresenter extends AdminPresenter
{
	/** @var ISettingsControlFactory @inject */
	public $settingControlFactory;

	/** @var Settings @inject */
	public $settings;

	public function startup()
	{
		parent::startup();

		$this->template->title = 'Settings';
	}

	public function actionDefault()
	{
		$this->template->settings = $this->settings->fetchAll();
	}

	public function actionEdit($handle)
	{
		/** @var AOption $option */
		$option = $this->settings->findOneBy(['handle' => $handle]);
		if(!$option){
			throw new BadRequestException('Setting ' . $handle . ' could not be found.');
		}

		/** @var SettingsControl $control */
		$control = $this['controlFactory'];

		$control->setOption($option);
	}


	public function createComponentSettings()
	{
		$control = $this->settingControlFactory->create();

		return $control;
	}
}
