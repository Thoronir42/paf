<?php

namespace App\Utils\Migrations;


use App\Utils\EntityInitializer;

abstract class BaseMigration
{
    /** @var EntityInitializer */
    protected $add;

    /** @var  MigraLog */
    protected $log;

    public function __construct(EntityInitializer $initializer, MigraLog $log)
    {
        $this->add = $initializer;
        $this->log = $log;
    }

    public final function __invoke()
    {
        $this->run();
    }

    /**
     * @return MigraLog
     */
    public abstract function run();
}
