<?php

namespace PAF\Modules\Feed\Components\FeedControl;

use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Html;
use PAF\Modules\Feed\FeedEvents;

class FeedControl extends Control
{
    private FeedEvents $events;

    private EntryControlFactory $entryControlFactory;
    private array $feedEntries;

    private string $templateFile = __DIR__ . '/feedControl.latte';

    private ?Html $controlPrototype = null;

    public function __construct(EntryControlFactory $entryControlFactory, array $feedEntries)
    {
        $this->events = new FeedEvents();
        $this->entryControlFactory = $entryControlFactory;
        $this->feedEntries = $feedEntries;
    }

    public function render()
    {
        $template = $this->createTemplate();
        $template->setFile($this->templateFile);
        $el = $this->getControlPrototype();
        $el->setAttribute('id', $this->lookupPath());
        $template->controlElement = $el;

        $template->entries = $this->feedEntries;

        $template->render();
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile(string $templateFile): void
    {
        $this->templateFile = $templateFile;
    }

    public function getControlPrototype(): Html
    {
        if (!$this->controlPrototype) {
            $el = Html::el('div');
            $this->controlPrototype = $el;
        }

        return $this->controlPrototype;
    }

    public function addEvent(string $type, string $event, $callback)
    {
        $this->events->register($type, $event, $callback);
    }

    protected function createComponent(string $name): ?IComponent
    {
        return $this->entryControlFactory->create($this->events, $this->feedEntries[$name]);
    }
}
