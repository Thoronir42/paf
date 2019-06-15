<?php

namespace SeStep\GeneralSettings\Exceptions;


class SectionNotFoundException extends NodeNotFoundException {
    public function __construct($sectionFQN)
    {
        parent::__construct("Section '$sectionFQN' was not found");
    }
}
