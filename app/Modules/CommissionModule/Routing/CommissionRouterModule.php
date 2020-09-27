<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Routing;

use Nette\Application\Routers\RouteList;
use PAF\Common\Router\RouterModule;

class CommissionRouterModule implements RouterModule
{
    public function setRoutes(RouteList $routeList): void
    {
        $routeList->addRoute('commission/<id>', 'Commission:detail');
        $routeList->addRoute('create-quote', "Quotes:create");
        $routeList->addRoute('commissions', 'Commission:list');
        $routeList->addRoute('product/<slug>', 'Product:view');
        $routeList->addRoute('<presenter quotes|commission|price-list>[/<action=default>[/<id>]]');
    }
}
