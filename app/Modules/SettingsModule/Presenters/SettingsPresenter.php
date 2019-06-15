<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Presenters;


use Exception;
use PAF\Common\BasePresenter;
use PAF\Common\Security\Authorizator;
use PAF\Modules\SettingsModule\Components\SettingsControl\ISettingsControlFactory;
use PAF\Modules\SettingsModule\Components\SettingsControl\SettingsControl;
use SeStep\SettingsInterface\LazySettingsIterator;

final class SettingsPresenter extends BasePresenter
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
        $container = new LazySettingsIterator($this->settings);

        /** @var SettingsControl $settings */
        $settings = $this['settings'];
        $settings->setSection($container);
    }

    public function createComponentSettings()
    {
        $control = $this->settingControlFactory->create(5);
        $control->onSetValue[] = function ($name, $value) {
            try {
                $this->settings->setValue($value, $name);
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
