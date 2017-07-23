<?php

namespace App\Common\Services\Doctrine;


use App\Common\Model\Entity\Fursuit;
use SeStep\Model\BaseDoctrineService;

class Fursuits extends BaseDoctrineService
{
    use SlugService;
}
