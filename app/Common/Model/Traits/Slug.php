<?php

namespace App\Common\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;

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

    /**
     * @param string $slug
     * @param bool   $format
     */
    public function setSlug($slug = "", $format = true)
    {
        if ($format) {
            $slug = Strings::webalize($slug);
        }
        $this->slug = $slug;
    }


}
