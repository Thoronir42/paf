<?php declare(strict_types=1);

namespace PAF\Modules\Settings\Routing;

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

        $routeList->addRoute('api/settings/<fqn \w+(.\w+)*>', 'SettingsApi:optionEndpoint');
    }
}
