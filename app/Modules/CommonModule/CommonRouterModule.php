<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule;

use Nette\Application\Routers\RouteList;
use PAF\Common\Router\RouterModule;

class CommonRouterModule implements RouterModule
{
    public function setRoutes(RouteList $routeList): void
    {
        $routeList->addRoute('sign-<action in|out>', ['presenter' => 'Sign']);
    }
}
