<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Components\NavigationMenu;


use Nette\Application\UI;
use Nette\Localization\ITranslator;
use SeStep\Navigation\Menu\Items\NavMenuLink;
use SeStep\Navigation\Menu\NavigationMenuSubitems;


class NavigationMenu extends UI\Control
{
    use NavigationMenuSubitems;

    /** @var string */
    protected $title;

    /** @var string */
    protected $brand_target;
    /**
     * @var ITranslator
     */
    private $translator;

    public function __construct(ITranslator $translator = null)
    {
        $this->title = 'Appliaction N';
        $this->brand_target = 'Default:';

        $this->translator = $translator;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setBrandTarget(string $code)
    {
        $this->brand_target = $code;
    }

    public function link(string $destination, $args = array()): string
    {
        return $this->presenter->link($destination, $args);
    }

    public function itemCurrent(NavMenuLink $item): bool
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
        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__ . "/topMenu.latte");

        $this->template->title = $this->title;
        $this->template->brand_code = $this->brand_target;
        $this->template->items = $this->items;

        $this->template->render();
    }
}

interface INavigationMenuFactory
{
    public function create(): NavigationMenu;
}
