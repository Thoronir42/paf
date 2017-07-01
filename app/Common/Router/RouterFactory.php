<?php

namespace App\Common\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{

    /**
     * @return Nette\Application\IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList;

        $adminRouteList = $router[] = new RouteList('Admin');

        $adminRouteList[] = new Route('admin/<presenter>/<action>[/<id>]', [
            'presenter' => 'settings',
            'action' => 'default'
        ]);

        $frontRouteList = $router[] = new RouteList('Front');

        $frontRouteList[] = new Route('utilities/migrate-<key>', 'Utilities:migrate');
        $frontRouteList[] = new Route('<presenter>/<action>[/<id>]', 'Default:default');

        return $router;
    }

}
