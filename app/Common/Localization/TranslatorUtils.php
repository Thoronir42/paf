<?php declare(strict_types=1);

namespace PAF\Common\Localization;

use Nette\Localization\ITranslator;

class TranslatorUtils
{
    public static function mapTranslate(array $values, ITranslator $translator)
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[$key] = $translator->translate($value);
        }

        return $result;
    }
}
