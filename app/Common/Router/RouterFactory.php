<?php declare(strict_types=1);

namespace PAF\Common\Router;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

final class RouterFactory
{
    /** @var RouterModule[] */
    private $modules = [];
    /** @var Router */
    private $fallbackRouter;

    /**
     * RouterFactory constructor.
     * @param Router $fallbackRouter
     * @param RouterModule[] $modules associative array of application router modules
     */
    public function __construct(Router $fallbackRouter, array $modules)
    {
        $this->fallbackRouter = $fallbackRouter;
        $this->modules = $modules;
    }

    /** @return Router */
    public function create()
    {
        $router = new RouteList();

        foreach ($this->modules as $appModuleName => $routerModule) {
            $moduleRouteList = new RouteList($appModuleName);
            $routerModule->setRoutes($moduleRouteList);
            $router->add($moduleRouteList);
        }

        $router[] = $this->fallbackRouter;

        return $router;
    }
}
