<?php declare(strict_types=1);
namespace PAF\Modules\CmsModule\Facade;

use PAF\Modules\CmsModule\Model\Page;
use PAF\Modules\CmsModule\Repository\PageRepository;

class CmsPages
{
    /** @var PageRepository */
    private $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPage(string $name): ?Page
    {
        return $this->repository->findOneBy([
            'slug' => $name,
        ]);
    }

    public function setContent(Page $page, string $content)
    {
        $page->content = $content;
        $this->repository->persist($page);

        return true;
    }
}
