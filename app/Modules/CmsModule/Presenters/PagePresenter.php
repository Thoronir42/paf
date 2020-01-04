<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Presenters;

use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use PAF\Common\BasePresenter;
use PAF\Modules\CmsModule\Components\Content\PageControl;
use PAF\Modules\CmsModule\Facade\CmsPages;

final class PagePresenter extends BasePresenter
{
    /** @var CmsPages @inject */
    public $pages;

    public function actionDisplay(string $pageName)
    {
        $page = $this->pages->getPage($pageName);

        if (!$page) {
            $code = IResponse::S404_NOT_FOUND;
            throw new BadRequestException("Page $pageName could not be found", $code);
        }

        $this['page'] = new PageControl($page);
        $this->template->page = $page;
    }
}
