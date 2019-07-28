<?php

namespace PAF\Commands;

use Dibi\Connection;
use Nette\FileNotFoundException;
use PAF\Utils\BaseCommand;

class InitDatabaseCommand extends BaseCommand
{
    /** @var Connection */
    private $connection;

    /** @var string[] */
    private $files = [];

    private $currentFile;
    private $currentQueries;
    private $currentQueryIndex;

    private $maxLengthProgress;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @param string[] $files */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    public function run(): int
    {
        $this->writeln("Initializing database ... \n");

        foreach ($this->files as $path) {
            try {
                $this->currentFile = $path;
                $this->executeCurrentFile();

                $this->writeFileProgress(' ok');
            } catch (\Throwable $ex) {
                $this->writeFileProgress($this->getQueryProgress() . ' error');
                throw $ex;
            } finally {
                $this->writeln();
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
        $this->write("\r");
        $progressStr = "  - $this->currentFile $progress";
        $this->write($progressStr);

        $len = mb_strlen($progressStr);
        if ($len < $this->maxLengthProgress) {
            $this->write(str_repeat(" ", $this->maxLengthProgress - $len));
        } else {
            $this->maxLengthProgress = $len;
        }
    }
}
