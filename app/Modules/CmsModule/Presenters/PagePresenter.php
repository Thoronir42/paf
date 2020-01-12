<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Presenters;

use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use PAF\Common\BasePresenter;
use PAF\Common\Security\Authorizator;
use PAF\Modules\CmsModule\Components\Content\PageControl;
use PAF\Modules\CmsModule\Facade\CmsPages;
use PAF\Modules\CmsModule\Model\Page;

final class PagePresenter extends BasePresenter
{
    /** @var CmsPages @inject */
    public $pages;

    public function actionDisplay(string $pageName)
    {
        $page = $this->pages->getPage($pageName);

        $pageExists = !!$page;

        if (!$pageExists) {
            if (!$this->user->isAllowed(Page::class, Authorizator::CREATE)) {
                $this->error("Page $pageName could not be found");
            }

            $page = new Page();
            $page->slug = $pageName;

            $this->flashTranslate('cms.page.missingNotice');
        }

        $pageControl = new PageControl($page);

        $pageControl->onUpdate[] = function (string $content) use ($page) {
            $page->content = $content;
            $this->pages->setContent($page, $content);
            if ($this->isAjax()) {
                $this->sendJson([
                    'status' => 'wip',
                    'content' => $content,
                ]);
            }

            $this->flashTranslate('cms.page.updated');
            $this->redirect('this');
        };
        $this['page'] = $pageControl;

        $this->template->page = $page;
        $this->template->pageExists = $pageExists;
    }
}
