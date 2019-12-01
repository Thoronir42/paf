<?php declare(strict_types=1);

namespace Data;

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
    private $defaultFiles;

    /** @var string[] */
    private $files = [];

    private $currentFile;
    private $currentQueries;
    private $currentQueryIndex;

    private $maxLengthProgress;

    /** @var string */
    private $databaseName;

    public function __construct(Connection $connection, string $databaseName, array $defaultFiles = [])
    {
        parent::__construct();

        $this->connection = $connection;
        $this->defaultFiles = $defaultFiles;
        $this->databaseName = $databaseName;
    }

    public function configure()
    {
        $this->addOption('default-files', 'd', null, "Use predefined database initialize files");
        $this->addOption('drop-all-tables', null, null, "Drop tables before initialization");
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('default-files')) {
            $output->writeln("Using default files");
            $this->files = $this->defaultFiles;
        }
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('drop-all-tables')) {
            $this->dropAllTables($output);
        }

        if (empty($this->files)) {
            $output->writeln("No files specified!");
            return 1;
        }

        $output->writeln("Initializing database ...");

        foreach ($this->files as $path) {
            try {
                $this->currentFile = $path;
                $this->executeCurrentFile($output);

                $this->writeFileProgress($output, ' ok');
            } catch (\Throwable $ex) {
                $this->writeFileProgress($output, $this->getQueryProgress() . ' error');
                throw $ex;
            } finally {
                $output->writeln("");
            }
        }

        $output->writeln("Database initialized");
        return 0;
    }

    private function executeCurrentFile(OutputInterface $output)
    {
        if (!file_exists($this->currentFile)) {
            $this->writeFileProgress($output, "file not found");
            throw new FileNotFoundException("File '$this->currentFile' could not be found");
        }

        $script = file_get_contents($this->currentFile);
        $this->currentQueries = array_filter(explode(';', $script), function ($query) {
            return !empty(trim($query));
        });
        $this->currentQueryIndex = 0;
        $this->maxLengthProgress = 0;

        while ($this->currentQueryIndex < count($this->currentQueries)) {
            $this->writeFileProgress($output, $this->getQueryProgress());
            $this->connection->nativeQuery($this->currentQueries[$this->currentQueryIndex] . ';');

            $this->currentQueryIndex++;
        }
    }

    private function getQueryProgress()
    {
        return '[' . $this->currentQueryIndex . '/' . count($this->currentQueries) . ']';
    }

    private function writeFileProgress(OutputInterface $output, $progress)
    {
        $options = OutputInterface::VERBOSITY_VERBOSE;

        $output->write("\r", false, $options);
        $progressStr = "  - $this->currentFile $progress";
        $output->write($progressStr, false, $options);

        $len = mb_strlen($progressStr);
        if ($len < $this->maxLengthProgress) {
            $spaceCleaner = str_repeat(" ", $this->maxLengthProgress - $len);
            $output->write($spaceCleaner, false, $options);
        } else {
            $this->maxLengthProgress = $len;
        }
    }

    private function dropAllTables(OutputInterface $output)
    {
        $tables = $this->connection->query(
            'SELECT t.table_name FROM information_schema.tables t WHERE t.table_schema = %s',
            $this->databaseName
        )
            ->fetchPairs();

        $output->writeln('Removing ' . count($tables) . ' tables');
        try {
            $output->writeln("Disabling foreign key checks");
            $this->connection->query('SET FOREIGN_KEY_CHECKS = 0;');
            foreach ($tables as $table) {
                $output->write(" - deleting table $table... ", false, OutputInterface::VERBOSITY_VERBOSE);
                $this->connection->query('DROP TABLE %n', $table);
                $output->writeln("ok", OutputInterface::VERBOSITY_VERBOSE);
            }
        } catch (\Throwable $ex) {
            $output->writeln("error");
            $output->writeln($ex->getMessage());
        } finally {
            $output->writeln("Enabling foreign key checks");
            $this->connection->query('SET FOREIGN_KEY_CHECKS = 1;');
        }

        $output->writeln("Dropped " . count($tables) . ' tables');
    }
}
