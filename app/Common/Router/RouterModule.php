<?php declare(strict_types=1);

namespace PAF\Common\Router;

use Nette\Application\Routers\RouteList;

interface RouterModule
{
    public function setRoutes(RouteList $routeList): void;
}
