<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Layout\Navigation;

use Nette\FileNotFoundException;
use Nette\Neon\Neon;
use Nette\Security\User;
use SeStep\GeneralSettings\Settings;
use SeStep\Navigation\Provider\AssociativeArrayProvider;
use SeStep\Navigation\Provider\NavigationItemsProvider;

final class NeonProvider implements NavigationItemsProvider
{
    /** @var string */
    private $file;
    /** @var User */
    private $user;
    /**
     * @var Settings
     */
    private $settings;

    public function __construct(string $file, User $user, Settings $settings)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $this->file = $file;
        $this->user = $user;
        $this->settings = $settings;
    }

    /** @inheritDoc */
    public function getItems()
    {
        $data = Neon::decode(file_get_contents($this->file));

        return iterator_to_array(new AssociativeArrayProvider($data['items'], [$this, 'checkRequirements']));
    }

    public function checkRequirements($item): bool
    {
        foreach ($item['requirements'] ?? [] as $requirement) {
            if (!$this->checkRequirement($requirement['type'], $requirement['value'])) {
                return false;
            }
        }

        return true;
    }

    private function checkRequirement(string $type, $value): bool
    {
        switch ($type) {
            case 'permission':
                return $this->checkPermission($value);

            case 'setting':
                return $this->checkSetting($value);
        }

        trigger_error("Unknown requirement type '$type'");
        return false;
    }

    private function checkPermission(string $permission): bool
    {
        return $this->user->isAllowed($permission);
    }

    private function checkSetting(string $settingName): bool
    {
        return (bool)$this->settings->getValue($settingName);
    }
}
