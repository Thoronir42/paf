<?php declare(strict_types=1);

if (!class_exists(Nette\Localization\Translator::class)) {
    class_alias(Nette\Localization\ITranslator::class, Nette\Localization\Translator::class);
}
