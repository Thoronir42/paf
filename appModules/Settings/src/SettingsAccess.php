<?php declare(strict_types=1);

namespace PAF\Modules\Settings;

use PAF\Modules\Settings\Components\SettingsControl\OptionNodeControl;
use PAF\Modules\Settings\InlineOption\SettingsOptionAccessor;
use SeStep\GeneralSettings\Settings;

trait SettingsAccess
{
    /** @inject */
    public Settings $settings;

    protected string $settingsRootFqn = '';

    public function createComponentOption(): OptionNodeControl
    {
        $optionAccessor = new SettingsOptionAccessor($this->settings);
        $optionAccessor->onValueChanged[] = function ($fqn) {
            if ($this->isAjax()) {
                $this->sendJson([
                    'status' => 'success'
                ]);
            } else {
                $this->flashMessage("Value of $fqn changed");
            }
        };
        $optionAccessor->onError[] = function ($ex) {
            $this->sendJson([
                'status' => 'error',
                'message' => get_class($ex) . ': ' . $ex->getMessage(),
                'source' => $ex->getFile() . ':' . $ex->getLine(),
            ]);
        };

        return new OptionNodeControl($optionAccessor, $this->settingsRootFqn, $this->translator ?? null);
    }
}
