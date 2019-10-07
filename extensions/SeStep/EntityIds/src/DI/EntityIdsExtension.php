<?php declare(strict_types=1);

namespace SeStep\EntityIds\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\EntityIds\CharSet;
use SeStep\EntityIds\Console\GenerateTypeIdCommand;
use SeStep\EntityIds\Console\ListIdTypesCommand;
use SeStep\EntityIds\EncodedTypeIdGenerator;
use SeStep\EntityIds\Type\CheckSum;

class EntityIdsExtension extends CompilerExtension
{

    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'charList' => Expect::string(),
            'types' => Expect::array(),
            'idLength' => Expect::int(12),
            'distinctPositions' => Expect::arrayOf(Expect::int()),
            'registerCommands' => Expect::bool(false),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();
        $this->addDefinitions($builder, $config);

        if ($config->registerCommands) {
            $this->addCommandDefinitions($builder, $config);
        }
    }

    private function addDefinitions(ContainerBuilder $builder, $config)
    {
        $charSet = $builder->addDefinition($this->prefix('charSet'))
            ->setType(CharSet::class)
            ->setAutowired(false);
        if (isset($config->charList)) {
            $charSet->setArgument('charList', $this->uniqueCharList($config->charList));
        }

        $checkSum = $builder->addDefinition($this->prefix('checkSum'))
            ->setType(CheckSum::class)
            ->setAutowired(false)
            ->setArguments([
                $charSet,
                $config->distinctPositions ?? [],
            ]);

        $builder->addDefinition($this->prefix('idGenerator'))
            ->setType(EncodedTypeIdGenerator::class)
            ->setArguments([
                $charSet,
                $checkSum,
                $config->types ?? [],
                $config->idLength,
            ]);
    }

    private function addCommandDefinitions(ContainerBuilder $builder, $config)
    {
        $builder->addDefinition($this->prefix('listIdTypesCommand'))
            ->setType(ListIdTypesCommand::class)
            ->setArguments([
                'name' => 'id:type:list',
                'types' => $config->types,
            ])
            ->addTag('console.command', ['name' => 'id:type:list']);

        $builder->addDefinition($this->prefix('generateTypeIdCommand'))
            ->setType(GenerateTypeIdCommand::class)
            ->setArguments([
                'name' => 'id:type:generate',
                'types' => $config->types,
            ])
            ->addTag('console.command', ['name' => 'id:type:generate']);
    }

    private function uniqueCharList(string $charList): string
    {
        $chars = [];
        $duplicateChars = [];

        $length = strlen($charList);
        for ($i = 0; $i < $length; $i++) {
            $char = $charList[$i];
            if (!array_key_exists($char, $chars)) {
                $chars[$char] = true;
            } else {
                $duplicateChars[$char] = true;
            }
        }

        if (!empty($duplicateChars)) {
            throw new \InvalidArgumentException("Parameter 'charList' contains duplicated characters: " .
                implode('', array_keys($duplicateChars)));
        }

        return $charList;
    }
}
