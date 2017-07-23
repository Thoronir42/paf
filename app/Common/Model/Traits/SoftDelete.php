<?php

namespace App\Common\Model\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SoftDelete
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }


}
