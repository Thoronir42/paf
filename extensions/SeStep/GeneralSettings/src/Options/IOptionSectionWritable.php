<?php


namespace SeStep\GeneralSettings\Options;


interface IOptionSectionWritable
{
    /**
     * @param mixed $name
     * @return IOptionSection
     */
    public function addSection($name);

    public function setValue($value, string $name);

    public function addValue($value);
}