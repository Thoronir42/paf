<?php

namespace App\Common\Model\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Slug
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $slug;

    /** @return string */
    public function getSlug()
    {
        return $this->slug;
    }

    /** @param string $slug */
    public function setSlug($slug = "")
    {
        $this->slug = $slug;
    }


}