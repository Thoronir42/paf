<?php declare(strict_types=1);

namespace SeStep\Commentable\Lean;

use SeStep\ModularLeanMapper\MapperModule;

class CommentsMapperModule extends MapperModule
{
    public function __construct()
    {
        parent::__construct(__NAMESPACE__ . '\\Model', __NAMESPACE__ . '\\Repository');
    }
}
