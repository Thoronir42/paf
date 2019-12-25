<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Layout\Navigation;

use Nette\FileNotFoundException;
use Nette\Neon\Neon;
use Nette\Security\User;
use SeStep\Navigation\Provider\AssociativeArrayProvider;
use SeStep\Navigation\Provider\NavigationItemsProvider;

final class NeonProvider implements NavigationItemsProvider
{
    /** @var string */
    private $file;
    /** @var User */
    private $user;

    public function __construct(string $file, User $user)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $this->file = $file;
        $this->user = $user;
    }

    /** @inheritDoc */
    public function getItems()
    {
        $data = Neon::decode(file_get_contents($this->file));

        return iterator_to_array(new AssociativeArrayProvider($data['items'], [$this, 'isUserAllowed']));
    }

    public function isUserAllowed($item)
    {
        $requiredPermission = $item['requiredPermission'] ?? null;
        if ($requiredPermission && !$this->user->isAllowed($requiredPermission)) {
            return false;
        }

        return true;
    }
}
