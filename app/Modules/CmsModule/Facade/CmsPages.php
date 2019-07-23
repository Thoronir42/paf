<?php


namespace PAF\Modules\CmsModule\Facade;


use PAF\Modules\CmsModule\Model\Page;

class CmsPages
{
    public function getPage($name): ?Page
    {
        $page = new Page();
        $page->title = $name;
        $page->content = <<<HTML
<ul>
    <li>Customer will look at my horse.</li>
    <li>Customer will give it a lick.</li>
    <li>Customer will not point out that it, in fact, is dirty.</li>
</ul>
HTML;

        return $page;
    }
}