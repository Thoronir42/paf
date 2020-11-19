<?php declare(strict_types=1);

namespace PAF\Utils;

use PHPUnit\Runner\BeforeTestHook;

abstract class InitializerExtension implements BeforeTestHook
{
    private $initialized = [];

    final public function executeBeforeTest(string $test): void
    {
        $classDelimiter = mb_strpos($test, '::');
        $testClass = mb_substr($test, 0, $classDelimiter);

        if ($this->isInitialized($testClass)) {
            return;
        }
        $this->initializeClass($testClass);
    }

    abstract protected function initializeClass(string $className): void;

    protected function isInitialized(string $subject): bool
    {
        return $this->initialized[$subject] ?? false;
    }

    protected function markInitialized(string $subject): void
    {
        $this->initialized[$subject] = true;
    }
}
