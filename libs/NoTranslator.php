<?php


namespace Libs;


use Nette\Localization\ITranslator;

class NoTranslator implements ITranslator
{

    /**
     * Translates the given string.
     */
    function translate($message, ...$parameters): string
    {
        return $message;
    }

    public function domain($domain): ITranslator
    {
        return new NoTranslator();
    }
}