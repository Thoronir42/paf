<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Presenters;

use Exception;
use PAF\Common\BasePresenter;
use PAF\Modules\SettingsModule\Components\SettingsControl\SettingsControl;

final class SettingsPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();

//        $this->validateAuthorization('admin-settings', Authorizator::READ, ':Common:Homepage:');
    }

    public function actionDefault()
    {
        $control = new SettingsControl($this->settings->getSection('.'), 2);
        $this->addCallbacks($control);

        $this['settings'] = $control;
    }

    private function addCallbacks(SettingsControl $control)
    {
        $control->onSetValue[] = function ($name, $value) {
            try {
                $this->settings->setValue($value, $name);
                $this->sendJson([
                    'status' => 'success'
                ]);
            } catch (Exception $e) {
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
