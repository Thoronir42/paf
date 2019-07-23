<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Presenters;


use PAF\Common\BasePresenter;
use PAF\Modules\CmsModule\Facade\CmsPages;
use PAF\Modules\CmsModule\Model\Page;

final class PagePresenter extends BasePresenter
{
    /** @var CmsPages @inject */
    public $pages;

    public function actionDisplay(string $pageName)
    {
        $page = $this->pages->getPage($pageName);

        $this->template->page = $page;
    }
}
