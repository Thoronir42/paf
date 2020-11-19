<?php declare(strict_types=1);

namespace PAF\Utils;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class StorageInitializerExtension extends InitializerExtension
{
    /** @var string */
    private $storageRoot;

    /** @var FilesystemInterface */
    private $storage;

    public function __construct(string $storageRoot = null)
    {
        if (!$storageRoot) {
            $storageRoot = dirname(__DIR__, 2) . '/temp/test/';
        }
        $this->storageRoot = $storageRoot;
    }

    protected function initializeClass(string $className): void
    {
        if (!is_callable([$className, 'initializeTestingStorage'])) {
            return;
        }
        call_user_func([$className, 'initializeTestingStorage'], $this->getStorage());
    }

    protected function getStorage(): FilesystemInterface
    {
        if (!$this->storage) {
            $this->storage = new Filesystem(new Local($this->storageRoot));
        }

        return $this->storage;
    }
}
