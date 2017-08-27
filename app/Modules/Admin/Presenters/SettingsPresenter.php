<?php

namespace App\Modules\Admin\Presenters;


use App\Common\Auth\Authorizator;
use SeStep\SettingsControl\ISettingsControlFactory;

class SettingsPresenter extends AdminPresenter
{
    /** @var ISettingsControlFactory @inject */
    public $settingControlFactory;

    public function startup()
    {
        parent::startup();

        $this->validateAuthorization('admin-settings', Authorizator::READ, ':Front:Default:');


        $this->template->title = 'Settings';
    }

    public function actionDefault()
    {
    }

    public function createComponentSettings()
    {
        $container = $this->settings->findAll();

        $control = $this->settingControlFactory->create($container);
        $control->onSetValue[] = function ($name, $value) {
            try {
                $this->settings->setValue($name, $value);
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
