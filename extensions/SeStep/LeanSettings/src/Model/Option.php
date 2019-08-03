<?php declare(strict_types=1);

namespace SeStep\LeanSettings\Model;

use SeStep\GeneralSettings\Options\IOption;
use SeStep\GeneralSettings\Options\IValuePool;

/**
 * @property string $value m:column(string_value)
 * @property ValuePool|null $valuePool m:hasOne
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
        return !!$this->valuePool;
    }

    public function getValuePool(): ?IValuePool
    {
        // todo: implementValuePools
//        return $this->row->referenced('valuePool');

        return null;
    }
}
