<?php

namespace PAF\Common\Commands;

use Dibi\Connection;
use Nette\FileNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDatabaseCommand extends Command
{
    protected static $defaultName = 'app:database:init';

    /** @var Connection */
    private $connection;

    /** @var string[] */
    private $files = [];

    private $currentFile;
    private $currentQueries;
    private $currentQueryIndex;

    private $maxLengthProgress;

    /** @var OutputInterface */
    private $out;

    public function __construct(Connection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
    }

    public function configure()
    {
        $this->addOption('default-files', 'd', null, "Use predefined database initialize files");
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        if($input->getOption('default-files')) {
            $root = dirname(dirname(__DIR__));
            $this->files = [
                $root . '/Modules/CommonModule/Model/database/initialize.sql',
                $root . '/Modules/CommissionModule/Model/database/initialize.sql',
                $root . '/Modules/PortfolioModule/Model/database/initialize.sql',
                $root . '/Modules/CmsModule/Model/database/initialize.sql',
                $root . '/../extensions/SeStep/LeanSettings/database/initialize.sql',
            ];
        }

        $output->writeln('BASD');
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->out = $output;
        if(empty($this->files)) {
            $output->writeln("No files specified!");
            return 1;
        }

        $output->writeln("Initializing database ...");

        foreach ($this->files as $path) {
            try {
                $this->currentFile = $path;
                $this->executeCurrentFile();

                $this->writeFileProgress(' ok');
            } catch (\Throwable $ex) {
                $this->writeFileProgress($this->getQueryProgress() . ' error');
                throw $ex;
            } finally {
                $output->writeln("");
            }
        }

        return 0;
    }

    private function executeCurrentFile()
    {
        if (!file_exists($this->currentFile)) {
            $this->writeFileProgress("file not found");
            throw new FileNotFoundException("File '$this->currentFile' could not be found");
        }

        $script = file_get_contents($this->currentFile);
        $this->currentQueries = array_filter(explode(';', $script), function ($query) {
            return !empty(trim($query));
        });
        $this->currentQueryIndex = 0;
        $this->maxLengthProgress = 0;

        while ($this->currentQueryIndex < count($this->currentQueries)) {
            $this->writeFileProgress($this->getQueryProgress());
            $this->connection->nativeQuery($this->currentQueries[$this->currentQueryIndex] . ';');

            $this->currentQueryIndex++;
        }
    }

    private function getQueryProgress()
    {
        return '[' . $this->currentQueryIndex . '/' . count($this->currentQueries) . ']';
    }

    private function writeFileProgress($progress)
    {
        $this->out->write("\r");
        $progressStr = "  - $this->currentFile $progress";
        $this->out->write($progressStr);

        $len = mb_strlen($progressStr);
        if ($len < $this->maxLengthProgress) {
            $this->out->write(str_repeat(" ", $this->maxLengthProgress - $len));
        } else {
            $this->maxLengthProgress = $len;
        }
    }
}
