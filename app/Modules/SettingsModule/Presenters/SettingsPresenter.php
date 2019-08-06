<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Presenters;

use Exception;
use Nette\Application\AbortException;
use PAF\Common\BasePresenter;
use PAF\Modules\SettingsModule\Components\SettingsControl\SettingsControl;

final class SettingsPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();

//        $this->validateAuthorization('admin-settings', Authorizator::READ, ':Common:Homepage:');
    }

    public function actionDefault(string $fqn = '.')
    {
        $section = $this->settings->getSection($fqn);

        $control = new SettingsControl($section, 4);

        $this['settings'] = $control;

        $control->onSetValue[] = function ($name, $value) {
            try {
                $this->settings->setValue($name, $value);
            } catch (Exception $e) {
                $this->sendJson([
                    'status' => 'error',
                    'message' => get_class($e) . ': ' . $e->getMessage(),
                    'source' => $e->getFile() . ':' . $e->getLine(),
                ]);
            }

            $this->sendJson([
                'status' => 'success'
            ]);
        };

        $control->onExpand[] = function ($expandFqn) {
            $this->redirect('this', ['fqn' => $expandFqn]);
        };
    }
}
