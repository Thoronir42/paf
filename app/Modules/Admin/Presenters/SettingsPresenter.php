<?php

namespace App\Modules\Admin\Presenters;


use App\Common\Auth\Authorizator;
use SeStep\SettingsControl\ISettingsControlFactory;
use SeStep\SettingsControl\SettingsControl;
use SeStep\SettingsInterface\LazySettingsIterator;

class SettingsPresenter extends AdminPresenter
{
    /** @var ISettingsControlFactory @inject */
    public $settingControlFactory;

    public function startup()
    {
        parent::startup();

        $this->validateAuthorization('admin-settings', Authorizator::READ, ':Front:Default:');
    }

    public function actionDefault()
    {
        $container = new LazySettingsIterator($this->options);

        /** @var SettingsControl $settings */
        $settings = $this['settings'];
        $settings->setSection($container);
    }

    public function createComponentSettings()
    {
        $control = $this->settingControlFactory->create(5);
        $control->onSetValue[] = function ($name, $value) {
            try {
                $this->options->setValue($value, $name);
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
