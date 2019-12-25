<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Layout\Navigation;

use Nette\Security\User;
use SeStep\Navigation\Menu\Items\ANavMenuItem;
use SeStep\Navigation\Provider\AssociativeArrayProvider;
use SeStep\Navigation\Provider\NavigationItemsProvider;

class SignItemProvider implements NavigationItemsProvider
{
    /** @var User */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return ANavMenuItem[]|\Traversable
     */
    public function getItems()
    {

        if ($this->user->isLoggedIn()) {
            $item = [
                'caption' => 'generic.sign-out',
                'target' => ':Common:Sign:out',
            ];
        } else {
            $item = [
                'caption' => 'generic.sign-in',
                'target' => ':Common:Sign:in'
            ];
        }

        return new AssociativeArrayProvider([$item]);
    }
}
