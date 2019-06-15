<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule\Presenters;


use PAF\Common\BasePresenter;
use PAF\Modules\CmsModule\Model\Page;

final class PagePresenter extends BasePresenter
{
    public function actionTos()
    {
        $page = new Page();
        $page->title = 'tos';
        $page->content = <<<HTML
<ul>
    <li>Customer will look at my horse.</li>
    <li>Customer will give it a lick.</li>
    <li>Customer will not point out that it, in fact, is dirty.</li>
</ul>
HTML;

    }
}
