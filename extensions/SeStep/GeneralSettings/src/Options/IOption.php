<?php

namespace SeStep\GeneralSettings\Options;


interface IOption extends INode
{
    const TYPE_STRING = 'string';
    const TYPE_BOOL = 'bool';
    const TYPE_INT = 'int';

    public function getValue();

    public function hasValuePool(): bool;

    public function getValuePool(): ?IValuePool;
}
