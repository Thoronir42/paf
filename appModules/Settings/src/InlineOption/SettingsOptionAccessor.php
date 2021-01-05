<?php declare(strict_types=1);

namespace PAF\Modules\Settings\InlineOption;

use Nette\SmartObject;
use SeStep\GeneralSettings\Model\INode;
use SeStep\GeneralSettings\Settings;

/**
 * @method onValueChanged(string $fqn, mixed $value)
 * @method onError(\Throwable $ex, string $fqn, mixed $value)
 */
class SettingsOptionAccessor implements OptionAccessor
{
    use SmartObject;

    public array $onValueChanged = [];
    public array $onError = [];

    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /** @inheritDoc */
    public function getNode(string $fqn): ?INode
    {
        return $this->settings->findNode($fqn);
    }

    /**
     * @param string $fqn
     * @param mixed $value
     *
     * @return bool
     */
    public function setValue(string $fqn, $value): bool
    {
        try {
            $this->settings->setValue($fqn, $value);
        } catch (\Throwable $ex) {
            $this->onError($ex, $fqn, $value);
            return false;
        }

        $this->onValueChanged($fqn, $value);
        return true;
    }
}
