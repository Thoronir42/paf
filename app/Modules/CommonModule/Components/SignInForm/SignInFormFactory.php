<?php


namespace PAF\Modules\CommonModule\Components\SignInForm;

interface SignInFormFactory
{
    /** @return SignInForm */
    public function create();
}
