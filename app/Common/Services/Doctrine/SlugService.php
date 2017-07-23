<?php

namespace App\Common\Services\Doctrine;


use App\Common\Model\Traits\SoftDelete;
use Kdyby\Doctrine\EntityRepository;

/**
 * @property EntityRepository $repository
 */
trait SlugService
{
    /**
     * @param string    $slug
     * @param null|bool $softDeleted
     *
     * @return bool
     */
    public function slugExists($slug, $softDeleted = false)
    {
        $where = ['slug' => $slug];
        if (is_bool($softDeleted) && in_array(SoftDelete::class, class_uses($this->repository->getClassName()))) {
            $where['deleted'] = $softDeleted;
        }

        return $this->repository->countBy($where) > 0;
    }
}
