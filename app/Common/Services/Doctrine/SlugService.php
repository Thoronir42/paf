<?php

namespace App\Common\Services\Doctrine;


use Kdyby\Doctrine\EntityRepository;

/**
 * @property EntityRepository $repository
 */
trait SlugService
{
    /**
     * @param string $slug
     * @return bool
     */
    public function slugExists($slug)
    {
        return $this->repository->countBy(['slug' => $slug]) > 0;
    }
}
