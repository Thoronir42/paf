<?php


namespace PAFData\Fixtures;


use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class InitializerModule
{
    /** @var OutputInterface */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output = null): void
    {
        $this->output = $output ?: new NullOutput();
    }

    abstract public function run(): int;
}
