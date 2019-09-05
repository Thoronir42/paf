<?php declare(strict_types=1);

namespace PAF\Common\Logging;

use Nette;
use Nette\Application\IPresenter;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class LoggingExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        foreach ($builder->getDefinitions() as $definition) {
            if (!$definition instanceof ServiceDefinition) {
                continue;
            }
            $class = $definition->getType();
            if (!class_exists($class) || !is_a($class, LoggerAwareInterface::class, true)) {
                continue;
            }

            $definition->addSetup('setLogger');
        }
    }

    public function afterCompile(Nette\PhpGenerator\ClassType $class)
    {
        $initMethod = $class->getMethod('initialize');

        $iPresenter = IPresenter::class;
        $setFunction = self::class . '::setPresenterLogger';
        $loggerInterface = LoggerInterface::class;
        $body = <<<PHP
\$this->getService("application")->onPresenter[] = function(\$application, $iPresenter \$presenter) {
    $setFunction(\$presenter, \$this->getByType($loggerInterface::class));
};
PHP;


        $initMethod->addBody($body);
    }


    public static function setPresenterLogger(IPresenter $presenter, LoggerInterface $logger)
    {
        if ($presenter instanceof LoggerAwareInterface) {
            $presenter->setLogger($logger);
        }
    }
}
