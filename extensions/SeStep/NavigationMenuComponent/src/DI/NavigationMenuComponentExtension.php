<?php declare(strict_types=1);

namespace SeStep\NavigationMenuComponent\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\UnexpectedValueException;
use SeStep\NavigationMenuComponent\NavigationMenu;
use SeStep\NavigationMenuComponent\Loader\NeonProvider;

class NavigationMenuComponentExtension extends CompilerExtension
{
    const TYPE_NEON = 'neon';
    const TYPE_FACTORY = 'factory';

    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'brandTitle' => Expect::string(),
            'brandTarget' => Expect::string(),
            'itemSource' => Expect::string(self::TYPE_NEON),
            'itemsFile' => Expect::string(),
            'factory' => Expect::mixed(),
        ])->castTo('array');
    }


    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $builder->addDefinition($this->prefix('control'))
            ->setType(NavigationMenu::class)
            ->setArguments([
                'title' => $config['brandTitle'],
                'items' => $this->getItemFactory($config['itemSource'], $config),
                'brandTarget' => $config['brandTarget'],
            ]);
    }

    private function getItemFactory(string $source, array $config)
    {
        switch ($source) {
            case self::TYPE_NEON:
                return new Statement(NeonProvider::class, [$config['itemsFile']]);
        }

        throw new UnexpectedValueException("Unknown source type '$source'");
    }
}
