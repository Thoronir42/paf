<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Routing;

use Nette\Application\Routers\RouteList;
use PAF\Common\Router\RouterModule;

class SettingsRouterModule implements RouterModule
{

    public function setRoutes(RouteList $routeList): void
    {
        $routeList->addRoute('settings', [
            'presenter' => 'Settings',
            'action' => 'default',
        ]);
    }
}
