<?php


namespace SeStep\GeneralSettings\Exceptions;


class OptionNotFoundException extends NodeNotFoundException
{
    public function __construct($optionFQN)
    {
        parent::__construct("Option '$optionFQN' was not found");
    }
}