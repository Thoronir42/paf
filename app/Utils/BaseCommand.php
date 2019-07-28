<?php

namespace PAF\Utils;

abstract class BaseCommand
{
    abstract public function run(): int;

    protected function write($value)
    {
        echo $value;
    }

    protected function writeln($value = null)
    {
        if (!is_null($value)) {
            echo $value;
        }

        echo "\n";
    }
}
