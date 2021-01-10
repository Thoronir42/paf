<?php declare(strict_types=1);

namespace PAF\Modules\Settings\Routing;

use Nette\Application\Routers\RouteList;
use SeStep\NetteModularApp\Routing\RouterModule;

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
