<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls\Menu;

use Nette\ComponentModel\IComponent;
use SeStep\Navigation\Provider\NavigationItemsProvider;

class Navbar extends MenuControl
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $brandTarget;
    /** @var NavigationItemsProvider */
    private $signItems;

    public function __construct(
        string $brandTitle,
        $items,
        NavigationItemsProvider $signItems,
        string $brandTarget = null
    ) {
        parent::__construct($items);

        $this->title = $brandTitle;
        $this->signItems = $signItems;
        $this->brandTarget = $brandTarget;
    }

    public function createComponent($name): IComponent
    {
        if ($name === 'signLink') {
            $link = iterator_to_array($this->signItems->getItems())[0];
            return new MenuItemControl($link);
        }

        return parent::createComponent($name);
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/navbar.latte');

        $this->template->title = $this->title;
        $this->template->brandTarget = $this->brandTarget;
        $this->template->items = $this->items;

        $this->template->render();
    }
}
