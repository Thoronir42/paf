<?php declare(strict_types=1);

namespace SeStep\EntityIds\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SeStep\EntityIds\CharSet;
use SeStep\EntityIds\EncodedTypeIdGenerator;
use SeStep\EntityIds\HasIdGenerator;
use SeStep\EntityIds\Type\CheckSum;

class EntityIdsExtension extends CompilerExtension
{

    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'charList' => Expect::string(),
            'autoWireGeneratorToTrait' => Expect::bool(false),
            'types' => Expect::array(),
            'idLength' => Expect::int(12),
            'distinctPositions' => Expect::arrayOf(Expect::int()),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

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

    public function beforeCompile()
    {
        $config = $this->getConfig();
        if ($config->autoWireGeneratorToTrait) {
            $builder = $this->getContainerBuilder();
            $idGenerator = $builder->getDefinition($this->prefix('idGenerator'));

            foreach ($builder->getDefinitions() as $definition) {
                if (!$definition instanceof ServiceDefinition) {
                    continue;
                }
                $type = $definition->getType();
                if (class_exists($type) && in_array(HasIdGenerator::class, class_uses($type))) {
                    $definition->addSetup(new Statement('injectEntityIdGenerator', [$idGenerator]));
                }
            }
        }
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
