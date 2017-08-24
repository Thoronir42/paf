<?php

namespace App\Common\Controls\NavigationMenu;

use Nette\Application\UI;
use SeStep\Navigation\Menu\Items\NavMenuLink;
use SeStep\Navigation\Menu\NavigationMenuSubitems;

/**
 * Class NavigationMenu
 * @package SeStep\Navigation\Control
 *
 * @property        string $title
 * @property        string $brand_target
 */
class NavigationMenu extends UI\Control
{
    use NavigationMenuSubitems;

    protected $title;

    protected $brand_target;

    public function __construct()
    {
        parent::__construct();

        $this->title = 'Appliaction N';
        $this->brand_target = 'Default:';

    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setBrandTarget($code)
    {
        $this->brand_target = $code;
    }

    public function link($destination, $args = array())
    {
        return $this->presenter->link($destination, $args);
    }

    public function itemCurrent(NavMenuLink $item)
    {
        /** @var NavMenuLink $subitem */
        foreach ($item->getItems() as $subitem) {
            if (!($subitem instanceof NavMenuLink)) {
                continue;
            }

            if ($subitem->getRole() == 'dropdown') {
                return $this->itemCurrent($subitem);
            }
            if ($this->presenter->isLinkCurrent($subitem->getTarget())) {
                return true;
            }
        }
        return false;
    }


    public function renderTop()
    {
        $this->template->setFile(__DIR__ . "/topMenu.latte");

        $this->template->title = $this->title;
        $this->template->brand_code = $this->brand_target;
        $this->template->items = $this->items;

        $this->template->render();
    }
}

interface INavigationMenuFactory
{

    /** @return NavigationMenu */
    public function create();
}
