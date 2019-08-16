<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Routing;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;
use PAF\Common\Router\RouterModule;

class CmsRouterModule extends RouterModule
{

    public function getRoutes(): Router
    {
        $router = new RouteList('Cms');

        $availablePages = ['terms-of-service'];
        $pageList = implode('|', $availablePages);

        $router[] = new Route("<pageName $pageList>[/<action=display>]", [
            'presenter' => 'Page',
        ]);

        return $router;
    }
}
