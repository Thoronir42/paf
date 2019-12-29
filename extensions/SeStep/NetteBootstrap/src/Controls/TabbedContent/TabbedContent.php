<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls\TabbedContent;

use Nette\Application\UI;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\Utils\Html;
use SeStep\NetteBootstrap\Controls\TabbedContent\TabContent\RawContent;

class TabbedContent extends UI\Control
{

    private $tabs = [];

    private $activeTab;

    public function addTab(string $name, $tabContent, string $caption, $options = [])
    {
        if (isset($this->tabs[$name])) {
            throw new InvalidStateException("Tab '$name' already exists");
        }

        $content = $this->initTabContent($tabContent, $options);
        if ($tabContent instanceof UI\Component) {
            $this[$name] = $tabContent;
        }
        $this->tabs[$name] = [
            'caption' => $caption,
            'content' => $content,
            'options' => $options,
        ];

        if (!$this->activeTab) {
            $this->activeTab = $name;
        }
    }

    public function setActiveTab(string $name)
    {
        if (!isset($this->tabs[$name])) {
            throw new InvalidArgumentException("Tab '$name' does not exist");
        }

        $this->activeTab = $name;
    }

    public function render()
    {
        $this->template->tabs = $this->tabs;
        $this->template->activeTab = $this->activeTab;

        $this->template->render(__DIR__ . '/tabbedContent.latte');
    }

    public function renderTabContent(string $name)
    {
        $tab = &$this->tabs[$name];

        /** @var TabContent\TabContent $content */
        $content = $tab['content'];
        echo $content->getHtml();
    }

    private function initTabContent($content, array $options): TabContent\TabContent
    {
        if (is_string($content) || $content instanceof Html) {
            return new RawContent($content);
        }

        if ($content instanceof UI\Control) {
            $renderMethod = $options['renderMethod'] ?? 'render';
            if (!method_exists($content, $renderMethod)) {
                $componentClass = get_class($content);
                throw new InvalidArgumentException("Tab content control is to be rendered via '$renderMethod'"
                    . " but controls class '$componentClass' does not have such method");
            }


            return new TabContent\ComponentTab($content, $renderMethod);
        }

        if (method_exists($content, '__toString')) {
            return new RawContent($content);
        }

        throw new InvalidArgumentException("Unrecognized content of class " . get_class($content));
    }
}
