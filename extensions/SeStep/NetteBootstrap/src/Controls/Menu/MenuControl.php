<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls\Menu;

use Nette\Application\UI;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;
use SeStep\Navigation\Menu\Items\ANavMenuItem;
use SeStep\Navigation\Menu\Items\INavMenuItem;
use SeStep\Navigation\Menu\Items\NavMenuLink;
use SeStep\Navigation\Provider\NavigationItemsProvider;

class MenuControl extends UI\Control
{
    /** @var ANavMenuItem[] */
    protected $items;

    /***
     * NavigationMenu constructor.
     * @param ANavMenuItem[]|NavigationItemsProvider $items
     */
    public function __construct($items)
    {
        if ($items instanceof NavigationItemsProvider) {
            $items = $items->getItems();
        }

        $this->items = $items;
    }

    public function link(string $destination, $args = array()): string
    {
        return $this->presenter->link($destination, $args);
    }

    /**
     * @param NavMenuLink $item
     * @return bool
     * @throws UI\InvalidLinkException
     */
    public function itemCurrent(NavMenuLink $item): bool
    {
        if ($item->getRole() === INavMenuItem::ROLE_LINK) {
            return $this->presenter->isLinkCurrent($item->getTarget());
        }

        if ($item->hasItems()) {
            foreach ($item->getItems() as $subItem) {
                if (!($subItem instanceof NavMenuLink)) {
                    continue;
                }
                if ($subItem->getRole() == NavMenuLink::ROLE_DROPDOWN && $this->itemCurrent($subItem)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function renderTabs(array $options = [])
    {
        $this->template->setFile(__DIR__ . "/menuControl-tabs.latte");

        $this->template->items = $this->items;
        $this->template->context = $options['context'] ?? null;

        $this->template->render();
    }

    public function createComponent($name): IComponent
    {
        if (!isset($this->items[$name])) {
            throw new InvalidStateException("Menu item $name not found");
        }

        return new MenuItemControl($this->items[$name]);
    }
}
