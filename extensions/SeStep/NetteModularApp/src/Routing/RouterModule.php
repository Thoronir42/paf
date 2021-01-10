<?php declare(strict_types=1);

namespace SeStep\NetteModularApp\Routing;

use Nette\Application\Routers\RouteList;

interface RouterModule
{
    public function setRoutes(RouteList $routeList): void;
}
