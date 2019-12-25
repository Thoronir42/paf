<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls\Menu;

use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use SeStep\Navigation\Menu\Items\ANavMenuItem;
use SeStep\Navigation\Menu\Items\NavMenuLink;

class MenuItemControl extends Control
{
    /** @var ANavMenuItem */
    private $item;

    public function __construct(ANavMenuItem $item)
    {
        $this->item = $item;
    }

    /**
     * @return ANavMenuItem
     */
    public function getItem(): ANavMenuItem
    {
        return $this->item;
    }

    public function render()
    {
        $role = $this->item->getRole();
        $file = __DIR__ . "/menuItem-$role.latte";
        if (!file_exists($file)) {
            throw new InvalidArgumentException("Unknown menuItem role: " . $role);
        }

        $template = $this->createTemplate();
        $template->setFile($file);
        $template->item = $this->item;

        $template->render();
    }

    public function createComponent($name): IComponent
    {
        $subItems = $this->item->getItems();
        if (!isset($subItems[$name])) {
            throw new InvalidStateException("Menu item $name not found");
        }

        return new MenuItemControl($subItems[$name]);
    }

    public function link(string $destination, $args = []): string
    {
        return $this->presenter->link($destination, $args);
    }

    public function itemCurrent(NavMenuLink $item)
    {
        return $this->parent->itemCurrent($item);
    }
}
