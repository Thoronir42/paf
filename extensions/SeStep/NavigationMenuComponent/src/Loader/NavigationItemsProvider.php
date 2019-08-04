<?php declare(strict_types=1);

namespace SeStep\NavigationMenuComponent\Loader;

use SeStep\Navigation\Menu\Items\ANavMenuItem;

// todo: move to NavMenu package
interface NavigationItemsProvider
{
    /**
     * @return ANavMenuItem[]
     */
    public function getItems();
}
