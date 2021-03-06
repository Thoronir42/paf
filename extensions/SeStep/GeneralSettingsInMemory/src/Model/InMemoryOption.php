<?php declare(strict_types=1);

namespace SeStep\GeneralSettingsInMemory\Model;

use SeStep\GeneralSettings\Model\IOption;
use SeStep\GeneralSettings\Model\IValuePool;

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

    public function setValuePool(?string $name)
    {
        if ($name) {
            $this->data['pool'] = $name;
        } else {
            unset($this->data['pool']);
        }
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
