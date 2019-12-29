<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls\TabbedContent\TabContent;

use Latte\Runtime\Html as LatteHtml;
use Nette\Utils\Html as NetteHtml;

class RawContent implements TabContent
{
    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function getHtml()
    {
        if (is_scalar($this->content) || $this->content instanceof NetteHtml || $this->content instanceof LatteHtml) {
            return $this->content;
        }

        return (string)$this->content;
    }
}
