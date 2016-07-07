<?php

namespace App\Presenters;


use App\Model\Settings\Settings;

class SettingsPresenter extends AdminPresenter
{
	/** @var Settings @inject */
	public $settings;

	public function actionDefault()
	{
		$this->template->settings = $this->settings->fetchAll();
	}
}
