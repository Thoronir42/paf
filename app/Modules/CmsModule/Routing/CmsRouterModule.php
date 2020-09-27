<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Routing;

use Nette\Application\Routers\RouteList;
use PAF\Common\Router\RouterModule;

class CmsRouterModule implements RouterModule
{

    public function setRoutes(RouteList $router): void
    {
        $availablePages = ['terms-of-service'];
        $pageList = implode('|', $availablePages);

        $router->addRoute("<pageName $pageList>[/<action=display>]", [
            'presenter' => 'Page',
        ]);
    }
}
