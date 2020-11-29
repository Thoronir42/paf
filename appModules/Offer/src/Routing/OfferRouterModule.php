<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule\Routing;

use Nette\Application\Routers\RouteList;
use PAF\Common\Router\RouterModule;

class OfferRouterModule implements RouterModule
{
    public function setRoutes(RouteList $routeList): void
    {
        $routeList->addRoute('<presenter price-list>/<action=default>[/<id>]');
    }
}
