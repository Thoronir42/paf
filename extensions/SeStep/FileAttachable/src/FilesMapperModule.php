<?php declare(strict_types=1);

namespace SeStep\FileAttachable;


use SeStep\ModularLeanMapper\MapperModule;

class FilesMapperModule extends MapperModule
{
    public function __construct()
    {
        parent::__construct(__NAMESPACE__ . '\\Model', __NAMESPACE__ . '\\Service');
    }
}
