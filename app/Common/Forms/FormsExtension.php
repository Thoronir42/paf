<?php


namespace PAF\Common\Forms;

use Nette\DI\CompilerExtension;

class FormsExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('formFactory'))
            ->setType(FormFactory::class);
    }
}
