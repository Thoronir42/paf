<?php declare(strict_types=1);
namespace SeStep\GeneralSettings\Options;

interface IOptionSectionWritable
{
    /**
     * @param mixed $name
     * @return IOptionSection
     */
    public function addSection(string $name);

    public function setValue($value, string $name);

    public function addValue($value, string $section = '');

    public function removeNode(string $name);
}
