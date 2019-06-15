<?php

namespace SeStep\GeneralSettings;


interface IOptions extends Options\IOptionSection
{
    public function setValue($value, string $name, string $domain = '');
}
