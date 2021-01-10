<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Routing;

use Nette\Application\Routers\RouteList;
use SeStep\NetteModularApp\Routing\RouterModule;

class CmsRouterModule implements RouterModule
{

    public function setRoutes(RouteList $routeList): void
    {
        $availablePages = ['terms-of-service'];
        $pageList = implode('|', $availablePages);

        $routeList->addRoute("<pageName $pageList>[/<action=display>]", [
            'presenter' => 'Page',
        ]);
    }
}
