<?php

namespace SeStep\LeanSettings\DI;


use Nette\DI\CompilerExtension;
use SeStep\LeanSettings\LeanOptions;
use SeStep\LeanSettings\Repository\LeanOptionNodeRepository;

class LeanSettingsExtensions extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('nodeRepository'))
            ->setFactory(LeanOptionNodeRepository::class);

        $builder->addDefinition($this->prefix('options'))
            ->setFactory(LeanOptions::class);
    }
}