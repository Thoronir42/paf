<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Api;

use Nette\Application\Routers\RouteList;
use SeStep\NetteModularApp\Routing\RouterModule;

class CommonApiRouterModule implements RouterModule
{
    public function setRoutes(RouteList $routeList): void
    {
        $routeList->addRoute('auth', ['presenter' => 'Auth']);
    }
}
