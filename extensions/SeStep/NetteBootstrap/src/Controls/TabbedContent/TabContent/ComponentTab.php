<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls\TabbedContent\TabContent;

use Nette\Application\UI;

class ComponentTab implements TabContent
{
    /** @var UI\Component */
    private $component;
    /** @var string */
    private $renderMethod;

    public function __construct(UI\Component $component, string $renderMethod)
    {
        $this->component = $component;
        $this->renderMethod = $renderMethod;
    }


    public function getHtml()
    {
        return call_user_func([$this->component, $this->renderMethod]);
    }
}
