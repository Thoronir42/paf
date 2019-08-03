<?php declare(strict_types=1);
namespace SeStep\GeneralSettingsInMemory;

use SeStep\GeneralSettings\Options\IOption;
use SeStep\GeneralSettings\Options\IValuePool;

class InMemoryOption extends InMemoryNode implements IOption
{
    public function getValue()
    {
        return $this->data['value'];
    }

    public function setValue($value)
    {
        $this->data['value'] = $value;
    }

    public function hasValuePool(): bool
    {
        return isset($this->data['pool']);
    }

    public function getValuePool(): ?IValuePool
    {
        if (!$this->hasValuePool()) {
            return null;
        }

        return $this->getRoot()->getPool($this->data['pool']);
    }
}
