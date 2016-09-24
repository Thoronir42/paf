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

    }

    public function createComponentSettings()
    {
        $control = $this->settingControlFactory->create($this->settings->findAll());
        $control->onSet[] = function ($name, $value) {
            try {
                $this->settings->setValue($name, $value);
                throw new \Exception("Terkl má problém.");
            } catch (\Exception $e) {
                $this->sendJson([
                    'status' => 'error',
                    'message' => get_class($e) . ': ' . $e->getMessage(),
                    'source' => $e->getFile() . ':' . $e->getLine(),
                ]);
            }

            $this->sendJson(['status' => 'success']);
        };

        return $control;
    }
}
