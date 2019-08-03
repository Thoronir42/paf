<?php declare(strict_types=1);
namespace PAF\Modules\CommonModule\Components\SignInForm;

interface SignInFormFactory
{
    /** @return SignInForm */
    public function create();
}
