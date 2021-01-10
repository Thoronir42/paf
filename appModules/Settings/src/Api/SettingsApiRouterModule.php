<?php declare(strict_types=1);

namespace PAF\Modules\Settings\Api;

use Nette\Application\Routers\RouteList;
use SeStep\NetteModularApp\Routing\RouterModule;

class SettingsApiRouterModule implements RouterModule
{
    public function setRoutes(RouteList $routeList): void
    {
        $routeList->addRoute('settings/<fqn \w+(.\w+)*>', 'SettingsApi:optionEndpoint');
    }
}
