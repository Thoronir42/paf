<?php declare(strict_types=1);
namespace SeStep\LeanSettings\DI;

use Nette\DI\CompilerExtension;
use SeStep\LeanSettings\LeanOptions;
use SeStep\LeanSettings\Repository\OptionNodeRepository;

class LeanSettingsExtensions extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('nodeRepository'))
            ->setFactory(OptionNodeRepository::class);

        $builder->addDefinition($this->prefix('options'))
            ->setFactory(LeanOptions::class);
    }
}
