<?php declare(strict_types=1);

namespace PAF\Modules\CmsModule;

use Nette\DI\CompilerExtension;

class CmsExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $config = $this->loadFromFile(__DIR__ . '/cmsExtension.neon');
        $this->loadDefinitionsFromConfig($config['services']);
    }
}
