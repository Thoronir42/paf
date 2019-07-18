<?php

namespace SeStep\GeneralSettings\Options;


interface IOption extends INode
{
    const TYPE_STRING = OptionTypeEnum::TYPE_STRING;
    const TYPE_BOOL = OptionTypeEnum::TYPE_BOOL;
    const TYPE_INT = OptionTypeEnum::TYPE_INT;

    public function getValue();

    public function hasValuePool(): bool;

    public function getValuePool(): ?IValuePool;
}
