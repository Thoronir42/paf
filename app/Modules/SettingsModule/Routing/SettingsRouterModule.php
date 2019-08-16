<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Routing;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;
use PAF\Common\Router\RouterModule;

class SettingsRouterModule extends RouterModule
{

    public function getRoutes(): Router
    {
        $router = new RouteList('Settings');
        $router[] = new Route('settings', [
            'presenter' => 'Settings',
        ]);

        return $router;
    }
}
