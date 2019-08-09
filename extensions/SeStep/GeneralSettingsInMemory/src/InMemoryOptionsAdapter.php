<?php declare(strict_types=1);

namespace SeStep\GeneralSettingsInMemory;

use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\GeneralSettings\Options\IValuePool;

final class InMemoryOptionsAdapter extends InMemoryOptionSection implements IOptionsAdapter
{

    private $rootData;

    public function __construct()
    {
        $this->rootData = [];
        parent::__construct($this, '', $this->rootData);
    }


    public function getFQN(): string
    {
        return '';
    }

    public function getPool(string $name): ?IValuePool
    {
        // todo: implement
        return null;
    }
}
