<?php


namespace PAF\Common\Forms;

use Nette\Application\UI\Form;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class FormsExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'defaultFormClass' => Expect::string(Form::class),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('formFactory'))
            ->setType(FormFactory::class)
            ->setArgument('defaultFormClass', $this->config->defaultFormClass);
    }
}
