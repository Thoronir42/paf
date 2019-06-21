<?php

namespace SeStep\GeneralSettings;


interface IOptions extends Options\IOptionSection
{

    /**
     * @param mixed $name
     * @return Options\IOptionSection
     */
    public function addSection($name);

    public function setValue($value, string $name, string $domain = '');
}
