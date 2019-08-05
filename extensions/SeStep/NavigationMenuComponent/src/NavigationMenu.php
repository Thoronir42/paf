<?php declare(strict_types=1);

namespace SeStep\NavigationMenuComponent;

use Nette\Application\UI;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;
use Nette\Localization\ITranslator;
use SeStep\Navigation\Menu\Items\ANavMenuItem;
use SeStep\Navigation\Menu\Items\INavMenuItem;
use SeStep\Navigation\Menu\Items\NavMenuLink;
use SeStep\Navigation\Provider\NavigationItemsProvider;

class NavigationMenu extends UI\Control
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $brandTarget;

    /** @var ANavMenuItem[] */
    private $items;
    /**
     * @var ITranslator
     */
    private $translator;

    /***
     * NavigationMenu constructor.
     * @param string $title
     * @param ANavMenuItem[]|NavigationItemsProvider $items
     * @param string|null $brandTarget
     * @param ITranslator|null $translator
     */
    public function __construct(string $title, $items, string $brandTarget = null, ITranslator $translator = null)
    {
        if ($items instanceof NavigationItemsProvider) {
            $items = $items->getItems();
        }

        $this->title = $title;
        $this->brandTarget = $brandTarget;
        $this->items = $items;

        $this->translator = $translator;
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
                if ($subItem->getRole() == 'dropdown' && $this->itemCurrent($subItem)) {
                    return true;
                }
            }
        }

        return false;
    }


    public function renderTop()
    {
        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__ . "/navigationMenuTop.latte");

        $this->template->title = $this->title;
        $this->template->brandTarget = $this->brandTarget;
        $this->template->items = $this->items;

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
