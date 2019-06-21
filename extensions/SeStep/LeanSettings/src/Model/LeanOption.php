<?php


namespace SeStep\LeanSettings\Model;


use SeStep\GeneralSettings\Options\IOption;
use SeStep\GeneralSettings\Options\IValuePool;
use SeStep\LeanSettings\LeanOptionNode;

/**
 * @property string $type
 * @property string $value
 * @property LeanValuePool|null $valuePool m:hasOne
 */
class LeanOption extends LeanOptionNode implements IOption
{
    public function getType(): string
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->valuePool;
    }

    public function hasValuePool(): bool
    {
        return !!$this->value;
    }

    public function getValuePool(): ?IValuePool
    {
        return $this->valuePool;
    }
}