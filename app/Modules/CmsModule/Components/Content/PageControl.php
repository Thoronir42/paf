<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Components\Content;

use Nette\Application\UI;
use Nette\Utils\Html;
use PAF\Modules\CmsModule\Model\Page;

class PageControl extends UI\Control
{
    /** @var Page */
    private $page;

    /** @var Html */
    private $controlPrototype;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function render()
    {
        $prototype = $this->getControlPrototype();

        echo $prototype->startTag();
        $this->renderTitle();
        $this->renderControls();
        $this->renderContent();
        echo $prototype->endTag();
    }

    public function renderTitle()
    {
        $this->template->page = $this->page;
        $this->template->render(__DIR__ . '/pageControl-title.latte');
    }

    public function renderControls()
    {
        $this->template->page = $this->page;
        $this->template->render(__DIR__ . '/pageControl-controls.latte');
    }

    public function renderContent()
    {
        $this->template->page = $this->page;
        $this->template->render(__DIR__ . '/pageControl-content.latte');
    }

    public function getControlPrototype()
    {
        if (!$this->controlPrototype) {
            $prototype = Html::el('div');
            $prototype->appendAttribute('class', 'cms-page');

            $this->controlPrototype = $prototype;
        }

        return $this->controlPrototype;
    }
}
