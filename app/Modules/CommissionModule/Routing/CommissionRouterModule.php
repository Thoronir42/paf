<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Routing;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;
use PAF\Common\Router\RouterModule;

class CommissionRouterModule extends RouterModule
{
    public function getRoutes(): Router
    {
        $router = new RouteList('Commission');
        $router[] = new Route('case/<id>', 'Cases:detail');
        $router[] = new Route('create-quote', "Quotes:create");
        $router[] = new Route('<presenter quotes|cases|price-list>[/<action=default>[/<id>]]');

        return $router;
    }
}
