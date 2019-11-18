<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule;

use Nette\DI\CompilerExtension;

class CommissionModuleExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $this->loadDefinitionsFromConfig($this->loadFromFile(__DIR__ . '/commissionModule.neon')['services']);
    }
}
