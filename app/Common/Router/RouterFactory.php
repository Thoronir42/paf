<?php declare(strict_types=1);

namespace PAF\Common\Router;


use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{
    /** @var array */
    private $modules;

    public function __construct($modules = [])
    {
        $this->modules = $modules;
    }


    /**
     * @return Nette\Application\IRouter
     */
    public function createRouter()
    {
        $router = new RouteList();

        $router[] = $this->createCmsModule();

        $modules = implode("|", $this->modules);

        $router[] = new Route("<presenter>/<action>[/<id>]", 'Common:Homepage:default');

        return $router;
    }

    public function createCmsModule(): Nette\Routing\Router
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
