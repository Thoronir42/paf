<?php

namespace App\Presenters;


use SeStep\SettingsControl\ISettingsControlFactory;

class SettingsPresenter extends AdminPresenter
{
    /** @var ISettingsControlFactory @inject */
    public $settingControlFactory;

    public function startup()
    {
        parent::startup();

        $this->template->title = 'Settings';
    }

    public function actionDefault()
    {
        $this->template->settings = $this->settings->fetchAll();
    }

    public function createComponentSettings()
    {
        $control = $this->settingControlFactory->create();

        return $control;
    }
}
