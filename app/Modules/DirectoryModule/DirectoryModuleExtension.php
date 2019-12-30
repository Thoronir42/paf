<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule;

use Nette\DI\CompilerExtension;

class DirectoryModuleExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $this->loadDefinitionsFromConfig($this->loadFromFile(__DIR__ . '/directoryModule.neon')['services']);
    }
}
