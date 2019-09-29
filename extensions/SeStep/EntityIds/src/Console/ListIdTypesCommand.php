<?php declare(strict_types=1);

namespace SeStep\EntityIds\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListIdTypesCommand extends Command
{
    /** @var array */
    private $types;

    public function __construct(string $name, array $types)
    {
        parent::__construct($name);

        $this->types = $types;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['CheckSum', 'Type']);

        foreach ($this->types as $checkSum => $type) {
            $table->addRow([$checkSum, $type]);
        }

        $table->render();
    }
}
