<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\InlineOption;


use Nette\Application\AbortException;
use Nette\SmartObject;
use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Model\INode;
use SeStep\GeneralSettings\Settings;

/**
 * Class SettingsOptionAccessor
 * @package PAF\Modules\SettingsModule\InlineOption
 *
 * @method onValueChanged(string $fqn, mixed $value)
 * @method onError(\Throwable $ex, string $fqn, mixed $value)
 */
class SettingsOptionAccessor implements OptionAccessor
{
    use SmartObject;

    public $onValueChanged = [];
    public $onError = [];

    /** @var Settings */
    private $settings;

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
            $this->onValueChanged($fqn, $value);
            return true;
        } catch (\Throwable $ex) {
            if($ex instanceof AbortException) {
                throw $ex;
            }

            $this->onError($ex, $fqn, $value);
            return false;
        }
    }
}
