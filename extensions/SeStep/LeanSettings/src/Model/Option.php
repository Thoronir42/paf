<?php declare(strict_types=1);

namespace SeStep\LeanSettings\Model;

use SeStep\GeneralSettings\Model\IOption;
use SeStep\GeneralSettings\Model\IValuePool;

/**
 * @property string $value m:column(string_value)
 * @property Section|null $valuePool m:hasOne
 */
class Option extends OptionNode implements IOption
{
    public function getType(): string
    {
        return $this->row->type;
    }

    public function setValue($value)
    {
        if ($this->hasValuePool()) {
            $this->valuePool->validate($value);
        }

        // todo: distinguish value type
        $this->row->string_value = $value;
    }

    public function getValue()
    {
        return $this->row->string_value;
    }

    public function hasValuePool(): bool
    {
        return isset($this->getRowData()['valuePool']);
    }

    public function setValuePool(?SectionValuePool $pool): self
    {
        $this->valuePool = $pool->getSection();
        return $this;
    }
    
    public function getValuePool(): ?IValuePool
    {
        if (!$this->hasValuePool()) {
            return null;
        }

        return new SectionValuePool($this->valuePool);
    }
}
